<?php

namespace App\Http\Requests\Admin\Content;

use Illuminate\Foundation\Http\FormRequest;

class StoreLessonRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'path_id'     => ['required', 'exists:paths,id'],
            'title'       => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'grade_level' => ['required', 'in:primary,middle,high,all'],
            'sort_order'  => ['integer', 'min:0'],
        ];
    }

    public function messages(): array
    {
        return [
            'path_id.required'     => 'يجب تحديد المسار',
            'path_id.exists'       => 'المسار المحدد غير موجود',
            'title.required'       => 'عنوان الدرس مطلوب',
            'grade_level.required' => 'يجب تحديد المرحلة الدراسية',
            'grade_level.in'       => 'المرحلة الدراسية غير صحيحة',
        ];
    }
}
