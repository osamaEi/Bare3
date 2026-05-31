<?php

namespace App\Http\Requests\Admin\Content;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePathRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'title'       => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'icon'        => ['nullable', 'string', 'max:100'],
            'color'       => ['nullable', 'string', 'max:10', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'cover_image' => ['nullable', 'image', 'max:2048'],
            'sort_order'  => ['integer', 'min:0'],
            'is_active'   => ['boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'title.required' => 'اسم المسار مطلوب',
            'color.regex'    => 'اللون يجب أن يكون بصيغة HEX صحيحة مثل #38BDF8',
        ];
    }
}
