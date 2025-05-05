<?php

declare(strict_types=1);

namespace TTBooking\Stateful;

use TTBooking\Stateful\Contracts\ResultPayload;

class ResultFactory implements Contracts\ResultFactory
{
    public function make(ResultPayload $payload): Contracts\Result
    {
        return new Result($payload);
    }
}
