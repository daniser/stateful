<?php

declare(strict_types=1);

namespace TTBooking\Stateful\Contracts;

interface ResolvesAliases
{
    /**
     * @return $this
     */
    public function setAliasResolver(AliasResolver $aliasResolver): static;
}
