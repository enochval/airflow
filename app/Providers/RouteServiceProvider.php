<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * The path to the "home" route for your application.
     *
     * This is used by Laravel authentication to redirect users after login.
     *
     * @var string
     */
    public const HOME = '/home';

    /**
     * The controller namespace for the application.
     *
     * When present, controller route declarations will automatically be prefixed with this namespace.
     *
     * @var string|null
     */
    // protected $namespace = 'App\\Http\\Controllers';

    /**
     * Define your route model bindings, pattern filters, etc.
     *
     * @return void
     */
    public function boot()
    {
        $this->configureRateLimiting();

        $this->routes(function () {
            $this->mapApiRoutes();

            $this->mapWebRoutes();

            $this->mapAuthRoutes();

            $this->mapTestRoutes();

            $this->mapOnboardingRoutes();

            $this->mapSetupRoutes();

            $this->mapFileRoutes();

            $this->mapConfigurationRoutes();
        });
    }

    /**
     * Configure the rate limiters for the application.
     *
     * @return void
     */
    protected function configureRateLimiting()
    {
        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(60)->by(optional($request->user())->id ?: $request->ip());
        });
    }

    /**
     * Define corporate "web" routes for the application.
     */
    protected function mapAuthRoutes()
    {
        Route::prefix('api/v1/auth')
            ->middleware('api')
            ->namespace($this->namespace)
            ->group(base_path('routes/auth.php'));
    }

    protected function mapWebRoutes()
    {
        Route::middleware('web')
            ->namespace($this->namespace)
            ->group(base_path('routes/web.php'));
    }

    protected function mapApiRoutes()
    {
        Route::prefix('api')
            ->middleware('api')
            ->namespace($this->namespace)
            ->group(base_path('routes/api.php'));
    }

    protected function mapTestRoutes()
    {
        Route::prefix('api')
            ->middleware('api')
            ->namespace($this->namespace)
            ->group(base_path('routes/test.php'));
    }

    protected function mapOnboardingRoutes()
    {
        Route::prefix('api/v1/onboard')
            ->middleware('api')
            ->namespace($this->namespace)
            ->group(base_path('routes/onboarding.php'));
    }

    protected function mapSetupRoutes()
    {
        Route::prefix('api/v1/setup')
            ->middleware(['api', 'auth:sanctum', 'attach.company'])
            ->namespace($this->namespace)
            ->group(base_path('routes/setup.php'));
    }

    protected function mapFileRoutes()
    {
        Route::prefix('api/v1/files')
            ->middleware(['api', 'auth:sanctum', 'attach.company'])
            ->namespace($this->namespace)
            ->group(base_path('routes/files.php'));
    }

    protected function mapConfigurationRoutes()
    {
        Route::prefix('api/v1/configuration')
            ->middleware(['api', 'auth:sanctum', 'attach.company'])
            ->namespace($this->namespace)
            ->group(base_path('routes/configuration.php'));
    }
}
