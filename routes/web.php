<?php

use App\Enums\UiComponent;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\ComponentController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\LetterController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('dashboard');
})->name('home');

Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'create'])->name('login');
    Route::post('/login', [LoginController::class, 'store'])->name('login.store');
});

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', DashboardController::class)->name('dashboard');
    Route::inertia('/blank', 'Blank')->name('blank');
    Route::post('/letters', [LetterController::class, 'store'])->name('letters.store');
    Route::post('/logout', [LoginController::class, 'destroy'])->name('logout');

    /*
     * reka-ui component documentation: /components and one page per component,
     * e.g. /components/accordion (route "components.accordion").
     */
    Route::prefix('components')->name('components.')->group(function () {
        Route::get('/', [ComponentController::class, 'index'])->name('index');

        foreach (UiComponent::cases() as $component) {
            Route::get($component->value, [ComponentController::class, 'show'])
                ->defaults('component', $component->value)
                ->name($component->value);
        }
    });
});
