<?php

namespace GmodStore\LaravelWebhooks\Models;

use GuzzleHttp\Exception\RequestException;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Psr\Http\Message\ResponseInterface;

/**
 * @property-read WebhookSubscription                $subscription
 * @property-read RequestException|ResponseInterface $result
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
        'result'
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
     * Set the "result" attribute.
     *
     * @param RequestException|ResponseInterface $value
     *
     * @return void
     */
    public function setResultAttribute($value)
    {
        if (!($value instanceof RequestException) && !($value instanceof ResponseInterface)) {
            throw new \InvalidArgumentException(
                '"value" argument must be of type `RequestException` or `ResponseInterface`'
            );
        }

        $this->attributes['result'] = serialize($value);
    }

    /**
     * Get the "result" attribute.
     *
     * @param string $value
     *
     * @return RequestException|ResponseInterface
     */
    public function getResultAttribute(string $value)
    {
        return unserialize($value);
    }

    /**
     * @return BelongsTo
     */
    public function subscription(): BelongsTo
    {
        return $this->belongsTo(WebhookSubscription::class);
    }
}
