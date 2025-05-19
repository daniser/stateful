<?php

declare(strict_types=1);

namespace TTBooking\Stateful\Middleware;

use Closure;
use TTBooking\Stateful\Contracts\Query;
use TTBooking\Stateful\Contracts\QueryPayload;
use TTBooking\Stateful\Contracts\Result;
use TTBooking\Stateful\Contracts\ResultPayload;
use TTBooking\Stateful\Contracts\StateFactory;
use TTBooking\Stateful\Contracts\StateRepository;

class StoreMiddleware
{
    public function __construct(protected StateFactory $state, protected StateRepository $store) {}

    /**
     * @template TResultPayload of ResultPayload
     * @template TQueryPayload of QueryPayload<non-empty-string, TResultPayload>
     *
     * @param  Query<TQueryPayload>  $query
     * @param  Closure(Query<TQueryPayload>): Result<TResultPayload>  $next
     * @return Result<TResultPayload>
     */
    public function handle(Query $query, Closure $next): Result
    {
        /** @var string|null $parentId */
        $parentId = $query->getContext()['parent_state_id'] ?? null;

        $this->store->put(
            $this->state->make($query, $result = $next($query), $parentId)
        );

        return $result;
    }
}
