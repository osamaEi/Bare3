<?php

namespace App\Http\Requests\Admin\Blog;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePostRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'title'           => ['required', 'string', 'max:255'],
            'category_id'     => ['required', 'exists:blog_categories,id'],
            'excerpt'         => ['nullable', 'string', 'max:500'],
            'content'         => ['required', 'string'],
            'featured_image'  => ['nullable', 'image', 'max:2048'],
            'seo_title'       => ['nullable', 'string', 'max:60'],
            'seo_description' => ['nullable', 'string', 'max:160'],
            'status'          => ['required', 'in:draft,published'],
            'tags'            => ['nullable', 'array'],
            'tags.*'          => ['string', 'max:50'],
        ];
    }

    public function messages(): array
    {
        return [
            'title.required'    => 'عنوان المقالة مطلوب',
            'content.required'  => 'محتوى المقالة مطلوب',
        ];
    }
}
