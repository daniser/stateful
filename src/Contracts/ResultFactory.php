<?php

declare(strict_types=1);

namespace TTBooking\Stateful\Contracts;

interface ResultFactory
{
    public function make(ResultPayload $payload): Result;
}
