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
        // Si el archivo index.php está en la raíz, significa que estamos en el hosting
        // y la raíz es la carpeta pública. Forzamos la ruta pública a base_path().
        if (file_exists(base_path('index.php'))) {
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
