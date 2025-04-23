<?php

declare(strict_types=1);

namespace TTBooking\Stateful;

final readonly class State
{
    /**
     * @template TResult of Contracts\Result
     *
     * @phpstan-param Contracts\Query<TResult> $query
     * @phpstan-param TResult $result
     */
    public function __construct(
        public string $id,
        public string $service,
        public Contracts\Query $query,
        public Contracts\Result $result,
        public ?string $parentId = null,
    ) {}
}
