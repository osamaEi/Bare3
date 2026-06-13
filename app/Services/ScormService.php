<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use ZipArchive;

/**
 * Handles unpacking a SCORM .zip package and reading its manifest
 * to discover the launch page (entry point).
 *
 * كل حزمة SCORM هي ملف مضغوط فيه صفحات HTML + ملف فهرس اسمه
 * imsmanifest.xml يحدّد من أين تبدأ الحزمة. هذه الخدمة تفكّ الضغط
 * وتقرأ هذا الفهرس.
 */
class ScormService
{
    /**
     * Unzip the uploaded package into storage/app/public/scorm/{folder}
     * and return [folder, entry_point, size_kb].
     */
    public function unpack(UploadedFile $file): array
    {
        $folder = 'scorm/'.Str::uuid()->toString();
        $absDir = Storage::disk('public')->path($folder);

        if (! is_dir($absDir)) {
            mkdir($absDir, 0775, true);
        }

        $zip = new ZipArchive;
        if ($zip->open($file->getRealPath()) !== true) {
            throw new \RuntimeException('تعذّر فتح ملف SCORM المضغوط');
        }
        $zip->extractTo($absDir);
        $zip->close();

        return [
            'package_path' => $folder,
            'entry_point' => $this->findEntryPoint($absDir),
            'file_size_kb' => (int) ($file->getSize() / 1024),
        ];
    }

    /**
     * Read imsmanifest.xml to find the launch page; fall back to index.html.
     */
    private function findEntryPoint(string $dir): string
    {
        $manifest = $dir.DIRECTORY_SEPARATOR.'imsmanifest.xml';

        if (is_file($manifest)) {
            $xml = @simplexml_load_file($manifest);
            if ($xml !== false) {
                // The launch file is the <resource href="..."> of the default resource
                foreach ($xml->resources->resource ?? [] as $resource) {
                    $href = (string) ($resource['href'] ?? '');
                    if ($href !== '') {
                        return $href;
                    }
                }
            }
        }

        // Fallbacks commonly used by authoring tools
        foreach (['index.html', 'index_lms.html', 'story.html', 'launch.html'] as $candidate) {
            if (is_file($dir.DIRECTORY_SEPARATOR.$candidate)) {
                return $candidate;
            }
        }

        return 'index.html';
    }
}
