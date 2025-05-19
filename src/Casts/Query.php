<?php

declare(strict_types=1);

namespace TTBooking\Stateful\Casts;

use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Database\Eloquent\Model;
use TTBooking\Stateful\Contracts\AliasResolver;
use TTBooking\Stateful\Contracts\Query as QueryContract;
use TTBooking\Stateful\Contracts\Serializer;
use TTBooking\Stateful\Facades\Stateful;

/**
 * @implements CastsAttributes<QueryContract, QueryContract>
 */
class Query implements CastsAttributes
{
    /** @var array<string, true> */
    protected array $context;

    public function __construct(string ...$arguments)
    {
        $this->context = array_fill_keys($arguments, true);
    }

    /**
     * @param  string  $value
     * @param  array{service?: string, type: string}  $attributes
     */
    public function get(Model $model, string $key, mixed $value, array $attributes): ?QueryContract
    {
        $service = $this->service($attributes);
        $type = $service->resolveAlias($attributes['type']);

        return $service->deserialize($value, $type, $this->context);
    }

    /**
     * @param  QueryContract  $value
     * @param  array{service?: string}  $attributes
     * @return array{type: string, query: string}
     */
    public function set(Model $model, string $key, mixed $value, array $attributes): array
    {
        $service = $this->service($attributes);
        $payload = $value->getPayload();

        return [
            'type' => $payload::getAlias(),
            'query' => $service->serialize($payload, $this->context),
        ];
    }

    /**
     * @param  array{service?: string}  $attributes
     */
    protected function service(array $attributes): AliasResolver&Serializer
    {
        return Stateful::service($attributes['service'] ?? null);
    }
}
