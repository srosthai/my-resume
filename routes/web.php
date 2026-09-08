<?php

use App\Http\Controllers\Backend\AboutMeController;
use App\Http\Controllers\Backend\EducationController;
use App\Http\Controllers\Backend\FeedController;
use App\Http\Controllers\Backend\NoteController;
use App\Http\Controllers\Backend\PopularSongController;
use App\Http\Controllers\Backend\ProjectController;
use App\Http\Controllers\Backend\ProjectTypeController;
use App\Http\Controllers\Backend\TechStackController;
use App\Http\Controllers\Backend\UserController;
use App\Http\Controllers\Backend\WorkExperienceController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Frontend\PortfolioController;
use App\Http\Controllers\Frontend\SitemapController;
use Illuminate\Support\Facades\Route;

// SEO
Route::get('/sitemap.xml', [SitemapController::class, 'index'])->name('sitemap');

// Public site
Route::get('/', [PortfolioController::class, 'home'])->name('home');
Route::get('/about', [PortfolioController::class, 'about'])->name('about');
Route::get('/portfolio', [PortfolioController::class, 'portfolio'])->name('portfolio');
Route::get('/portfolio/{project}', [PortfolioController::class, 'showProject'])->name('portfolio.show');
Route::get('/contact', [PortfolioController::class, 'contact'])->name('contact');
Route::get('/hobby', [PortfolioController::class, 'hobby'])->name('hobby');
Route::get('/more', [PortfolioController::class, 'more'])->name('more');
Route::get('/resume', [PortfolioController::class, 'resume'])->name('resume');
Route::get('/note', [PortfolioController::class, 'note'])->name('note');
Route::get('/note/{note:slug}', [PortfolioController::class, 'showNote'])->name('note.show');
Route::get('/feeds', [PortfolioController::class, 'feeds'])->name('feeds');
Route::get('/feeds/{feed:slug}', [PortfolioController::class, 'showFeed'])->name('feeds.show');
Route::post('/contact/send', [PortfolioController::class, 'sendContactMessage'])->middleware('throttle:contact')->name('contact.send');

// Public JSON endpoints
Route::middleware('throttle:feed-actions')->group(function () {
    Route::post('/api/feeds/{feed}/view', [PortfolioController::class, 'incrementFeedView'])->name('api.feeds.view');
    Route::post('/api/feeds/{feed}/like', [PortfolioController::class, 'toggleFeedLike'])->name('api.feeds.like');
});
Route::get('/api/popular-songs', [PopularSongController::class, 'getForPlayer'])->name('api.popular-songs');

// Admin (owner only)
Route::middleware(['auth', 'owner'])->group(function () {
    Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::prefix('backend')->name('backend.')->group(function () {
        // "Me": the single owner profile
        Route::get('me', [UserController::class, 'index'])->name('users.index');
        Route::get('users/{user}/delete', [UserController::class, 'delete'])->name('users.delete');
        Route::resource('users', UserController::class)->except('index');

        Route::resource('about-me', AboutMeController::class)->except('show')->parameters(['about-me' => 'aboutMe']);
        Route::resource('work-experience', WorkExperienceController::class)->except('show')->parameters(['work-experience' => 'workExperience']);
        Route::resource('education', EducationController::class)->except('show');
        Route::resource('tech-stacks', TechStackController::class)->except('show')->parameters(['tech-stacks' => 'techStack']);
        Route::resource('project-types', ProjectTypeController::class)->except('show')->parameters(['project-types' => 'projectType']);
        Route::resource('projects', ProjectController::class)->except('show');
        Route::resource('popular-songs', PopularSongController::class)->parameters(['popular-songs' => 'popularSong']);

        Route::patch('notes/{note}/toggle-featured', [NoteController::class, 'toggleFeatured'])->name('notes.toggle-featured');
        Route::post('notes/{note}/duplicate', [NoteController::class, 'duplicate'])->name('notes.duplicate');
        Route::resource('notes', NoteController::class);

        Route::patch('feeds/{feed}/toggle-pinned', [FeedController::class, 'togglePinned'])->name('feeds.toggle-pinned');
        Route::resource('feeds', FeedController::class)->except('show');
    });
});

require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
