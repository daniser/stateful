<?php

declare(strict_types=1);

namespace TTBooking\Stateful\Contracts;

/**
 * @template TStateRepository of StateRepository
 *
 * @extends Factory<TStateRepository>
 */
interface RepositoryFactory extends Factory {}
