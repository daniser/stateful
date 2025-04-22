<?php

declare(strict_types=1);

namespace TTBooking\Stateful;

use Symfony\Component\Uid\Ulid;
use TTBooking\Stateful\Contracts\StateFactory;

class UlidStateFactory implements StateFactory
{
    public function make(Contracts\Query $query, Contracts\Result $result, ?string $parentId = null): State
    {
        return new State((string) new Ulid, $query, $result, $parentId);
    }
}
