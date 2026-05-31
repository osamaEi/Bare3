<?php

namespace App\Http\Requests\Admin\Content;

use Illuminate\Foundation\Http\FormRequest;

class StoreVideoRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'lesson_id'        => ['required', 'exists:lessons,id'],
            'title'            => ['required', 'string', 'max:255'],
            'video_file'       => ['nullable', 'file', 'mimes:mp4,mkv,avi,mov', 'max:2097152'], // 2GB
            'url'              => ['nullable', 'url', 'required_without:video_file'],
            'duration_seconds' => ['nullable', 'integer', 'min:1'],
            'watch_threshold'  => ['integer', 'min:50', 'max:100'],
            'thumbnail'        => ['nullable', 'image', 'max:1024'],
        ];
    }

    public function messages(): array
    {
        return [
            'lesson_id.required'       => 'يجب تحديد الدرس',
            'lesson_id.exists'         => 'الدرس المحدد غير موجود',
            'title.required'           => 'عنوان الفيديو مطلوب',
            'video_file.mimes'         => 'صيغة الفيديو يجب أن تكون MP4 أو MKV أو AVI أو MOV',
            'video_file.max'           => 'حجم الفيديو يجب ألا يتجاوز 2 جيجابايت',
            'url.required_without'     => 'يجب رفع ملف فيديو أو إدخال رابط الفيديو',
            'url.url'                  => 'رابط الفيديو غير صحيح',
            'watch_threshold.min'      => 'نسبة المشاهدة المطلوبة يجب أن تكون 50٪ على الأقل',
            'watch_threshold.max'      => 'نسبة المشاهدة المطلوبة يجب ألا تتجاوز 100٪',
        ];
    }
}
