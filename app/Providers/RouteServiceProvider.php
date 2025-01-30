<?php

namespace App\Providers;

use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use App\Http\Middleware\CheckRole; // 👈 Asegúrate de importar el middleware

class RouteServiceProvider extends ServiceProvider
{
    /**
     * Definir los bindings de rutas, middlewares y configuraciones.
     */
    public function boot()
    {
        parent::boot(); // 👈 Mantén esta línea si ya existe

        // Registrar el middleware de roles
        Route::middlewareGroup('role', [
            CheckRole::class,
        ]);
    }
}
