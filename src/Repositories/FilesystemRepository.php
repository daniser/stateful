<?php

declare(strict_types=1);

namespace TTBooking\Stateful\Repositories;

use Illuminate\Filesystem\Filesystem;
use TTBooking\Stateful\Concerns\HasSerializer;
use TTBooking\Stateful\Contracts\SerializesData;
use TTBooking\Stateful\Contracts\StateRepository;
use TTBooking\Stateful\Exceptions\StateNotFoundException;
use TTBooking\Stateful\State;

class FilesystemRepository implements SerializesData, StateRepository
{
    use HasSerializer;

    public function __construct(
        protected Filesystem $files,
        protected string $path = 'stateful/state',
    ) {}

    public function has(string $id): bool
    {
        // TODO: Implement has() method.

        return false;
    }

    public function get(string $id): State
    {
        // TODO: Implement get() method.

        throw new StateNotFoundException;
    }

    public function put(State $state): State
    {
        // TODO: Implement put() method.

        return $state;
    }
}
