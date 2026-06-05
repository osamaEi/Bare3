<?php

namespace App\Http\Controllers\Parent;

use App\Http\Controllers\Controller;
use Inertia\Inertia;
use Inertia\Response;

class ParentDashboardController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('Parent/Dashboard');
    }
}
