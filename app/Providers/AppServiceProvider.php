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
        // Redefinir la ruta pública para que apunte a la raíz en el hosting
        // Esto permite que asset() y public_path() funcionen correctamente sin la carpeta /public
        if (config('app.env') === 'production' || str_contains(request()->getHost(), 'myjob.solutions')) {
            $this->app->bind('path.public', function() {
                return base_path();
            });
        }
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
