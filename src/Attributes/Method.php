<?php

declare(strict_types=1);

namespace TTBooking\Stateful\Attributes;

use Attribute;

#[Attribute(Attribute::TARGET_CLASS)]
class Method
{
    public function __construct(public string $method) {}
}
