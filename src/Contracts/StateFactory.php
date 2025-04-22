<?php

declare(strict_types=1);

namespace TTBooking\Stateful\Contracts;

use TTBooking\Stateful\State;

interface StateFactory
{
    /**
     * @template TResult of Result
     *
     * @phpstan-param Query<TResult> $query
     * @phpstan-param TResult $result
     */
    public function make(Query $query, Result $result, ?string $parentId = null): State;
}
