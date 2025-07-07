<?php

use App\Http\Controllers\Backend\UserController;
use App\Http\Controllers\Frontend\AboutMeController;
use App\Http\Controllers\Frontend\PortfolioController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

// Public Frontend Routes
Route::get('/', [PortfolioController::class, 'home'])->name('home');
Route::get('/about', [PortfolioController::class, 'about'])->name('about');
Route::get('/portfolio', [PortfolioController::class, 'portfolio'])->name('portfolio');
Route::get('/contact', [PortfolioController::class, 'contact'])->name('contact');
Route::get('/hobby', [PortfolioController::class, 'hobby'])->name('hobby');
Route::get('/more', [PortfolioController::class, 'more'])->name('more');

// Dashboard Route
Route::get('dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

// Authenticated Routes
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('about-me', [AboutMeController::class, 'index'])->name('about-me');
    Route::get('me', [UserController::class, 'index'])->name('backend.users.index');
    Route::get('backend/users/create', [UserController::class, 'create'])->name('backend.users.create');
    Route::post('backend/users', [UserController::class, 'store'])->name('backend.users.store');
    Route::get('backend/users/{user}', [UserController::class, 'show'])->name('backend.users.show');
    Route::get('backend/users/{user}/edit', [UserController::class, 'edit'])->name('backend.users.edit');
    Route::put('backend/users/{user}', [UserController::class, 'update'])->name('backend.users.update');
    Route::get('backend/users/{user}/delete', [UserController::class, 'delete'])->name('backend.users.delete');
    Route::delete('backend/users/{user}', [UserController::class, 'destroy'])->name('backend.users.destroy');
});

require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
