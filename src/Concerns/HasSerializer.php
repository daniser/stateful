<?php

declare(strict_types=1);

namespace TTBooking\Stateful\Concerns;

use TTBooking\Stateful\Contracts\Serializer;
use TTBooking\Stateful\Contracts\SerializesData;

/**
 * @phpstan-require-implements SerializesData
 */
trait HasSerializer
{
    protected Serializer $serializer;

    public function setSerializer(Serializer $serializer): static
    {
        $this->serializer = $serializer;

        return $this;
    }
}
