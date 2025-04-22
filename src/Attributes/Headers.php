<?php

declare(strict_types=1);

namespace TTBooking\Stateful\Attributes;

use Attribute;

#[Attribute(Attribute::TARGET_CLASS)]
class Headers
{
    /**
     * @param  array<string, string>  $headers
     */
    public function __construct(public array $headers) {}
}
