<?php

namespace App\Providers;

use App\Models\User;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\Rules\Password;

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
        // The portfolio owner is the only admin and may do everything.
        Gate::before(fn (User $user) => $user->is_owner ? true : null);

        if ($this->app->isProduction()) {
            URL::forceScheme('https');
        }

        // Single-owner site: one strong password matters. The breach check
        // needs network access, so it only runs in production.
        Password::defaults(function () {
            $rule = Password::min(12)->letters()->numbers();

            return $this->app->isProduction() ? $rule->uncompromised() : $rule;
        });

        // Public write endpoints: contact form and feed like/view counters.
        RateLimiter::for('contact', fn (Request $request) => Limit::perMinutes(5, 3)->by($request->ip()));
        RateLimiter::for('feed-actions', fn (Request $request) => Limit::perMinute(30)->by($request->ip()));

        // Share resolved SEO identity (incl. absolute profile image) with the
        // root Blade view so server-rendered meta + JSON-LD are always present,
        // independent of client-side hydration.
        View::composer('app', function ($view) {
            $seo = config('seo');
            $base = $seo['url'];

            $image = User::owner()->value('image');
            $seo['person']['image'] = $image
                ? (str_starts_with($image, 'http') ? $image : $base.'/'.ltrim($image, '/'))
                : $base.$seo['default_image'];

            // Canonical URL on the production domain (ignores local APP_URL host).
            $path = trim(request()->getPathInfo(), '/');
            $seo['canonical'] = $base.($path !== '' ? '/'.$path : '');

            $view->with('seo', $seo);
        });
    }
}
