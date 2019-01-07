<?php

namespace GmodStore\LaravelWebhooks\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Foundation\Bus\PendingDispatch;

/**
 * @property-read string $subscriber_type
 * @property-read int    $subscriber_id
 * @property-read string $webhook_type
 * @property-read array  $options
 *
 * @method static Builder to(int $webhook_type)
 *
 * @mixin Builder
 */
class WebhookSubscription extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'webhook_type',
        'options',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'options' => 'array',
    ];

    /**
     * {@inheritdoc}
     */
    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        $this->setTable(config('laravel-webhooks.table_names.webhook_subscriptions'));
    }

    /**
     * Scope the query to only include subscriptions to $webhook_type.
     *
     * @param Builder $query
     * @param string  $webhook_type
     *
     * @return Builder
     */
    public function scopeTo(Builder $query, string $webhook_type): Builder
    {
        return $query->where(compact('webhook_type'));
    }

    /**
     * The subscriber this subscription belongs to.
     *
     * @return MorphTo
     */
    public function subscriber(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * @return PendingDispatch
     */
    public function executeWebhook(): PendingDispatch
    {
        /** @var \Gmodstore\LaravelWebhooks\Webhook $webhook */
        $webhook = new $this->webhook_type(...func_get_args());

        $webhook->setSubscription($this);

        return $webhook->dispatch();
    }
}
