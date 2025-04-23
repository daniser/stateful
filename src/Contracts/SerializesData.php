<?php

declare(strict_types=1);

namespace TTBooking\Stateful\Contracts;

interface SerializesData
{
    /**
     * @return $this
     */
    public function setSerializer(Serializer $serializer): static;
}
