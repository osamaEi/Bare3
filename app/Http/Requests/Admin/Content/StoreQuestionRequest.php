<?php

namespace App\Http\Requests\Admin\Content;

use Illuminate\Foundation\Http\FormRequest;

class StoreQuestionRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'text'      => ['required', 'string'],
            'options'   => ['required', 'array', 'min:2', 'max:4'],
            'options.*' => ['required', 'string', 'max:500'],
            'correct'   => ['required', 'integer', 'min:0'],
        ];
    }

    public function messages(): array
    {
        return [
            'text.required'    => 'نص السؤال مطلوب',
            'options.required' => 'خيارات الإجابة مطلوبة',
            'options.min'      => 'يجب إضافة خيارين على الأقل',
            'options.max'      => 'لا يمكن إضافة أكثر من 4 خيارات',
            'correct.required' => 'يجب تحديد الإجابة الصحيحة',
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($v) {
            $options = $this->input('options', []);
            $correct = $this->input('correct', -1);

            if ($correct >= count($options)) {
                $v->errors()->add('correct', 'رقم الإجابة الصحيحة يتجاوز عدد الخيارات المتاحة');
            }
        });
    }
}
