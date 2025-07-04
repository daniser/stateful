<?php

declare(strict_types=1);

namespace TTBooking\Stateful;

use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Contracts\Container\Container;
use Illuminate\Http\Request;
use TTBooking\Stateful\Contracts\ClientFactory;
use TTBooking\Stateful\Contracts\Query;
use TTBooking\Stateful\Contracts\QueryPayload;
use TTBooking\Stateful\Contracts\RepositoryFactory;
use TTBooking\Stateful\Contracts\Result;
use TTBooking\Stateful\Contracts\ResultPayload;
use TTBooking\Stateful\Contracts\SerializerFactory;
use TTBooking\Stateful\Exceptions\ClientException;

/**
 * @extends Support\Manager<Contracts\Service>
 */
class ServiceManager extends Support\Manager implements Contracts\Service, Contracts\ServiceFactory
{
    protected string $selectorKey = 'stateful.service';

    public function serialize(mixed $data, array $context = []): string
    {
        return $this->service()->serialize($data, $context);
    }

    public function deserialize(string $data, string $type, array $context = []): object
    {
        return $this->service()->deserialize($data, $type, $context);
    }

    public function resolveAlias(string $alias): string
    {
        return $this->service()->resolveAlias($alias);
    }

    /**
     * @template TResultPayload of ResultPayload
     * @template TQueryPayload of QueryPayload<non-empty-string, TResultPayload>
     *
     * @param  Query<TQueryPayload>  $query
     * @return Result<TResultPayload>
     *
     * @throws ClientException
     */
    public function query(Query $query): Result
    {
        return $this->service()->query($query);
    }

    public function has(string $id): bool
    {
        return $this->service()->has($id);
    }

    public function get(string $id): State
    {
        return $this->service()->get($id);
    }

    public function put(State $state): State
    {
        return $this->service()->put($state);
    }

    public function newQuery(string $type, ?State $state = null, ?Request $request = null): Query
    {
        return $this->service()->newQuery($type, $state, $request);
    }

    public function service(?string $name = null): Contracts\Service
    {
        return $this->connection($name);
    }

    /**
     * @param  array{
     *     serializer?: array<string, mixed>|string,
     *     connection?: array<string, mixed>|string,
     *     store?: array<string, mixed>|string,
     *     query_payload_classes?: array<array-key, class-string<QueryPayload>>,
     * }  $config
     *
     * @throws BindingResolutionException
     */
    protected function createDefaultDriver(array $config, string $name): Service
    {
        /** @var Service */
        return $this->cloneContainer($config, $name)->make(Service::class, compact('name'));
    }

    /**
     * @param  array{
     *      serializer?: array<string, mixed>|string,
     *      connection?: array<string, mixed>|string,
     *      store?: array<string, mixed>|string,
     *      query_payload_classes?: array<array-key, class-string<QueryPayload>>,
     *  }  $config
     */
    protected function cloneContainer(array $config, string $name): Container
    {
        $container = clone $this->container;

        $container->instance(Container::class, $container);

        $container->instance(
            Contracts\Serializer::class,
            $container->call($this->createSerializer(...), compact('config', 'name'))
        );

        $container->instance(Contracts\AliasResolver::class, $this->createAliasResolver($config));

        $container->instance(
            Contracts\Client::class,
            $container->call($this->createClient(...), compact('config', 'name'))
        );

        $container->instance(
            Contracts\StateRepository::class,
            $container->call($this->createRepository(...), compact('config', 'name'))
        );

        return $container;
    }

    /**
     * @param  array{serializer?: array<string, mixed>|string}  $config
     */
    protected function createSerializer(SerializerFactory $factory, array $config, string $name): Contracts\Serializer
    {
        return $factory->serializer($this->getConnectionName($config, 'serializer', $name));
    }

    /**
     * @param  array{query_payload_classes?: array<array-key, class-string<QueryPayload>>}  $config
     */
    protected function createAliasResolver(array $config): Contracts\AliasResolver
    {
        return new AliasResolver($config['query_payload_classes'] ?? []);
    }

    /**
     * @param  array{connection?: array<string, mixed>|string}  $config
     */
    protected function createClient(ClientFactory $factory, array $config, string $name): Contracts\Client
    {
        return $factory->connection($this->getConnectionName($config, 'connection', $name));
    }

    /**
     * @param  RepositoryFactory<Contracts\StateRepository>  $factory
     * @param  array{store?: array<string, mixed>|string}  $config
     */
    protected function createRepository(RepositoryFactory $factory, array $config, string $name): Contracts\StateRepository
    {
        return $factory->connection($this->getConnectionName($config, 'store', $name));
    }

    /**
     * @param  array<string, array<string, mixed>|string|null>  $config
     */
    protected function getConnectionName(array $config, string $key, string $name): ?string
    {
        $connection = $config[$key] ?? null;

        return is_array($connection) ? ".{$this->getPoolKey()}.$name.$key" : $connection;
    }
}
