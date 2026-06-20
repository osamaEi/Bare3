<?php

namespace App\Services;

use App\Models\Setting;
use Illuminate\Support\Facades\Http;

/**
 * Thin wrapper around the PayTabs hosted payment page API.
 * Docs: https://support.paytabs.com/en/support/solutions/articles/60000711358
 */
class PayTabsService
{
    private string $baseUrl;
    private ?string $profileId;
    private ?string $serverKey;

    public function __construct()
    {
        // Settings (admin-managed via DB) take precedence over .env defaults.
        $this->profileId = Setting::get('paytabs_profile_id') ?: config('services.paytabs.profile_id');
        $this->serverKey = Setting::get('paytabs_server_key') ?: config('services.paytabs.server_key');
        $base = Setting::get('paytabs_base_url') ?: config('services.paytabs.base_url');
        $this->baseUrl = rtrim($base, '/');
    }

    public function currency(): string
    {
        return Setting::get('paytabs_currency') ?: config('services.paytabs.currency', 'SAR');
    }

    public function isConfigured(): bool
    {
        return filled($this->profileId) && filled($this->serverKey);
    }

    /**
     * Create a hosted payment page and return the redirect URL + tran_ref.
     *
     * @return array{ok:bool, redirect_url:?string, tran_ref:?string, message:?string}
     */
    public function createHostedPage(array $args): array
    {
        if (! $this->isConfigured()) {
            return ['ok' => false, 'redirect_url' => null, 'tran_ref' => null, 'message' => 'PayTabs غير مُهيّأ — أضف المفاتيح في .env'];
        }

        $payload = [
            'profile_id'        => (int) $this->profileId,
            'tran_type'         => 'sale',
            'tran_class'        => 'ecom',
            'cart_id'           => $args['cart_id'],
            'cart_description'  => $args['description'],
            'cart_currency'     => $args['currency'] ?? config('services.paytabs.currency', 'SAR'),
            'cart_amount'       => (float) $args['amount'],
            'callback'          => $args['callback'],   // server-to-server IPN
            'return'            => $args['return'],     // browser redirect back
            'customer_details'  => [
                'name'  => $args['customer']['name'] ?? 'Customer',
                'email' => $args['customer']['email'] ?? 'customer@example.com',
            ],
        ];

        $res = Http::withHeaders([
            'authorization' => $this->serverKey,
            'content-type'  => 'application/json',
        ])->post($this->baseUrl.'/payment/request', $payload);

        if (! $res->successful()) {
            return ['ok' => false, 'redirect_url' => null, 'tran_ref' => null, 'message' => $res->json('message') ?? 'تعذّر إنشاء صفحة الدفع'];
        }

        return [
            'ok'           => true,
            'redirect_url' => $res->json('redirect_url'),
            'tran_ref'     => $res->json('tran_ref'),
            'message'      => null,
        ];
    }

    /**
     * Verify a transaction by its reference (used in callback/return).
     *
     * @return array{ok:bool, paid:bool, raw:array}
     */
    public function verify(string $tranRef): array
    {
        if (! $this->isConfigured()) {
            return ['ok' => false, 'paid' => false, 'raw' => []];
        }

        $res = Http::withHeaders([
            'authorization' => $this->serverKey,
            'content-type'  => 'application/json',
        ])->post($this->baseUrl.'/payment/query', [
            'profile_id' => (int) $this->profileId,
            'tran_ref'   => $tranRef,
        ]);

        $raw = $res->json() ?? [];
        $status = strtoupper($raw['payment_result']['response_status'] ?? '');

        return [
            'ok'   => $res->successful(),
            'paid' => $status === 'A', // A = Authorised/Approved
            'raw'  => $raw,
        ];
    }
}
