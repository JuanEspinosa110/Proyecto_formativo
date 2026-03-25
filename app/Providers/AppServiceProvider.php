<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Events\MigrationsStarted;
use Illuminate\Database\Events\MigrationsEnded;
use Illuminate\Support\Facades\Event;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Paginator::useBootstrapFive();

        // Deshabilitar FK checks durante migraciones (evita errores en migrate:refresh con FKs cruzadas)
        Event::listen(MigrationsStarted::class, function () {
            Schema::disableForeignKeyConstraints();
        });

        Event::listen(MigrationsEnded::class, function () {
            Schema::enableForeignKeyConstraints();
        });
    }
}
