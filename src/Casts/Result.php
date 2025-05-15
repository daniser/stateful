<?php

declare(strict_types=1);

namespace TTBooking\Stateful\Casts;

use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Database\Eloquent\Model;
use TTBooking\Stateful\Contracts\ResultPayload;

/**
 * @implements CastsAttributes<ResultPayload, ResultPayload>
 */
class Result implements CastsAttributes
{
    public function get(Model $model, string $key, mixed $value, array $attributes): ?ResultPayload
    {
        return null;
    }

    public function set(Model $model, string $key, mixed $value, array $attributes): string
    {
        return '';
    }
}
