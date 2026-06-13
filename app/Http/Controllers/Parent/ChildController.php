<?php

namespace App\Http\Controllers\Parent;

use App\Http\Controllers\Controller;
use App\Repositories\Contracts\ParentRepositoryInterface;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ChildController extends Controller
{
    public function __construct(
        private readonly ParentRepositoryInterface $parents,
    ) {}

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8',
            'gender' => 'nullable|in:male,female',
        ], [
            'name.required' => 'اسم الطفل مطلوب',
            'email.required' => 'البريد الإلكتروني مطلوب',
            'email.unique' => 'البريد الإلكتروني مستخدم من قبل',
            'password.required' => 'كلمة المرور مطلوبة',
            'password.min' => 'كلمة المرور يجب أن تكون 8 أحرف على الأقل',
        ]);

        $this->parents->addChild($request->user(), $data);

        return back()->with('success', 'تمت إضافة الطفل بنجاح');
    }

    public function report(Request $request, int $child): Response
    {
        return Inertia::render('Parent/ChildReport', [
            'report' => $this->parents->childReport($request->user(), $child),
        ]);
    }
}
