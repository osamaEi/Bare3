<?php

namespace App\Http\Requests\Admin\Content;

use Illuminate\Foundation\Http\FormRequest;

class UpdateLessonRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'title'       => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'grade_level' => ['required', 'in:primary,middle,high,all'],
            'sort_order'  => ['integer', 'min:0'],
            'is_active'   => ['boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'title.required'       => 'عنوان الدرس مطلوب',
            'grade_level.required' => 'يجب تحديد المرحلة الدراسية',
            'grade_level.in'       => 'المرحلة الدراسية غير صحيحة',
        ];
    }
}
