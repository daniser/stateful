<?php

declare(strict_types=1);

namespace TTBooking\Stateful\Casts;

use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Database\Eloquent\Model;
use TTBooking\Stateful\Contracts\QueryPayload;

/**
 * @implements CastsAttributes<QueryPayload, QueryPayload>
 */
class Query implements CastsAttributes
{
    public function get(Model $model, string $key, mixed $value, array $attributes): ?QueryPayload
    {
        return null;
    }

    /**
     * @return array{}
     */
    public function set(Model $model, string $key, mixed $value, array $attributes): array
    {
        return [];
    }
}
