<?php

declare(strict_types=1);

namespace TTBooking\Stateful\Contracts;

use Illuminate\Http\Request;

interface Service extends Client, Serializer, StateRepository
{
    public function newQuery(string $query, ?Request $request = null): Query;
}
