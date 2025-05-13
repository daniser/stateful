<?php

declare(strict_types=1);

namespace TTBooking\Stateful\Http\Controllers;

use Illuminate\Routing\Controller as BaseController;
use TTBooking\Stateful\Contracts\Result;
use TTBooking\Stateful\Facades\Stateful;
use TTBooking\Stateful\State;

class Controller extends BaseController
{
    public function query(string $service, string $query): Result
    {
        $service = Stateful::service($service);

        return $service->query($service->newQuery($query));
    }

    public function state(string $service, string $state, ?string $closest = null): State
    {
        return Stateful::service($service)->get($state);
    }
}
