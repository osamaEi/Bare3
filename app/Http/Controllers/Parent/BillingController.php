<?php

namespace App\Http\Controllers\Parent;

use App\Http\Controllers\Controller;
use App\Repositories\Contracts\ParentRepositoryInterface;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class BillingController extends Controller
{
    public function __construct(
        private readonly ParentRepositoryInterface $parents,
    ) {}

    public function index(Request $request): Response
    {
        return Inertia::render('Parent/Billing', [
            'billing' => $this->parents->billing($request->user()),
        ]);
    }
}
