<?php

/** @var string $store */
$store = env('SF_REPOSITORY', 'eloquent');

return [

    /*
    |--------------------------------------------------------------------------
    | Default Stateful Service Name
    |--------------------------------------------------------------------------
    */

    'service' => env('SF_SERVICE', 'default'),

    'services' => [

        'air' => [
            'connection' => 'default',
            'store' => 'eloquent', // 'null'
        ],

    ],

    /*
    |--------------------------------------------------------------------------
    | Default Stateful Connection Name
    |--------------------------------------------------------------------------
    */

    // 'connection' => env('SF_CONNECTION', 'default'),

    'connections' => [

        'default' => [
            'uri' => env('WB_URI'),
            'login' => env('WB_LOGIN'),
            'password' => env('WB_PASSWORD'),
            'provider' => env('WB_PROVIDER', ''),
            'salePoint' => null,
            'currency' => env('WB_CURRENCY', 'RUB'),
            'locale' => env('WB_LOCALE', 'ru'),
            // 'respondType' => RespondType::JSON,
            'legacy' => env('WB_LEGACY', true),
        ],

    ],

    /*
    |--------------------------------------------------------------------------
    | Default Stateful Repository Name
    |--------------------------------------------------------------------------
    |
    | Supported drivers: "aggregate", "eloquent", "database", "filesystem",
    |         "array", "null"
    */

    // 'store' => str_contains($store, ',') ? 'aggregate' : $store,

    'stores' => [

        'aggregate' => [
            'stores' => $store === 'aggregate' ? [] : explode(',', $store),
        ],

        'eloquent' => [
            'model' => TTBooking\Stateful\Models\State::class,
        ],

        'database' => [
            'table' => 'stateful_state',
        ],

        'filesystem' => [
            'path' => 'stateful/state',
        ],

    ],

    /*
    |--------------------------------------------------------------------------
    | State Class
    |--------------------------------------------------------------------------
    */

    'state' => TTBooking\Stateful\State::class,

    /*
    |--------------------------------------------------------------------------
    | Query/Result Serializer
    |--------------------------------------------------------------------------
    |
    | Supported serializers: "default", "symfony", "jms"
    */

    'serializer' => env('SF_SERIALIZER', 'default'),

    'serializers' => [

        'symfony' => [
            'normalizers' => [
                Symfony\Component\Serializer\Normalizer\BackedEnumNormalizer::class,
                Symfony\Component\Serializer\Normalizer\DateTimeNormalizer::class,
                Symfony\Component\Serializer\Normalizer\ArrayDenormalizer::class,
                Symfony\Component\Serializer\Normalizer\PropertyNormalizer::class,
            ],
            'encoders' => [
                Symfony\Component\Serializer\Encoder\JsonEncoder::class,
            ],
            'context' => [],
        ],

        'jms' => [
            'enum_support' => true,
            'naming_strategy' => JMS\Serializer\Naming\IdenticalPropertyNamingStrategy::class,
        ],

    ],

    /*
    |--------------------------------------------------------------------------
    | Query Middleware
    |--------------------------------------------------------------------------
    */

    'middleware' => [
        TTBooking\Stateful\Middleware\AmendMiddleware::class,
        TTBooking\Stateful\Middleware\StoreMiddleware::class,
    ],

    /*
    |--------------------------------------------------------------------------
    | Result Amenders
    |--------------------------------------------------------------------------
    */

    'amenders' => [
        'type' => [
            //
        ],
        'path' => [
            //
        ],
    ],

];
