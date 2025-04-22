<?php

declare(strict_types=1);

namespace TTBooking\Stateful\Repositories;

use Illuminate\Support\Collection;
use TTBooking\Stateful\Contracts\RepositoryFactory;
use TTBooking\Stateful\Contracts\StateRepository;
use TTBooking\Stateful\Exceptions\AggregateStateNotFoundException;
use TTBooking\Stateful\Exceptions\StateNotFoundException;
use TTBooking\Stateful\State;

class AggregateRepository implements StateRepository
{
    /** @var Collection<int, StateRepository> */
    protected Collection $stores;

    /**
     * @param  RepositoryFactory<StateRepository>  $repository
     * @param  list<string|null>  $stores
     */
    public function __construct(RepositoryFactory $repository, array $stores = [])
    {
        $this->stores = collect($stores)->push('null')->unique()->map(
            static fn (?string $store) => $repository->connection($store)
        );
    }

    public function has(string $id): bool
    {
        return $this->stores->some->has($id);
    }

    /**
     * @throws AggregateStateNotFoundException
     */
    public function get(string $id): State
    {
        $exceptions = [];

        foreach ($this->stores as $store) {
            try {
                return $store->get($id);
            } catch (AggregateStateNotFoundException $e) {
                $exceptions = [...$exceptions, ...$e->getExceptions()];
            } catch (StateNotFoundException $e) {
                $exceptions[] = $e;
            }
        }

        throw AggregateStateNotFoundException::withExceptions("State [$id] not found", $exceptions);
    }

    public function put(State $state): State
    {
        return $this->stores->each->put($state); // @phpstan-ignore-line
    }
}
