<?php

declare(strict_types=1);

namespace TTBooking\Stateful\Attributes;

use Attribute;

#[Attribute(Attribute::TARGET_CLASS)]
class Alias
{
    public function __construct(public string $alias) {}
}
