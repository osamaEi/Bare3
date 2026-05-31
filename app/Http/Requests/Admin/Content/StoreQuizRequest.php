<?php

namespace App\Http\Requests\Admin\Content;

use Illuminate\Foundation\Http\FormRequest;

class StoreQuizRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'lesson_id'          => ['required', 'exists:lessons,id'],
            'title'              => ['required', 'string', 'max:255'],
            'pass_mark'          => ['integer', 'min:50', 'max:100'],
            'max_attempts'       => ['integer', 'min:1', 'max:10'],
            'time_limit_minutes' => ['nullable', 'integer', 'min:5', 'max:180'],

            'questions'            => ['nullable', 'array'],
            'questions.*.text'     => ['required_with:questions', 'string'],
            'questions.*.options'  => ['required_with:questions', 'array', 'min:2', 'max:4'],
            'questions.*.options.*'=> ['required', 'string'],
            'questions.*.correct'  => ['required_with:questions', 'integer', 'min:0'],
        ];
    }

    public function messages(): array
    {
        return [
            'lesson_id.required'          => 'يجب تحديد الدرس',
            'title.required'              => 'عنوان الاختبار مطلوب',
            'pass_mark.min'               => 'نسبة النجاح يجب أن تكون 50٪ على الأقل',
            'pass_mark.max'               => 'نسبة النجاح يجب ألا تتجاوز 100٪',
            'max_attempts.min'            => 'عدد المحاولات يجب أن يكون واحدة على الأقل',
            'questions.*.text.required_with'    => 'نص السؤال مطلوب',
            'questions.*.options.min'           => 'كل سؤال يجب أن يحتوي على خيارين على الأقل',
            'questions.*.options.max'           => 'كل سؤال يجب ألا يتجاوز 4 خيارات',
        ];
    }
}
