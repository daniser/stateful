<?php

declare(strict_types=1);

namespace TTBooking\Stateful\Contracts;

/**
 * @template TResultPayload of ResultPayload = ResultPayload
 */
interface Result
{
    /**
     * @param  array<string, mixed>  $context
     * @return $this
     */
    public function withContext(array $context): static;

    /**
     * @return array<string, mixed>
     */
    public function getContext(): array;

    /**
     * @phpstan-param TResultPayload $payload
     *
     * @return $this
     */
    public function withPayload(object $payload): static;

    /**
     * @phpstan-return TResultPayload
     */
    public function getPayload(): object;
}
