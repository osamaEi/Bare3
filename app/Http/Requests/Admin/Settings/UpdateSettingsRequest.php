<?php

namespace App\Http\Requests\Admin\Settings;

use Illuminate\Foundation\Http\FormRequest;

class UpdateSettingsRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'platform_name'       => ['required', 'string', 'max:100'],
            'platform_email'      => ['nullable', 'email'],
            'pass_mark_default'   => ['integer', 'min:50', 'max:100'],
            'video_threshold'     => ['integer', 'min:50', 'max:100'],
            'max_quiz_attempts'   => ['integer', 'min:1', 'max:10'],
            'notification_email'  => ['boolean'],
            'notification_in_app' => ['boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'platform_name.required'    => 'اسم المنصة مطلوب',
            'platform_email.email'      => 'البريد الإلكتروني غير صحيح',
            'pass_mark_default.min'     => 'نسبة النجاح الافتراضية يجب أن تكون 50٪ على الأقل',
            'video_threshold.min'       => 'نسبة مشاهدة الفيديو يجب أن تكون 50٪ على الأقل',
            'max_quiz_attempts.min'     => 'عدد المحاولات يجب أن يكون واحدة على الأقل',
        ];
    }
}
