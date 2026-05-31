<?php

namespace App\Http\Requests\Admin\Content;

use Illuminate\Foundation\Http\FormRequest;

class StoreScormRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'lesson_id'   => ['required', 'exists:lessons,id'],
            'title'       => ['required', 'string', 'max:255'],
            'version'     => ['required', 'in:1.2,2004'],
            'scorm_file'  => ['required', 'file', 'mimes:zip', 'max:524288'], // 512MB
            'entry_point' => ['nullable', 'string', 'max:255'],
        ];
    }

    public function messages(): array
    {
        return [
            'lesson_id.required'  => 'يجب تحديد الدرس',
            'lesson_id.exists'    => 'الدرس المحدد غير موجود',
            'title.required'      => 'عنوان النشاط مطلوب',
            'version.required'    => 'يجب تحديد إصدار SCORM',
            'version.in'          => 'إصدار SCORM يجب أن يكون 1.2 أو 2004',
            'scorm_file.required' => 'ملف SCORM مطلوب',
            'scorm_file.mimes'    => 'ملف SCORM يجب أن يكون بصيغة ZIP',
            'scorm_file.max'      => 'حجم ملف SCORM يجب ألا يتجاوز 512 ميجابايت',
        ];
    }
}
