<?php

declare(strict_types=1);

namespace TTBooking\Stateful;

use Illuminate\Contracts\Foundation\CachesRoutes;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

class StatefulApiServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        if (! ($this->app instanceof CachesRoutes && $this->app->routesAreCached())) {
            $this->registerRoutes();
        }

        if ($this->app->runningInConsole()) {
            $this->offerPublishing();
            $this->registerMigrations();
        }
    }

    /**
     * Register the Stateful API routes.
     */
    protected function registerRoutes(): void
    {
        Route::domain($this->app['config']['stateful-api.domain'] ?? '')
            ->prefix($this->app['config']['stateful-api.path'] ?? '')
            ->name('stateful-api.')
            ->namespace('TTBooking\\Stateful\\Http\\Controllers')
            ->middleware($this->app['config']['stateful-api.middleware'] ?? 'api')
            ->group(fn () => $this->loadRoutesFrom(__DIR__.'/../routes/api.php'));
    }

    /**
     * Setup the resource publishing groups for Stateful/Stateful API.
     */
    protected function offerPublishing(): void
    {
        $this->publishes([
            __DIR__.'/../config/stateful.php' => $this->app->configPath('stateful.php'),
            __DIR__.'/../config/stateful-api.php' => $this->app->configPath('stateful-api.php'),
        ], ['stateful-config', 'stateful', 'config']);

        $this->publishes([
            __DIR__.'/../database/migrations' => $this->app->databasePath('migrations'),
        ], ['stateful-migrations', 'stateful', 'migrations']);
    }

    /**
     * Register the Stateful's migrations.
     */
    protected function registerMigrations(): void
    {
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
    }

    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->configure();
    }

    /**
     * Setup the configuration for Stateful API.
     */
    protected function configure(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../config/stateful-api.php', 'stateful-api');
    }
}
