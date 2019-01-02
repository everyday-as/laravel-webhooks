<?php

namespace GmodStore\LaravelWebhooks;

use GmodStore\LaravelWebhooks\Jobs\ExecuteWebhook;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Psr7\Request;
use Illuminate\Foundation\Bus\PendingDispatch;
use Psr\Http\Message\ResponseInterface;

abstract class Webhook
{
    /**
     * @return PendingDispatch
     */
    public static function execute(): PendingDispatch
    {
        $webhook = new static(...func_get_args());

        return ExecuteWebhook::dispatch($webhook)
            ->onQueue(config('laravel-webhooks.queue'));
    }

    /**
     * Get the method to use when making the request.
     *
     * @return string
     */
    protected function getMethod()
    {
        return 'GET';
    }

    protected abstract function getUrl(): string;

    /**
     * Get the user agent to use when making the request.
     *
     * @return string
     */
    protected function getUserAgent(): string
    {
        return str_replace('?', class_basename($this), config('laravel-webhooks.user_agent'));
    }

    /**
     * Get the content type for the request.
     *
     * @return string
     */
    protected function getContentType(): string
    {
        return '';
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
     * @return string|array|null
     */
    protected function getBody()
    {
        return null;
    }

    private function buildHeadersArray()
    {
        $headers = $this->getHeaders();

        if (!isset($headers['User-Agent'])) {
            $headers['User-Agent'] = $this->getUserAgent();
        }

        if (!isset($headers['Content-Type']) && !empty($content_type = $this->getContentType())) {
            $headers['Content-Type'] = $content_type;
        }

        return $headers;
    }

    /**
     * @return Request
     */
    public function buildRequest(): Request
    {
        if (!empty($body = $this->getBody())) {
            $options['body'] = $body;
        }

        return new Request(
            $this->getMethod(),
            $this->getUrl(),
            $this->buildHeadersArray(),
            $this->getBody()
        );
    }

    public function handleFailure(ClientException $exception)
    {
        //
    }

    public function handleSuccess(ResponseInterface $response)
    {
        //
    }
}