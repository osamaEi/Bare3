<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Certificate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;

class CertificateController extends Controller
{
    public function index(Request $request): Response
    {
        $certificates = $request->user()->certificates()
            ->with('path')
            ->latest('issued_at')
            ->get()
            ->map(fn (Certificate $c) => [
                'id' => $c->id,
                'cert_number' => $c->cert_number,
                'path' => $c->path?->title,
                'issued_at' => $c->issued_at?->format('Y-m-d'),
                'has_pdf' => (bool) $c->pdf_path,
            ])
            ->toArray();

        return Inertia::render('Student/Certificates', [
            'certificates' => $certificates,
        ]);
    }

    public function download(Request $request, Certificate $certificate): StreamedResponse
    {
        abort_unless($certificate->student_id === $request->user()->id, 403);
        abort_unless($certificate->pdf_path && Storage::disk('public')->exists($certificate->pdf_path), 404);

        return Storage::disk('public')->download(
            $certificate->pdf_path,
            "certificate-{$certificate->cert_number}.pdf"
        );
    }

    public function verify(string $certNumber): Response
    {
        $certificate = Certificate::with(['student', 'path'])
            ->where('cert_number', $certNumber)
            ->first();

        return Inertia::render('Certificates/Verify', [
            'valid' => (bool) $certificate,
            'certificate' => $certificate ? [
                'cert_number' => $certificate->cert_number,
                'student' => $certificate->student->name,
                'path' => $certificate->path?->title,
                'issued_at' => $certificate->issued_at?->format('Y-m-d'),
            ] : null,
        ]);
    }
}
