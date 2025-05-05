<?php

declare(strict_types=1);

namespace TTBooking\Stateful;

use Illuminate\Contracts\Container\BindingResolutionException;
use TTBooking\Stateful\Contracts\Query;
use TTBooking\Stateful\Contracts\QueryPayload;
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

    /**
     * @template TResultPayload of ResultPayload
     * @template TQueryPayload of QueryPayload<TResultPayload>
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

    public function service(?string $name = null): Contracts\Service
    {
        return $this->connection($name);
    }

    /**
     * @param  array{
     *     serializer: array<string, mixed>|string|null,
     *     connection: array<string, mixed>|string|null,
     *     store: array<string, mixed>|string|null,
     * }  $config
     *
     * @throws BindingResolutionException
     */
    protected function createDefaultDriver(array $config, string $name): Service
    {
        return new Service(
            $serializer = $this->createSerializer($config, $name),
            $this->createClient($config, $name, $serializer),
            $this->createRepository($config, $name, $serializer),
        );
    }

    /**
     * @param  array{serializer: array<string, mixed>|string|null}  $config
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
     * @param  array{connection: array<string, mixed>|string|null}  $config
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
     * @param  array{store: array<string, mixed>|string|null}  $config
     *
     * @throws BindingResolutionException
     */
    protected function createRepository(array $config, string $name, ?Contracts\Serializer $serializer): Contracts\StateRepository
    {
        /** @var Contracts\RepositoryFactory<Contracts\StateRepository> $factory */
        $factory = $this->container->make(Contracts\RepositoryFactory::class);

        $repository = $factory->connection($this->getConnectionName($config, 'store', $name));

        if ($serializer && $repository instanceof SerializesData) {
            return (clone $repository)->setSerializer($serializer);
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
