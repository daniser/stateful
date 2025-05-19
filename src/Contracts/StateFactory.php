<?php

declare(strict_types=1);

namespace TTBooking\Stateful\Contracts;

use TTBooking\Stateful\State;

interface StateFactory
{
    /**
     * @template TResultPayload of ResultPayload
     * @template TQueryPayload of QueryPayload<non-empty-string, TResultPayload>
     *
     * @param  Query<TQueryPayload>  $query
     * @param  Result<TResultPayload>  $result
     */
    public function make(Query $query, Result $result, ?string $parentId = null): State;
}
