<?php

namespace App\Http\Requests\Admin\Content;

use Illuminate\Foundation\Http\FormRequest;

class StorePathRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'title'       => ['required', 'string', 'max:255'],
            'slug'        => ['required', 'string', 'unique:paths,slug', 'regex:/^[a-z0-9-]+$/'],
            'description' => ['nullable', 'string'],
            'icon'        => ['nullable', 'string', 'max:100'],
            'color'       => ['nullable', 'string', 'max:10', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'cover_image' => ['nullable', 'image', 'max:2048'],
            'sort_order'  => ['integer', 'min:0'],
        ];
    }

    public function messages(): array
    {
        return [
            'title.required' => 'اسم المسار مطلوب',
            'slug.required'  => 'المعرف الفريد للمسار مطلوب',
            'slug.unique'    => 'هذا المعرف مستخدم من قبل',
            'slug.regex'     => 'المعرف يجب أن يحتوي على أحرف إنجليزية صغيرة وأرقام وشرطات فقط',
            'color.regex'    => 'اللون يجب أن يكون بصيغة HEX صحيحة مثل #38BDF8',
        ];
    }
}
