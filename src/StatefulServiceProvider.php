<?php

declare(strict_types=1);

namespace TTBooking\Stateful;

use Illuminate\Contracts\Support\DeferrableProvider;
use Illuminate\Support\ServiceProvider;
use TTBooking\Stateful\Middleware\AmendMiddleware;

class StatefulServiceProvider extends ServiceProvider implements DeferrableProvider
{
    /**
     * All of the singletons that should be registered.
     *
     * @var array<string, class-string>
     */
    public $singletons = [
        'stateful-client' => ConnectionManager::class,
        'stateful-store' => StorageManager::class,
        'stateful-serializer' => SerializerManager::class,
        'stateful' => ServiceManager::class,
    ];

    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->configure();
        $this->registerServices();
    }

    /**
     * Setup the configuration for Stateful.
     */
    protected function configure(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../config/stateful.php', 'stateful');
    }

    /**
     * Register Stateful's services in the container.
     */
    protected function registerServices(): void
    {
        $this->app->alias('stateful-client', Contracts\ClientFactory::class);
        $this->app->alias('stateful-store', Contracts\RepositoryFactory::class);
        $this->app->alias('stateful-serializer', Contracts\SerializerFactory::class);
        $this->app->alias('stateful', Contracts\ServiceFactory::class);

        $this->app->when(AmendMiddleware::class)->needs('$typeAmenders')->giveConfig('stateful.amenders.type', []);
        $this->app->when(AmendMiddleware::class)->needs('$pathAmenders')->giveConfig('stateful.amenders.path', []);
    }

    /**
     * Get the services provided by the provider.
     *
     * @return list<string>
     */
    public function provides(): array
    {
        return [
            'stateful-client', Contracts\ClientFactory::class,
            'stateful-store', Contracts\RepositoryFactory::class,
            'stateful-serializer', Contracts\SerializerFactory::class,
            'stateful', Contracts\ServiceFactory::class,
        ];
    }
}
