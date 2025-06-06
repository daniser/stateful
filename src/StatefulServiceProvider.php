<?php

declare(strict_types=1);

namespace TTBooking\Stateful;

use Illuminate\Contracts\Container\Container;
use Illuminate\Contracts\Support\DeferrableProvider;
use Illuminate\Pipeline\Pipeline;
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
        /** @phpstan-ignore-next-line */
        $this->app->singleton('stateful-client.connection', static fn ($app) => $app['stateful-client']->connection());
        $this->app->alias('stateful-client', Contracts\ClientFactory::class);
        $this->app->alias('stateful-client.connection', Contracts\Client::class);

        $this->app->when(AmendMiddleware::class)->needs('$typeAmenders')->giveConfig('stateful.amenders.type', []);
        $this->app->when(AmendMiddleware::class)->needs('$pathAmenders')->giveConfig('stateful.amenders.path', []);

        /** @phpstan-ignore-next-line */
        $this->app->singleton('stateful-store.store', static fn ($app) => $app['stateful-store']->connection());
        $this->app->alias('stateful-store', Contracts\RepositoryFactory::class);
        $this->app->alias('stateful-store.store', Contracts\StateRepository::class);

        /** @phpstan-ignore-next-line */
        $this->app->singleton('stateful-serializer.serializer', static fn ($app) => $app['stateful-serializer']->serializer());
        $this->app->alias('stateful-serializer', Contracts\SerializerFactory::class);
        $this->app->alias('stateful-serializer.serializer', Contracts\Serializer::class);

        /** @phpstan-ignore-next-line */
        $this->app->singleton('stateful.service', static fn ($app) => $app['stateful']->service());
        $this->app->alias('stateful', Contracts\ServiceFactory::class);
        $this->app->alias('stateful.service', Contracts\Service::class);
    }

    /**
     * Get the services provided by the provider.
     *
     * @return list<string>
     */
    public function provides(): array
    {
        return [
            'stateful-client', 'stateful-client.connection',
            Contracts\ClientFactory::class, Contracts\Client::class,
            'stateful-store', 'stateful-store.store',
            Contracts\RepositoryFactory::class, Contracts\StateRepository::class,
            'stateful-serializer', 'stateful-serializer.serializer',
            Contracts\SerializerFactory::class, Contracts\Serializer::class,
            'stateful', 'stateful.service',
            Contracts\ServiceFactory::class, Contracts\Service::class,
        ];
    }
}
