<?php

declare(strict_types=1);

namespace TTBooking\Stateful\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller as BaseController;
use TTBooking\Stateful\Facades\Stateful;

class Controller extends BaseController
{
    public function query(string $service, string $query, ?string $state = null, ?string $closest = null): JsonResponse
    {
        $service = Stateful::service($service);

        if (isset($state)) {
            $state = $service->get($state);
        }

        return JsonResponse::fromJsonString(
            $service->serialize($service->query($service->newQuery($query, $state)))
        );
    }

    public function state(string $service, string $state, ?string $closest = null): JsonResponse
    {
        $service = Stateful::service($service);

        return JsonResponse::fromJsonString(
            $service->serialize($service->get($state))
        );
    }
}
