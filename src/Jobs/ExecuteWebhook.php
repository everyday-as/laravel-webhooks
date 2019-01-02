<?php

namespace GmodStore\LaravelWebhooks\Jobs;

use GmodStore\LaravelWebhooks\Webhook;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
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
    protected $retries;

    /**
     * Create a new job instance.
     *
     * @param Webhook $webhook
     * @param int     $retries
     */
    public function __construct(Webhook $webhook, int $retries = 1)
    {
        $this->webhook = $webhook;
        $this->retries = $retries;
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
        try {
            $this->webhook->handleSuccess((new Client())->send($this->webhook->buildRequest()));
        } catch (ClientException $exception) {
            if ($this->retries >= config('laravel-webhooks.retries.number')) {
                $this->webhook->handleFailure($exception);

                return;
            }

            $pending_dispatch = self::dispatch($this->webhook, $this->retries + 1);

            if (($delay = config('laravel-webhooks.retries.delay')) > 0) {
                $pending_dispatch->delay($delay);
            }
        }
    }
}
