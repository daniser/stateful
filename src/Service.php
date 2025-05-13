<?php

declare(strict_types=1);

namespace TTBooking\Stateful;

use ArgumentCountError;
use Illuminate\Contracts\Container\Container;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Reflector;
use Illuminate\Support\Str;
use InvalidArgumentException;
use ReflectionException;
use ReflectionMethod;
use TTBooking\Stateful\Contracts\Query;
use TTBooking\Stateful\Contracts\QueryPayload;
use TTBooking\Stateful\Contracts\Result;
use TTBooking\Stateful\Contracts\ResultPayload;
use TTBooking\Stateful\Exceptions\ClientException;
use UnexpectedValueException;

class Service implements Contracts\Service
{
    public function __construct(
        protected Contracts\Serializer $serializer,
        protected Contracts\Client $client,
        protected Contracts\StateRepository $store,
        protected Container $container,
    ) {}

    public function serialize(mixed $data, array $context = []): string
    {
        return $this->serializer->serialize($data, $context);
    }

    public function deserialize(string $data, string $type, array $context = []): object
    {
        return $this->serializer->deserialize($data, $type, $context);
    }

    /**
     * @template TResultPayload of ResultPayload
     * @template TQueryPayload of QueryPayload<TResultPayload>
     *
     * @param  Query<TQueryPayload>  $query
     * @return Result<TResultPayload>
     *
     * @throws ClientException
     */
    public function query(Query $query): Result
    {
        return $this->client->query($query);
    }

    public function has(string $id): bool
    {
        return $this->store->has($id);
    }

    public function get(string $id): State
    {
        return $this->store->get($id);
    }

    public function put(State $state): State
    {
        return $this->store->put($state);
    }

    public function newQuery(string $query, ?Request $request = null): Query
    {
        try {
            $refMethod = new ReflectionMethod($this, 'new'.Str::studly($query).'Query');
        } catch (ReflectionException $e) {
            throw new InvalidArgumentException("Request handler for query [$query] not registered.", $e->getCode(), $e);
        }

        $refParameter = $refMethod->getParameters()[0]
            ?? throw new ArgumentCountError("Request handler for query [$query] missing required parameter.");

        /** @var class-string<FormRequest> $requestClass */
        $requestClass = Reflector::getParameterClassName($refParameter)
            ?? throw new InvalidArgumentException('Cannot determine parameter type.');

        Reflector::isParameterSubclassOf($refParameter, FormRequest::class)
            or throw new InvalidArgumentException('Handler parameter must be descendant of a FormRequest class.');

        $request = $requestClass::createFrom($request ?? $this->container->make(Request::class))
            ->setContainer($this->container)
            ->setRedirector($this->container->make(Redirector::class));

        $request->validateResolved();

        $query = $refMethod->invoke($this, $request);

        $query instanceof Query or throw new UnexpectedValueException('Request handler must return valid query.');

        return $query;
    }
}
