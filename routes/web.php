<?php

use Inertia\Inertia;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Backend\UserController;
use App\Http\Controllers\Backend\AboutMeController;
use App\Http\Controllers\Backend\EducationController;
use App\Http\Controllers\Backend\TechStackController;
use App\Http\Controllers\Backend\ProjectTypeController;
use App\Http\Controllers\Backend\ProjectController;
use App\Http\Controllers\Frontend\PortfolioController;
use App\Http\Controllers\Backend\WorkExperienceController;
use App\Http\Controllers\Backend\PopularSongController;

// Public Frontend Routes
Route::get('/', [PortfolioController::class, 'home'])->name('home');
Route::get('/about', [PortfolioController::class, 'about'])->name('about');
Route::get('/portfolio', [PortfolioController::class, 'portfolio'])->name('portfolio');
Route::get('/contact', [PortfolioController::class, 'contact'])->name('contact');
Route::get('/hobby', [PortfolioController::class, 'hobby'])->name('hobby');
Route::get('/more', [PortfolioController::class, 'more'])->name('more');
Route::get('/resume', [PortfolioController::class, 'resume'])->name('resume');

// API Routes for Frontend
Route::get('/api/popular-songs', [PopularSongController::class, 'getForPlayer'])->name('api.popular-songs');

// Dashboard Route
Route::get('dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

// Authenticated Routes
Route::middleware(['auth', 'verified'])->group(function () {
    // Me Route
    Route::get('me', [UserController::class, 'index'])->name('backend.users.index');
    Route::get('backend/users/create', [UserController::class, 'create'])->name('backend.users.create');
    Route::post('backend/users', [UserController::class, 'store'])->name('backend.users.store');
    Route::get('backend/users/{user}', [UserController::class, 'show'])->name('backend.users.show');
    Route::get('backend/users/{user}/edit', [UserController::class, 'edit'])->name('backend.users.edit');
    Route::put('backend/users/{user}', [UserController::class, 'update'])->name('backend.users.update');
    Route::get('backend/users/{user}/delete', [UserController::class, 'delete'])->name('backend.users.delete');
    Route::delete('backend/users/{user}', [UserController::class, 'destroy'])->name('backend.users.destroy');

    // About Me Routes
    Route::get('about-me', [AboutMeController::class, 'index'])->name('about-me');
    Route::get('backend/about-me/create', [AboutMeController::class, 'create'])->name('backend.about-me.create');
    Route::post('backend/about-me', [AboutMeController::class, 'store'])->name('backend.about-me.store');
    Route::get('backend/about-me/{aboutMe}/edit', [AboutMeController::class, 'edit'])->name('backend.about-me.edit');
    Route::put('backend/about-me/{aboutMe}', [AboutMeController::class, 'update'])->name('backend.about-me.update');
    Route::delete('backend/about-me/{aboutMe}', [AboutMeController::class, 'destroy'])->name('backend.about-me.destroy');

    //Work Experience Routes
    Route::get('work-experience', [WorkExperienceController::class, 'index'])->name('work-experience');
    Route::get('backend/work-experience/create', [WorkExperienceController::class, 'create'])->name('backend.work-experience.create');
    Route::post('backend/work-experience', [WorkExperienceController::class, 'store'])->name('backend.work-experience.store');
    Route::get('backend/work-experience/{workExperience}/edit', [WorkExperienceController::class, 'edit'])->name('backend.work-experience.edit');
    Route::put('backend/work-experience/{workExperience}', [WorkExperienceController::class, 'update'])->name('backend.work-experience.update');
    Route::delete('backend/work-experience/{workExperience}', [WorkExperienceController::class, 'destroy'])->name('backend.work-experience.destroy');

    //Education Routes
    Route::get('eductions', [EducationController::class, 'index'])->name('eductions');
    Route::get('backend/eductions/create', [EducationController::class, 'create'])->name('backend.eductions.create');
    Route::post('backend/eductions', [EducationController::class, 'store'])->name('backend.eductions.store');
    Route::get('backend/eductions/{education}/edit', [EducationController::class, 'edit'])->name('backend.eductions.edit');
    Route::put('backend/eductions/{education}', [EducationController::class, 'update'])->name('backend.eductions.update');
    Route::delete('backend/eductions/{education}', [EducationController::class, 'destroy'])->name('backend.eductions.destroy');

    //TechStack Routes
    Route::get('tech-stacks', [TechStackController::class, 'index'])->name('tech-stacks');
    Route::get('backend/tech-stacks/create', [TechStackController::class, 'create'])->name('backend.tech-stacks.create');
    Route::post('backend/tech-stacks', [TechStackController::class, 'store'])->name('backend.tech-stacks.store');
    Route::get('backend/tech-stacks/{techStack}/edit', [TechStackController::class, 'edit'])->name('backend.tech-stacks.edit');
    Route::put('backend/tech-stacks/{techStack}', [TechStackController::class, 'update'])->name('backend.tech-stacks.update');
    Route::delete('backend/tech-stacks/{techStack}', [TechStackController::class, 'destroy'])->name('backend.tech-stacks.destroy');

    //Project Types Routes
    Route::get('project-types', [ProjectTypeController::class, 'index'])->name('project-types');
    Route::get('backend/project-types/create', [ProjectTypeController::class, 'create'])->name('backend.project-types.create');
    Route::post('backend/project-types', [ProjectTypeController::class, 'store'])->name('backend.project-types.store');
    Route::get('backend/project-types/{projectType}/edit', [ProjectTypeController::class, 'edit'])->name('backend.project-types.edit');
    Route::put('backend/project-types/{projectType}', [ProjectTypeController::class, 'update'])->name('backend.project-types.update');
    Route::delete('backend/project-types/{projectType}', [ProjectTypeController::class, 'destroy'])->name('backend.project-types.destroy');

    //Projects Routes
    Route::get('projects', [ProjectController::class, 'index'])->name('projects');
    Route::get('backend/projects/create', [ProjectController::class, 'create'])->name('backend.projects.create');
    Route::post('backend/projects', [ProjectController::class, 'store'])->name('backend.projects.store');
    Route::get('backend/projects/{project}/edit', [ProjectController::class, 'edit'])->name('backend.projects.edit');
    Route::match(['PUT', 'POST'], 'backend/projects/{project}', [ProjectController::class, 'update'])->name('backend.projects.update');
    Route::delete('backend/projects/{project}', [ProjectController::class, 'destroy'])->name('backend.projects.destroy');

    //Popular Songs Routes
    Route::get('popular-songs', [PopularSongController::class, 'index'])->name('popular-songs');
    Route::get('backend/popular-songs/create', [PopularSongController::class, 'create'])->name('backend.popular-songs.create');
    Route::post('backend/popular-songs', [PopularSongController::class, 'store'])->name('backend.popular-songs.store');
    Route::get('backend/popular-songs/{popularSong}', [PopularSongController::class, 'show'])->name('backend.popular-songs.show');
    Route::get('backend/popular-songs/{popularSong}/edit', [PopularSongController::class, 'edit'])->name('backend.popular-songs.edit');
    Route::put('backend/popular-songs/{popularSong}', [PopularSongController::class, 'update'])->name('backend.popular-songs.update');
    Route::delete('backend/popular-songs/{popularSong}', [PopularSongController::class, 'destroy'])->name('backend.popular-songs.destroy');
});

require __DIR__ . '/settings.php';
require __DIR__ . '/auth.php';
