<?php

use App\Enums\UiComponent;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\ComponentController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DocumentTemplateController;
use App\Http\Controllers\LetterController;
use App\Http\Controllers\PendudukController;
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
    Route::inertia('/editor', 'Editor')->name('editor');
    Route::post('/letters', [LetterController::class, 'store'])->name('letters.store');
    Route::post('/logout', [LoginController::class, 'destroy'])->name('logout');

    Route::get('/letters-templates', [LetterController::class, 'storeTemplate'])->name('letters-templates.index');

    /*
     * Word templates with ${variable} placeholders: upload, fill in, download.
     */
    Route::prefix('document-templates')->name('document-templates.')->group(function () {
        Route::get('/', [DocumentTemplateController::class, 'index'])->name('index');
        Route::get('/create', [DocumentTemplateController::class, 'create'])->name('create');
        Route::post('/', [DocumentTemplateController::class, 'store'])->name('store');
        Route::get('/sample', [DocumentTemplateController::class, 'sample'])->name('sample');
        Route::get('/{documentTemplate}', [DocumentTemplateController::class, 'show'])->name('show');
        Route::get('/{documentTemplate}/download', [DocumentTemplateController::class, 'download'])->name('download');
        Route::post('/{documentTemplate}/generate', [DocumentTemplateController::class, 'generate'])->name('generate');
        Route::post('/{documentTemplate}/print', [DocumentTemplateController::class, 'printable'])->name('print');
        Route::patch('/{documentTemplate}', [DocumentTemplateController::class, 'update'])->name('update');
        Route::delete('/{documentTemplate}', [DocumentTemplateController::class, 'destroy'])->name('destroy');
    });

    Route::get('/penduduk/search', [PendudukController::class, 'search'])->name('penduduk.search');
    Route::post('/penduduk/import', [PendudukController::class, 'import'])->name('penduduk.import');
    Route::get('/penduduk/import/template', [PendudukController::class, 'importTemplate'])->name('penduduk.import-template');
    Route::resource('penduduk', PendudukController::class)->except(['show']);

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
