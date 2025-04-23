<?php

declare(strict_types=1);

namespace TTBooking\Stateful\Concerns;

use TTBooking\Stateful\Contracts\Serializer;

trait HasSerializer
{
    protected Serializer $serializer;

    public function setSerializer(Serializer $serializer): static
    {
        $this->serializer = $serializer;

        return $this;
    }
}
