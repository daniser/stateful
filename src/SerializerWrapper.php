<?php

declare(strict_types=1);

namespace TTBooking\Stateful;

use JMS\Serializer\SerializerInterface as JMSSerializerInterface;
use Symfony\Component\Serializer\SerializerInterface as SymfonySerializerInterface;

class SerializerWrapper implements Contracts\Serializer
{
    public function __construct(protected JMSSerializerInterface|SymfonySerializerInterface $serializer) {}

    public function serialize(mixed $data, array $context = []): string
    {
        return $this->serializer->serialize($data, 'json');
    }

    public function deserialize(string $data, string $type, array $context = []): object
    {
        // @phpstan-ignore-next-line
        return $this->serializer->deserialize($data, $type, 'json');
    }
}
