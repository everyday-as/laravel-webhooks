<?php

namespace GmodStore\LaravelWebhooks;

use Illuminate\Database\Eloquent\Relations\MorphMany;

/**
 * @mixin \Illuminate\Database\Eloquent\Model
 */
trait HasWebhookSubscriptions
{
    /**
     * The webhook subscriptions belonging to this model.
     *
     * @return MorphMany
     */
    public function webhook_subscriptions(): MorphMany
    {
        return $this->morphMany(WebhookSubscription::class, 'subscriber');
    }

    /**
     * @param string  $webhook_type
     * @param array   $options
     *
     * @return false|WebhookSubscription
     */
    public function subscribeToWebhook($webhook_type, array $options)
    {
        return $this
            ->webhook_subscriptions()
            ->save(new WebhookSubscription(compact('webhook_type', 'options')));
    }
}
