<?php

namespace GmodStore\LaravelWebhooks;

use GmodStore\LaravelWebhooks\Jobs\DeliverWebhook;
use GmodStore\LaravelWebhooks\Models\WebhookDelivery;
use GmodStore\LaravelWebhooks\Models\WebhookSubscription;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Psr7\Request;
use Illuminate\Foundation\Bus\PendingDispatch;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Str;
use Psr\Http\Message\ResponseInterface;

abstract class Webhook
{
    use SerializesModels;

    protected ?WebhookSubscription $subscription = null;

    /**
     * Construct and deliver a webhook of this type.
     *
     * @return PendingDispatch
     */
    public static function execute(): PendingDispatch
    {
        return (new static(...func_get_args()))->deliver();
    }

    /**
     * Dispatch a job to deliver this webhook.
     *
     * @param null $queue
     *
     * @return PendingDispatch
     */
    public function deliver($queue = null): PendingDispatch
    {
        $pending_dispatch = DeliverWebhook::dispatch($this);

        if (!empty($queue = $queue ?? config('laravel-webhooks.queue'))) {
            $pending_dispatch->onQueue($queue);
        }

        return $pending_dispatch;
    }

    /**
     * Build the request this webhook should perform.
     *
     * @return Request
     */
    public function buildRequest(): Request
    {
        $body = $this->getBody();

        if (is_array($body)) {
            $body = \GuzzleHttp\json_encode($body);
        }

        return new Request(
            strtoupper($this->getMethod()),
            $this->getUrl(),
            $this->buildHeadersArray(),
            $body
        );
    }

    /**
     * Handle a failed delivery of the webhook.
     *
     * @param Request          $request
     * @param RequestException $exception
     *
     * @return void
     */
    public function handleFailure(Request $request, RequestException $exception): void
    {
        if (config('laravel-webhooks.log_deliveries')) {
            $this->logDelivery($request, $exception);
        }
    }

    /**
     * Handle a successful delivery of the webhook.
     *
     * @param Request           $request
     * @param ResponseInterface $response
     *
     * @return void
     */
    public function handleSuccess(Request $request, ResponseInterface $response): void
    {
        if (config('laravel-webhooks.log_deliveries')) {
            $this->logDelivery($request, $response);
        }
    }

    /**
     * Set the "subscription" property to the `WebhookSubscription` associated with this `Webhook` instance.
     *
     * @param WebhookSubscription $subscription
     */
    final public function setSubscription(WebhookSubscription $subscription): void
    {
        $this->subscription = $subscription;
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
    protected function getMethod(): string
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
        return Str::replaceLast('Webhook', '', class_basename(static::class));
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
     * @return string|array|resource|\Psr\Http\Message\StreamInterface|null
     */
    protected function getBody()
    {
        return [];
    }

    /**
     * @param Request                            $request
     * @param ResponseInterface|RequestException $result
     *
     * @return WebhookDelivery
     */
    protected function logDelivery(Request $request, $result): WebhookDelivery
    {
        if (!($success = $result instanceof ResponseInterface)) {
            $result = $result->getResponse();
        }

        return WebhookDelivery::create([
            'webhook_type'    => static::class,
            'subscription_id' => optional($this->subscription)->id,
            'success'         => $success,
            'request'         => $request,
            'response'        => $result,
        ]);
    }

    /**
     * Build the array of headers to send in the request.
     *
     * @return array
     */
    private function buildHeadersArray(): array
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
}
