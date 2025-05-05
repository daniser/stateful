<?php

declare(strict_types=1);

namespace TTBooking\Stateful;

use JMS\Serializer\Naming\IdenticalPropertyNamingStrategy;
use JMS\Serializer\Naming\PropertyNamingStrategyInterface;
use JMS\Serializer\Naming\SerializedNameAnnotationStrategy;
use JMS\Serializer\SerializerBuilder;
use Symfony\Component\Serializer\Encoder\DecoderInterface;
use Symfony\Component\Serializer\Encoder\EncoderInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\Serializer as SymfonySerializer;

/**
 * @extends Support\Manager<Contracts\Serializer>
 */
class SerializerManager extends Support\Manager implements Contracts\Serializer, Contracts\SerializerFactory
{
    protected string $selectorKey = 'stateful.serializer';

    public function serialize(mixed $data, array $context = []): string
    {
        return $this->serializer()->serialize($data, $context);
    }

    public function deserialize(string $data, string $type, array $context = []): object
    {
        return $this->serializer()->deserialize($data, $type, $context);
    }

    public function serializer(?string $name = null): Contracts\Serializer
    {
        return $this->connection($name);
    }

    /**
     * @param  array{
     *     normalizers?: list<class-string<NormalizerInterface|DenormalizerInterface>>,
     *     encoders?: list<class-string<EncoderInterface|DecoderInterface>>,
     *     context?: array<string, mixed>,
     * }  $config
     */
    protected function createSymfonyDriver(array $config): SerializerWrapper
    {
        return new SerializerWrapper(
            new SymfonySerializer(
                self::batchInstantiate($config['normalizers'] ?? []),
                self::batchInstantiate($config['encoders'] ?? []),
                // $config['context'] ?? [],
            )
        );
    }

    /**
     * @param  array{
     *     enum_support?: bool,
     *     naming_strategy?: class-string<PropertyNamingStrategyInterface>
     * }  $config
     */
    protected function createJmsDriver(array $config): SerializerWrapper
    {
        return new SerializerWrapper(
            SerializerBuilder::create()
                ->enableEnumSupport($config['enum_support'] ?? false)
                ->setPropertyNamingStrategy(new SerializedNameAnnotationStrategy(
                    isset($config['naming_strategy'])
                        ? new $config['naming_strategy']
                        : new IdenticalPropertyNamingStrategy
                ))
                ->build()
        );
    }

    /**
     * @template T of object
     *
     * @param  list<class-string<T>>  $classes
     * @return list<T>
     */
    private static function batchInstantiate(array $classes): array
    {
        return array_map(static fn (string $class) => new $class, $classes);
    }
}
