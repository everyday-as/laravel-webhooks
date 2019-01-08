<?php

namespace GmodStore\LaravelWebhooks;

use GmodStore\LaravelWebhooks\Jobs\ExecuteWebhook;
use GmodStore\LaravelWebhooks\Models\WebhookSubscription;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Psr7\Request;
use Illuminate\Foundation\Bus\PendingDispatch;
use Illuminate\Support\Facades\Log;
use Psr\Http\Message\ResponseInterface;

abstract class Webhook
{
    /**
     * @var WebhookSubscription
     */
    protected $subscription;

    /**
     * @return PendingDispatch
     */
    public static function execute(): PendingDispatch
    {
        return (new static(...func_get_args()))->dispatch();
    }

    /**
     * Get the URL to make the request to.
     *
     * @return string
     */
    abstract protected function getUrl(): string;

    /**
     * Get the method to use when making the request.
     *
     * @return string
     */
    protected function getMethod()
    {
        return 'POST';
    }

    /**
     * Get the content type for the request.
     *
     * @return string
     */
    protected function getContentType(): string
    {
        return 'application/json';
    }

    protected function getType(): string
    {
        return str_replace_last('Webhook', '', class_basename(static::class));
    }

    /**
     * Get the headers to send with the request.
     *
     * @return array
     */
    protected function getHeaders(): array
    {
        return [];
    }

    /**
     * Get the request body.
     *
     * @return string|null|resource|\Psr\Http\Message\StreamInterface
     */
    protected function getBody()
    {
        return '{}';
    }

    private function buildHeadersArray()
    {
        $headers = [];

        if (!empty($content_type = $this->getContentType())) {
            $headers['Content-Type'] = $content_type;
        }

        if ($type_header_name = config('laravel-webhooks.headers.webhook_type')) {
            $headers[$type_header_name] = $this->getType();
        }

        return array_merge($headers, $this->getHeaders());
    }

    /**
     * Dispatch a job to execute this webhook.
     *
     * @param null $queue
     *
     * @return PendingDispatch
     */
    public function dispatch($queue = null): PendingDispatch
    {
        $pending_dispatch = ExecuteWebhook::dispatch($this);

        if (!empty($queue = $queue ?? config('laravel-webhooks.queue'))) {
            $pending_dispatch->onQueue($queue);
        }

        return $pending_dispatch;
    }

    /**
     * @return Request
     */
    public function buildRequest(): Request
    {
        return new Request(
            strtoupper($this->getMethod()),
            $this->getUrl(),
            $this->buildHeadersArray(),
            $this->getBody()
        );
    }

    public function handleFailure(RequestException $exception)
    {
        if (config('laravel-webhooks.log_failures')) {
            Log::error(
                'Webhook failed',
                [
                    'webhook'   => $this,
                    'exception' => $exception,
                ]
            );
        }
    }

    public function handleSuccess(ResponseInterface $response)
    {
        //
    }

    /**
     * Set the "subscription" property to the `WebhookSubscription` associated with this `Webhook` instance.
     *
     * @param WebhookSubscription $subscription
     */
    final public function setSubscription(WebhookSubscription $subscription)
    {
        $this->subscription = $subscription;
    }
}
