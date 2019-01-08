<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Log failures
    |--------------------------------------------------------------------------
    |
    | Configure whether or not to log webhook failures.
    |
    */
    'log_failures' => env('LARAVEL_WEBHOOKS_LOG_FAILURES', true),

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
        'attempts'    => 5,
        'exponential' => [
            'exponent'  => 2,
            'max_delay' => 300,
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
    ],
];
