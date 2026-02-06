<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;

Route::get('/', function () {
    return view('index');
})->name('home');


Route::get('/superadmin/dashboard', function () {
    return view('admin.dashboard');
});

Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login'])->name('login.post');
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

Route::view('/register', 'auth.register')->name('register');

Route::middleware('auth:web')->group(function () {

    Route::get('/pasajero/dashboard', fn () => view('pasajeros.index'))
        ->name('pasajero.dashboard');

    Route::get('/empresa/dashboard', fn () => view('empresa.dashboard'))
        ->name('empresa.dashboard');
});

Route::middleware('auth:superadmin')->group(function () {

    Route::get('/superadmin/dashboard', fn () => view('superadmin.dashboard'))
        ->name('superadmin.dashboard');
});

