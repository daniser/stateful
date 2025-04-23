<?php

declare(strict_types=1);

namespace TTBooking\Stateful\Support;

use Closure;
use Illuminate\Contracts\Config\Repository;
use Illuminate\Contracts\Container\Container;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use InvalidArgumentException;
use TTBooking\Stateful\Contracts\Factory;

/**
 * @template TConnection of object
 *
 * @implements Factory<TConnection>
 */
abstract class Manager implements Factory
{
    /** Connection selector key. */
    protected string $selectorKey;

    /** Connection pool key. */
    protected string $poolKey;

    /**
     * The registered custom driver creators.
     *
     * @var array<string, Closure(Container, array<string, mixed>, string, string): TConnection>
     */
    protected array $customCreators = [];

    /**
     * The array of created connections.
     *
     * @var array<string, TConnection>
     */
    protected array $connections = [];

    /**
     * Create a new manager instance.
     *
     * @param  Container  $container  The container instance.
     * @param  Repository  $config  The configuration repository instance.
     */
    public function __construct(protected Container $container, protected Repository $config) {}

    /**
     * Get the default driver name.
     */
    public function getDefaultDriver(): string
    {
        /** @var string */
        return $this->config->get($this->getSelectorKey(), 'default');
    }

    /**
     * Get a connection instance.
     *
     * @return TConnection
     *
     * @throws InvalidArgumentException
     */
    public function connection(?string $name = null)
    {
        $name ??= $this->getDefaultDriver();

        return $this->connections[$name] ??= $this->resolve($name);
    }

    /**
     * @return TConnection
     */
    public function makeConnection(array $config, ?string $name = null)
    {
        //
    }

    /**
     * Get all of the created connections.
     *
     * @return array<string, TConnection>
     */
    public function getConnections(): array
    {
        return $this->connections;
    }

    /**
     * Dynamically call the default connection instance.
     *
     * @param  array<mixed>  $parameters
     */
    public function __call(string $method, array $parameters): mixed
    {
        return $this->connection()->$method(...$parameters);
    }

    /**
     * Register a custom driver creator Closure.
     *
     * @param  Closure(Container, array<string, mixed>, string, string): TConnection  $callback
     * @return $this
     */
    public function extend(string $driver, Closure $callback): static
    {
        $this->customCreators[$driver] = $callback->bindTo($this, $this) ?? $callback;

        return $this;
    }

    /**
     * Resolve the given connection.
     *
     * @return TConnection
     *
     * @throws InvalidArgumentException
     */
    protected function resolve(string $name)
    {
        $config = $this->getConfig($name);

        /** @var string $driver */
        $driver = Arr::pull($config, 'driver');

        if (isset($this->customCreators[$driver])) {
            return $this->callCustomCreator($config, $name, $driver);
        } else {
            $method = 'create'.Str::studly($driver).'Driver';

            if (method_exists($this, $method)) {
                /** @var TConnection */
                return $this->$method($config, $name, $driver);
            }
        }

        throw new InvalidArgumentException("Driver [$driver] not supported.");
    }

    /**
     * Call a custom driver creator.
     *
     * @param  array<string, mixed>  $config
     * @return TConnection
     */
    protected function callCustomCreator(array $config, string $name, string $driver)
    {
        return $this->customCreators[$driver]($this->container, $config, $name, $driver);
    }

    /**
     * Get the connection selector key.
     */
    protected function getSelectorKey(): string
    {
        return $this->selectorKey;
    }

    /**
     * Get the connection pool key.
     */
    protected function getPoolKey(): string
    {
        return $this->poolKey ??= Str::plural($this->getSelectorKey());
    }

    /**
     * Get the connection configuration.
     *
     * @return array<string, mixed>
     */
    protected function getConfig(string $name): array
    {
        $poolKey = $this->getPoolKey();

        /** @var array<string, mixed> $config */
        $config = $this->config->get("$poolKey.$name", []);

        return $config + ['driver' => $name];
    }
}
