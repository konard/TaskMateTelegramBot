<?php

declare(strict_types=1);

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\Contracts\NotificationServiceInterface;
use App\Services\Contracts\ValidationServiceInterface;
use App\Services\TelegramNotificationService;
use App\Services\ValidationService;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Bind interfaces to concrete implementations
        $this->app->bind(
            NotificationServiceInterface::class,
            TelegramNotificationService::class
        );

        $this->app->bind(
            ValidationServiceInterface::class,
            ValidationService::class
        );
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
