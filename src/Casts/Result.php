<?php

declare(strict_types=1);

namespace TTBooking\Stateful\Casts;

use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Database\Eloquent\Model;
use TTBooking\Stateful\Contracts\Result as ResultContract;
use TTBooking\Stateful\Contracts\Serializer;
use TTBooking\Stateful\Facades\Stateful;

/**
 * @implements CastsAttributes<ResultContract, ResultContract>
 */
class Result implements CastsAttributes
{
    /** @var array<string, true> */
    protected array $context;

    public function __construct(string ...$arguments)
    {
        $this->context = array_fill_keys($arguments, true);
    }

    /**
     * @param  string  $value
     * @param  array{service?: string}  $attributes
     */
    public function get(Model $model, string $key, mixed $value, array $attributes): ?ResultContract
    {
        return $this->serializer($attributes)->deserialize($value, ResultContract::class, $this->context);
    }

    /**
     * @param  ResultContract  $value
     * @param  array{service?: string}  $attributes
     */
    public function set(Model $model, string $key, mixed $value, array $attributes): string
    {
        return $this->serializer($attributes)->serialize($value, $this->context);
    }

    /**
     * @param  array{service?: string}  $attributes
     */
    protected function serializer(array $attributes): Serializer
    {
        return Stateful::service($attributes['service'] ?? null);
    }
}
