<?php

declare(strict_types=1);

namespace TTBooking\Stateful;

use ReflectionClass;
use ReflectionException;
use ReflectionNamedType;
use ReflectionObject;
use ReflectionProperty;

/**
 * Instantiate a class without calling its constructor.
 * If a class has promoted properties, apply default values from corresponding constructor parameters.
 *
 * @template T of object
 *
 * @param  class-string<T>  $class
 *
 * @phpstan-return T
 */
function entity(string $class): object
{
    $refClass = new ReflectionClass($class);
    $refParams = $refClass->getConstructor()?->getParameters() ?? [];
    $entity = $refClass->newInstanceWithoutConstructor();

    foreach ($refParams as $refParam) {
        $refParam->isPromoted() && $refParam->isDefaultValueAvailable() && $refClass->hasProperty($refParam->name)
        && $refClass->getProperty($refParam->name)->setValue($entity, $refParam->getDefaultValue());
    }

    /** @var T */
    return $entity;
}

/**
 * Check for object completeness.
 * To be complete, an object must have no uninitialized public properties.
 */
function complete(object $object): bool
{
    $refObject = new ReflectionObject($object);
    $refProps = $refObject->getProperties(ReflectionProperty::IS_PUBLIC);

    foreach ($refProps as $refProp) {
        if (! $refProp->isInitialized($object)) {
            return false;
        }

        $propValue = $refProp->getValue($object);
        if (is_iterable($propValue)) {
            foreach ($propValue as $item) {
                if (is_object($item) && ! complete($item)) {
                    return false;
                }
            }
        } elseif (is_object($propValue) && ! complete($propValue)) {
            return false;
        }
    }

    return true;
}

/**
 * Get an actual type of the specified property.
 *
 * @param  class-string|object  $objectOrClass
 *
 * @return class-string
 *
 * @throws ReflectionException
 */
function property_class(object|string $objectOrClass, string $propertyName): string
{
    $refClass = new ReflectionClass($objectOrClass);
    $refType = $refClass->getProperty($propertyName)->getType();

    if ($refType instanceof ReflectionNamedType) {
        /** @var class-string */
        return $refType->getName();
    }

    throw new ReflectionException("Property \"$propertyName\" has no type or has more than one type.");
}

/**
 * Get an actual return type of the specified method.
 *
 * @param  class-string|object  $objectOrClass
 *
 * @return class-string
 *
 * @throws ReflectionException
 */
function return_class(object|string $objectOrClass, string $methodName): string
{
    $refClass = new ReflectionClass($objectOrClass);
    $refType = $refClass->getMethod($methodName)->getReturnType();

    if ($refType instanceof ReflectionNamedType) {
        /** @var class-string */
        return $refType->getName();
    }

    throw new ReflectionException("Method \"$methodName\" has no return type or has more than one return type.");
}
