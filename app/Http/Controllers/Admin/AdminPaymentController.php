<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Repositories\Contracts\PaymentRepositoryInterface;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class AdminPaymentController extends Controller
{
    public function __construct(
        private readonly PaymentRepositoryInterface $payments,
    ) {}

    public function index(Request $request): Response
    {
        $filters = $request->only(['gateway', 'status', 'search', 'per_page']);

        return Inertia::render('Admin/Payments', [
            'transactions'  => $this->payments->allTransactions($filters),
            'revenue_month' => $this->payments->totalRevenue('month'),
            'revenue_year'  => $this->payments->totalRevenue('year'),
            'revenue_total' => $this->payments->totalRevenue(),
            'gateway_split' => $this->payments->revenueByGateway(),
            'monthly_chart' => $this->payments->revenueByMonth(6),
            'plan_split'    => $this->payments->revenueByPlan(),
            'counts'        => [
                'success'  => $this->payments->countByStatus('success'),
                'refunded' => $this->payments->countByStatus('refunded'),
                'failed'   => $this->payments->countByStatus('failed'),
                'pending'  => $this->payments->countByStatus('pending'),
            ],
            'filters' => $filters,
        ]);
    }

    public function show(int $id): Response
    {
        return Inertia::render('Admin/PaymentDetail', [
            'transaction' => $this->payments->findTransaction($id),
        ]);
    }

    public function refund(int $id): RedirectResponse
    {
        $this->payments->refund($id);

        return back()->with('success', 'تم استرداد المبلغ بنجاح');
    }
}
