<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Enabled
    |--------------------------------------------------------------------------
    |
    | Enable / disable Laravel Webhooks, useful when testing locally.
    |
    */
    'enabled' => env('LARAVEL_WEBHOOKS_ENABLED', false),

    /*
    |--------------------------------------------------------------------------
    | Log deliveries
    |--------------------------------------------------------------------------
    |
    | Configure whether or not to log webhook deliveries in the database.
    |
    */
    'log_deliveries' => env('LARAVEL_WEBHOOKS_LOG_DELIVERIES', true),

    /*
    |--------------------------------------------------------------------------
    | Backoff
    |--------------------------------------------------------------------------
    |
    | Configure whether or not to retry failed webhook executions, the number
    | of attempts and delay (in seconds) between attempts. In the case that
    | exponential backoff is enabled "delay" is used as the initial delay.
    | Setting "exponential" to false will disable exponential backoff, and
    | setting "attempts" to 0 will disable backoff entirely.
    |
    */
    'backoff' => [
        'delay'       => 30,
        'attempts'    => 15,
        'exponential' => [
            'exponent'  => 2,
            'max_delay' => 43200,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Queue
    |--------------------------------------------------------------------------
    |
    | Configure the queue to execute webhooks on.
    |
    */
    'queue' => env('LARAVEL_WEBHOOKS_QUEUE'),

    /*
    |--------------------------------------------------------------------------
    | Headers
    |--------------------------------------------------------------------------
    |
    | Configure the names of custom headers used by this library, set to false
    | to disable.
    |
    */
    'headers' => [
        'webhook_type' => 'X-Webhook-Type',
    ],

    /*
    |--------------------------------------------------------------------------
    | Guzzle Config
    |--------------------------------------------------------------------------
    |
    | Configure the options passed to the shared guzzle client used to execute
    | all webhook requests.
    |
    */
    'guzzle' => [
        'timeout' => 1,

        'headers' => [
            'User-Agent' => env('LARAVEL_WEBHOOKS_USER_AGENT', 'GmodStore/LaravelWebhooks'),
        ],

        'proxy' => env('LARAVEL_WEBHOOKS_PROXY'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Table names
    |--------------------------------------------------------------------------
    |
    | Configure table names.
    |
    */
    'table_names' => [
        'webhook_subscriptions' => 'webhook_subscriptions',
        'webhook_deliveries'    => 'webhook_deliveries',
    ],
];
