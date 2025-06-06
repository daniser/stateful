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
     * @param  TResultPayload  $payload
     * @return $this
     */
    public function withPayload(ResultPayload $payload): static;

    /**
     * @return TResultPayload
     */
    public function getPayload(): ResultPayload;
}
