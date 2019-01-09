<?php

namespace GmodStore\LaravelWebhooks\Jobs;

use GmodStore\LaravelWebhooks\Webhook;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ExecuteWebhook implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * @var Webhook
     */
    protected $webhook;

    /**
     * @var int
     */
    protected $attempts;

    /**
     * Create a new job instance.
     *
     * @param Webhook $webhook
     * @param int     $attempts
     */
    public function __construct(Webhook $webhook, int $attempts = 1)
    {
        $this->webhook = $webhook;
        $this->attempts = $attempts;
    }

    /**
     * Execute the job.
     *
     * @throws \GuzzleHttp\Exception\GuzzleException
     *
     * @return void
     */
    public function handle()
    {
        if (!config('laravel-webhooks.enabled')) {
            return;
        }

        /** @var Client $client */
        $client = app('laravel-webhooks:client');

        try {
            $this->webhook->handleSuccess($client->send($this->webhook->buildRequest()));
        } catch (RequestException $exception) {
            if ($this->attempts >= config('laravel-webhooks.backoff.attempts')) {
                $this->webhook->handleFailure($exception);

                return;
            }

            $pending_dispatch = self::dispatch($this->webhook, $this->attempts + 1);

            $delay = config('laravel-webhooks.backoff.delay');

            if ($exponential = config('laravel-webhooks.backoff.exponential')) {
                $delay = min($exponential['max_delay'], $delay * ($this->attempts ** $exponential['exponent']));
            }

            $pending_dispatch->delay($delay);
        }
    }
}
