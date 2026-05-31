<?php

namespace App\Http\Requests\Admin\Blog;

use Illuminate\Foundation\Http\FormRequest;

class StorePostRequest extends FormRequest
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
            'tags.*'          => ['integer', 'exists:blog_tags,id'],
        ];
    }

    public function messages(): array
    {
        return [
            'title.required'       => 'عنوان المقالة مطلوب',
            'category_id.required' => 'يجب اختيار قسم للمقالة',
            'content.required'     => 'محتوى المقالة مطلوب',
            'status.in'            => 'حالة المقالة يجب أن تكون مسودة أو منشورة',
            'featured_image.image' => 'الملف المرفق يجب أن يكون صورة',
            'featured_image.max'   => 'حجم الصورة يجب ألا يتجاوز 2 ميجابايت',
            'seo_title.max'        => 'عنوان SEO يجب ألا يتجاوز 60 حرفاً',
            'seo_description.max'  => 'وصف SEO يجب ألا يتجاوز 160 حرفاً',
        ];
    }
}
