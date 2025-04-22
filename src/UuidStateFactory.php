<?php

declare(strict_types=1);

namespace TTBooking\Stateful;

use Symfony\Component\Uid\UuidV7;
use TTBooking\Stateful\Contracts\StateFactory;

class UuidStateFactory implements StateFactory
{
    public function make(Contracts\Query $query, Contracts\Result $result, ?string $parentId = null): State
    {
        return new State((string) new UuidV7, $query, $result, $parentId);
    }
}
