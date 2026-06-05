<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\User\StoreUserRequest;
use App\Http\Requests\Admin\User\UpdateUserRequest;
use App\Repositories\Contracts\UserRepositoryInterface;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class AdminUserController extends Controller
{
    public function __construct(
        private readonly UserRepositoryInterface $users,
    ) {}

    public function index(Request $request): Response
    {
        $filters = $request->only(['search', 'status', 'per_page']);

        return Inertia::render('Admin/Users', [
            'students' => $this->users->allByRole('student', $filters),
            'parents' => $this->users->allByRole('parent', $filters),
            'teachers' => $this->users->allByRole('teacher', $filters),
            'counts' => [
                'students' => $this->users->countByRole('student'),
                'parents' => $this->users->countByRole('parent'),
                'teachers' => $this->users->countByRole('teacher'),
                'active' => $this->users->countActive(),
            ],
            'filters' => $filters,
        ]);
    }

    public function store(StoreUserRequest $request): RedirectResponse
    {
        $this->users->create($request->validated());

        return back()->with('success', 'تم إضافة المستخدم بنجاح');
    }

    public function update(UpdateUserRequest $request, int $id): RedirectResponse
    {
        $data = array_filter($request->validated(), fn ($v) => $v !== null);
        $this->users->update($id, $data);

        return back()->with('success', 'تم تعديل المستخدم بنجاح');
    }

    public function destroy(int $id): RedirectResponse
    {
        $this->users->delete($id);

        return back()->with('success', 'تم حذف المستخدم');
    }

    public function toggleStatus(int $id): RedirectResponse
    {
        $this->users->toggleStatus($id);

        return back()->with('success', 'تم تغيير حالة المستخدم');
    }
}
