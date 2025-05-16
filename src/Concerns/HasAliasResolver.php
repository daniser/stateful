<?php

declare(strict_types=1);

namespace TTBooking\Stateful\Concerns;

use TTBooking\Stateful\Contracts\AliasResolver;
use TTBooking\Stateful\Contracts\ResolvesAliases;

/**
 * @phpstan-require-implements ResolvesAliases
 */
trait HasAliasResolver
{
    protected AliasResolver $aliasResolver;

    public function setAliasResolver(AliasResolver $aliasResolver): static
    {
        $this->aliasResolver = $aliasResolver;

        return $this;
    }
}
