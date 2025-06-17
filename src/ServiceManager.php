<?php

declare(strict_types=1);

namespace TTBooking\Stateful;

use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Contracts\Container\Container;
use Illuminate\Http\Request;
use TTBooking\Stateful\Contracts\Query;
use TTBooking\Stateful\Contracts\QueryPayload;
use TTBooking\Stateful\Contracts\ResolvesAliases;
use TTBooking\Stateful\Contracts\Result;
use TTBooking\Stateful\Contracts\ResultPayload;
use TTBooking\Stateful\Contracts\SerializesData;
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

    public function newQuery(string $type, ?Request $request = null): Query
    {
        return $this->service()->newQuery($type, $request);
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
        return $this->cloneContainer($config, $name)->make(Service::class);
    }

    /**
     * @param  array{
     *      serializer?: array<string, mixed>|string,
     *      connection?: array<string, mixed>|string,
     *      store?: array<string, mixed>|string,
     *      query_payload_classes?: array<array-key, class-string<QueryPayload>>,
     *  }  $config
     *
     * @throws BindingResolutionException
     */
    protected function cloneContainer(array $config, string $name): Container
    {
        $container = clone $this->container;

        $container->instance(
            Contracts\Serializer::class,
            $serializer = $this->createSerializer($config, $name)
        );

        $container->instance(
            Contracts\AliasResolver::class,
            $aliasResolver = $this->createAliasResolver($config, $name)
        );

        $container->instance(
            Contracts\Client::class,
            $this->createClient($config, $name, $serializer)
        );

        $container->instance(
            Contracts\StateRepository::class,
            $this->createRepository($config, $name, $serializer, $aliasResolver)
        );

        return $container;
    }

    /**
     * @param  array{serializer?: array<string, mixed>|string}  $config
     *
     * @throws BindingResolutionException
     */
    protected function createSerializer(array $config, string $name): Contracts\Serializer
    {
        /** @var Contracts\SerializerFactory $factory */
        $factory = $this->container->make(Contracts\SerializerFactory::class);

        return $factory->serializer($this->getConnectionName($config, 'serializer', $name));
    }

    /**
     * @param  array{query_payload_classes?: array<array-key, class-string<QueryPayload>>}  $config
     */
    protected function createAliasResolver(array $config, string $name): Contracts\AliasResolver
    {
        return new AliasResolver($config['query_payload_classes'] ?? []);
    }

    /**
     * @param  array{connection?: array<string, mixed>|string}  $config
     *
     * @throws BindingResolutionException
     */
    protected function createClient(array $config, string $name, ?Contracts\Serializer $serializer): Contracts\Client
    {
        /** @var Contracts\ClientFactory $factory */
        $factory = $this->container->make(Contracts\ClientFactory::class);

        $client = $factory->connection($this->getConnectionName($config, 'connection', $name));

        if ($serializer && $client instanceof SerializesData) {
            return (clone $client)->setSerializer($serializer);
        }

        return $client;
    }

    /**
     * @param  array{store?: array<string, mixed>|string}  $config
     *
     * @throws BindingResolutionException
     */
    protected function createRepository(
        array $config,
        string $name,
        ?Contracts\Serializer $serializer,
        ?Contracts\AliasResolver $aliasResolver,
    ): Contracts\StateRepository {
        /** @var Contracts\RepositoryFactory<Contracts\StateRepository> $factory */
        $factory = $this->container->make(Contracts\RepositoryFactory::class);

        $repository = clone $factory->connection($this->getConnectionName($config, 'store', $name));

        if ($serializer && $repository instanceof SerializesData) {
            $repository->setSerializer($serializer);
        }

        if ($aliasResolver && $repository instanceof ResolvesAliases) {
            $repository->setAliasResolver($aliasResolver);
        }

        return $repository;
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
