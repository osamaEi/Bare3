<?php

namespace App\Http\Requests\Admin\User;

use Illuminate\Foundation\Http\FormRequest;

class UpdateUserRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        $id = $this->route('id');

        return [
            'name'     => ['required', 'string', 'max:255'],
            'email'    => ['required', 'email', "unique:users,email,{$id}"],
            'password' => ['nullable', 'string', 'min:8'],
            'role'     => ['required', 'in:student,parent,teacher'],
            'phone'    => ['nullable', 'string', 'max:20'],
            'gender'   => ['nullable', 'in:male,female'],
            'is_active'=> ['boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required'  => 'اسم المستخدم مطلوب',
            'email.unique'   => 'البريد الإلكتروني مستخدم من قبل',
            'password.min'   => 'كلمة المرور يجب أن تكون 8 أحرف على الأقل',
            'role.in'        => 'نوع الحساب غير صحيح',
        ];
    }
}
