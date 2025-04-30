<?php

declare(strict_types=1);

namespace TTBooking\Stateful;

final readonly class State
{
    /**
     * @template TResultPayload of Contracts\ResultPayload
     * @template TQueryPayload of Contracts\QueryPayload<TResultPayload>
     *
     * @param  Contracts\Query<TQueryPayload>  $query
     * @param  Contracts\Result<TResultPayload>  $result
     */
    public function __construct(
        public string $id,
        public Contracts\Query $query,
        public Contracts\Result $result,
        public ?string $parentId = null,
    ) {}
}
