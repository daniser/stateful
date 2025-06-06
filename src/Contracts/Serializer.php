<?php

declare(strict_types=1);

namespace TTBooking\Stateful\Contracts;

interface Serializer
{
    /**
     * @param  array<string, mixed>  $context
     */
    public function serialize(mixed $data, array $context = []): string;

    /**
     * @template T of object
     *
     * @param  class-string<T>  $type
     * @param  array<string, mixed>  $context
     * @return T
     */
    public function deserialize(string $data, string $type, array $context = []): object;
}
