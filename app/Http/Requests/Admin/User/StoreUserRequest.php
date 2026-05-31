<?php

namespace App\Http\Requests\Admin\User;

use Illuminate\Foundation\Http\FormRequest;

class StoreUserRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'name'     => ['required', 'string', 'max:255'],
            'email'    => ['required', 'email', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8'],
            'role'     => ['required', 'in:student,parent,teacher'],
            'phone'    => ['nullable', 'string', 'max:20'],
            'gender'   => ['nullable', 'in:male,female'],
            'level'    => ['nullable', 'required_if:role,student', 'in:primary,middle,high'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required'     => 'اسم المستخدم مطلوب',
            'email.required'    => 'البريد الإلكتروني مطلوب',
            'email.unique'      => 'البريد الإلكتروني مستخدم من قبل',
            'password.required' => 'كلمة المرور مطلوبة',
            'password.min'      => 'كلمة المرور يجب أن تكون 8 أحرف على الأقل',
            'role.required'     => 'يجب تحديد نوع الحساب',
            'role.in'           => 'نوع الحساب غير صحيح',
            'level.required_if' => 'يجب تحديد المرحلة الدراسية للطالب',
        ];
    }
}
