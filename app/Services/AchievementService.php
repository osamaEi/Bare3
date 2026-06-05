<?php

namespace App\Services;

use App\Models\Badge;
use App\Models\Certificate;
use App\Models\Enrollment;
use App\Models\StudentBadge;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class AchievementService
{
    /**
     * Award the path badge to a student if not already earned.
     * Returns the StudentBadge if newly awarded, otherwise null.
     */
    public function awardPathBadge(User $student, int $pathId): ?StudentBadge
    {
        $badge = Badge::where('path_id', $pathId)->where('type', 'path')->first();

        if (! $badge) {
            return null;
        }

        $exists = StudentBadge::where('student_id', $student->id)
            ->where('badge_id', $badge->id)
            ->exists();

        if ($exists) {
            return null;
        }

        return StudentBadge::create([
            'student_id' => $student->id,
            'badge_id' => $badge->id,
            'earned_at' => now(),
            'seen' => false,
        ]);
    }

    /**
     * Issue a certificate for a completed enrollment (idempotent).
     */
    public function issueCertificate(User $student, Enrollment $enrollment): Certificate
    {
        $existing = Certificate::where('enrollment_id', $enrollment->id)->first();
        if ($existing) {
            return $existing;
        }

        $certNumber = $this->generateCertNumber();

        $certificate = Certificate::create([
            'student_id' => $student->id,
            'path_id' => $enrollment->path_id,
            'enrollment_id' => $enrollment->id,
            'cert_number' => $certNumber,
            'issued_at' => now(),
        ]);

        $verifyUrl = route('certificates.verify', $certNumber);
        $qrSvg = base64_encode(QrCode::format('svg')->size(140)->generate($verifyUrl));
        $certificate->qr_code = $verifyUrl;

        $pdf = Pdf::loadView('certificates.template', [
            'studentName' => $student->name,
            'pathTitle' => $enrollment->path->title,
            'certNumber' => $certNumber,
            'issuedAt' => $certificate->issued_at,
            'qrSvg' => $qrSvg,
        ])->setPaper('a4', 'landscape');

        $path = "certificates/{$certNumber}.pdf";
        Storage::disk('public')->put($path, $pdf->output());

        $certificate->pdf_path = $path;
        $certificate->save();

        return $certificate;
    }

    private function generateCertNumber(): string
    {
        $year = now()->year;
        $count = Certificate::whereYear('issued_at', $year)->count() + 1;

        return sprintf('BARE3-%d-%05d', $year, $count);
    }
}
