<?php

declare(strict_types=1);

namespace TTBooking\Stateful\Middleware;

use Closure;
use Illuminate\Contracts\Container\Container;
use ParentIterator;
use RecursiveArrayIterator;
use RecursiveIteratorIterator;
use TTBooking\Stateful\Contracts\Amender;
use TTBooking\Stateful\Contracts\Query;
use TTBooking\Stateful\Contracts\Result;
use TTBooking\Stateful\Support\RecursivePathIterator;

class AmendMiddleware
{
    /**
     * @param  array<class-string, list<class-string<Amender<object>>>|class-string<Amender<object>>>  $typeAmenders
     * @param  array<string, list<class-string<Amender<object>>>|class-string<Amender<object>>>  $pathAmenders
     */
    public function __construct(
        protected Container $container,
        protected array $typeAmenders = [],
        protected array $pathAmenders = [],
    ) {}

    /**
     * @template TResult of Result
     * @template TQuery of Query<TResult>
     *
     * @phpstan-param  TQuery $query
     * @param  Closure(TQuery): TResult  $next
     *
     * @phpstan-return TResult
     */
    public function handle(Query $query, Closure $next): Result
    {
        $result = $next($query);

        /** @var RecursivePathIterator<array-key, array<mixed>|object> $iterator */
        $iterator = new RecursiveIteratorIterator(
            new ParentIterator(new RecursivePathIterator(new RecursiveArrayIterator($result))), // @phpstan-ignore-line
            RecursiveIteratorIterator::CHILD_FIRST
        );

        foreach ($iterator as $key => $item) {
            $path = $iterator->path();

            if (is_object($item)) {
                $this->amend((array) ($this->typeAmenders[$item::class] ?? []), $item, $key, $result, $path);

                foreach ($this->pathAmenders as $pattern => $amenderClasses) {
                    if (fnmatch($pattern, $path)) {
                        $this->amend((array) $amenderClasses, $item, $key, $result, $path);
                    }
                }
            }
        }

        return $result;
    }

    /**
     * @param  list<class-string<Amender<object>>>  $amenderClasses
     */
    protected function amend(array $amenderClasses, object $item, string|int $key, object $entity, string $path): void
    {
        foreach ($amenderClasses as $amenderClass) {
            /** @var Amender<object> $amender */
            $amender = $this->container->make($amenderClass);
            $amender->amend($item, $key, $entity, $path);
        }
    }
}
