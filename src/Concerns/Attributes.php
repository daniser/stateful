<?php

declare(strict_types=1);

namespace TTBooking\Stateful\Concerns;

use Exception;
use Illuminate\Support\Reflector;
use Illuminate\Support\Str;
use phpDocumentor\Reflection\TypeResolver;
use phpDocumentor\Reflection\Types\ContextFactory;
use PHPStan\PhpDocParser\Ast\ConstExpr\ConstExprStringNode;
use PHPStan\PhpDocParser\Ast\Type\ConstTypeNode;
use PHPStan\PhpDocParser\Ast\Type\IdentifierTypeNode;
use PHPStan\PhpDocParser\Lexer\Lexer;
use PHPStan\PhpDocParser\Parser\ConstExprParser;
use PHPStan\PhpDocParser\Parser\PhpDocParser;
use PHPStan\PhpDocParser\Parser\TokenIterator;
use PHPStan\PhpDocParser\Parser\TypeParser;
use PHPStan\PhpDocParser\ParserConfig;
use ReflectionClass;
use TTBooking\Stateful\Attributes\Alias;
use TTBooking\Stateful\Attributes\ResultType;
use TTBooking\Stateful\Contracts\QueryPayload;
use TTBooking\Stateful\Contracts\ResultPayload;

/**
 * @template TAlias of non-empty-string
 * @template TResultPayload of ResultPayload = ResultPayload
 *
 * @phpstan-require-implements QueryPayload<TAlias, TResultPayload>
 */
trait Attributes
{
    /**
     * @phpstan-return TAlias
     */
    public static function getAlias(): string
    {
        return Reflector::getClassAttribute(static::class, Alias::class)->alias
            ?? static::parseImplements()->alias
            ?? Str::snake(class_basename(static::class));
    }

    /**
     * @return class-string<TResultPayload>
     */
    public static function getResultPayloadType(): string
    {
        return Reflector::getClassAttribute(static::class, ResultType::class)->type
            ?? static::parseImplements()->type
            ?? throw new Exception('ResultType attribute not defined.');
    }

    /**
     * @return object{alias: string|null, type: string|null}
     */
    protected static function parseImplements(): object
    {
        static $result;

        if ($result) {
            return $result;
        }

        $refClass = new ReflectionClass(static::class);

        if ($docComment = $refClass->getDocComment()) {
            $config = new ParserConfig([]);
            $lexer = new Lexer($config);
            $constExprParser = new ConstExprParser($config);
            $typeParser = new TypeParser($config, $constExprParser);
            $phpDocParser = new PhpDocParser($config, $typeParser, $constExprParser);

            $tokens = new TokenIterator($lexer->tokenize($docComment));
            $phpDocNode = $phpDocParser->parse($tokens);
            $implementsTags = $phpDocNode->getImplementsTagValues();

            $context = (new ContextFactory)->createFromReflector($refClass);
            $typeResolver = new TypeResolver;

            foreach ($implementsTags as $implementsTag) {
                $interface = $typeResolver->resolve($implementsTag->type->type->name, $context);

                if (! is_a($interface, QueryPayload::class, true)) {
                    continue;
                }

                $genericTypes = $implementsTag->type->genericTypes;

                if (isset($genericTypes[0]) && $genericTypes[0] instanceof ConstTypeNode) {
                    $constExpr = $genericTypes[0]->constExpr;
                    if ($constExpr instanceof ConstExprStringNode) {
                        $alias = $constExpr->value;
                    }
                }

                if (isset($genericTypes[1]) && $genericTypes[1] instanceof IdentifierTypeNode) {
                    $resultPayloadType = $typeResolver->resolve($genericTypes[1]->name, $context);
                }
            }
        }

        return $result = (object) ['alias' => $alias ?? null, 'type' => $resultPayloadType ?? null];
    }
}
