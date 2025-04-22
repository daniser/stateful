<?php

declare(strict_types=1);

namespace TTBooking\Stateful\Middleware;

use Closure;
use TTBooking\Stateful\Contracts\Query;
use TTBooking\Stateful\Contracts\Result;
use TTBooking\Stateful\Contracts\StateFactory;
use TTBooking\Stateful\Contracts\StateRepository;

class StoreMiddleware
{
    public function __construct(protected StateFactory $state, protected StateRepository $store) {}

    /**
     * @template TResult of Result
     * @template TQuery of Query<TResult>
     *
     * @phpstan-param  TQuery $query
     * @param  Closure(TQuery): TResult  $next
     *
     * @phpstan-return TResult
     */
    public function handle(Query $query, Closure $next): Result
    {
        $this->store->put(
            $this->state->make($query, $result = $next($query), $query->getContext()['parent_state_id'] ?? null)
        );

        return $result;
    }
}
