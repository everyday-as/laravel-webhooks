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

class DeliverWebhook implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    protected Webhook $webhook;

    protected int $attempts;

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
    public function handle(): void
    {
        if (!config('laravel-webhooks.enabled')) {
            return;
        }

        /** @var Client $client */
        $client = app('laravel-webhooks:client');

        $request = $this->webhook->buildRequest();

        try {
            $this->webhook->handleSuccess($request, $client->send($request));
        } catch (RequestException $exception) {
            if ($this->attempts >= config('laravel-webhooks.backoff.attempts')) {
                $this->webhook->handleFailure($request, $exception);

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
