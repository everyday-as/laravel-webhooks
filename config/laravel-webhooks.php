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
    | Retries
    |--------------------------------------------------------------------------
    |
    | Configure whether or not to retry failed webhook executions,
    | the number of retries and delay (in seconds) to wait between retries
    |
    */
    'retries' => [
        'number' => 5,
        'delay'  => 30,
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
    | HTTP Config
    |--------------------------------------------------------------------------
    |
    | Configure options used in the requests made by this library.
    |
    */
    'http' => [
        /*
        |--------------------------------------------------------------------------
        | User agent
        |--------------------------------------------------------------------------
        |
        | Configure the default user agent to use when making requests.
        | NOTE: `?` is replaced by the class name of the webhook being executed.
        |
        */
        'user_agent' => env('LARAVEL_WEBHOOKS_USER_AGENT', 'GmodStore-LaravelWebhooks/?'),

        /*
        |--------------------------------------------------------------------------
        | Request timeout
        |--------------------------------------------------------------------------
        |
        | Configure the timeout for requests, in seconds.
        |
        */
        'timeout' => 1,
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
