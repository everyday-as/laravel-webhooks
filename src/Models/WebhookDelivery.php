<?php

namespace GmodStore\LaravelWebhooks\Models;

use GuzzleHttp\Psr7\Message;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

/**
 * @property-read string                 $webhook_type
 * @property-read int                    $subscription_id
 * @property-read bool                $success
 * @property-read RequestInterface       $request
 * @property-read ResponseInterface|null $response
 * @property-read WebhookSubscription    $subscription
 *
 * @method static Builder to(int $webhook_type)
 *
 * @mixin Builder
 */
class WebhookDelivery extends Model
{
    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'success' => 'boolean',
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'webhook_type',
        'subscription_id',
        'success',
        'request',
        'response',
    ];

    /**
     * {@inheritdoc}
     */
    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        $this->setTable(config('laravel-webhooks.table_names.webhook_deliveries'));
    }

    /**
     * Set the "request" attribute.
     *
     * @param RequestInterface $value
     *
     * @return void
     */
    public function setRequestAttribute(RequestInterface $value): void
    {
        $this->attributes['request'] = Message::toString($value);
    }

    /**
     * Get the "request" attribute.
     *
     * @param string $value
     *
     * @return RequestInterface
     */
    public function getRequestAttribute(string $value): RequestInterface
    {
        return Message::parseRequest($value);
    }

    /**
     * Set the "response" attribute.
     *
     * @param ResponseInterface|null $value
     *
     * @return void
     */
    public function setResponseAttribute(?ResponseInterface $value): void
    {
        $this->attributes['response'] = null === $value ? null : Message::toString($value);
    }

    /**
     * Get the "response" attribute.
     *
     * @param string|null $value
     *
     * @return ResponseInterface|null
     */
    public function getResponseAttribute(?string $value): ?ResponseInterface
    {
        return null === $value ? null : Message::ParseResponse($value);
    }

    /**
     * @return BelongsTo
     */
    public function subscription(): BelongsTo
    {
        return $this->belongsTo(WebhookSubscription::class);
    }
}
