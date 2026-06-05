<?php

namespace App\Providers;

use App\Repositories\BlogRepository;
use App\Repositories\ContentRepository;
use App\Repositories\Contracts\BlogRepositoryInterface;
use App\Repositories\Contracts\ContentRepositoryInterface;
use App\Repositories\Contracts\DashboardRepositoryInterface;
use App\Repositories\Contracts\PaymentRepositoryInterface;
use App\Repositories\Contracts\StudentRepositoryInterface;
use App\Repositories\Contracts\UserRepositoryInterface;
use App\Repositories\DashboardRepository;
use App\Repositories\PaymentRepository;
use App\Repositories\StudentRepository;
use App\Repositories\UserRepository;
use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(DashboardRepositoryInterface::class, DashboardRepository::class);
        $this->app->bind(UserRepositoryInterface::class,      UserRepository::class);
        $this->app->bind(PaymentRepositoryInterface::class,   PaymentRepository::class);
        $this->app->bind(ContentRepositoryInterface::class,   ContentRepository::class);
        $this->app->bind(BlogRepositoryInterface::class,      BlogRepository::class);
        $this->app->bind(StudentRepositoryInterface::class,   StudentRepository::class);
    }
}
