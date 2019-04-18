<?php

namespace GmodStore\LaravelWebhooks;

use GmodStore\LaravelWebhooks\Models\WebhookSubscription;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Foundation\Bus\PendingDispatch;

/**
 * @property-read \Illuminate\Database\Eloquent\Collection $webhook_subscriptions
 *
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
     * @param string $webhook_type
     * @param array  $options
     *
     * @return false|WebhookSubscription
     */
    public function subscribeToWebhook(string $webhook_type, array $options)
    {
        return $this
            ->webhook_subscriptions()
            ->save(new WebhookSubscription(compact('webhook_type', 'options')));
    }

    /**
     * @param string $webhook_type
     * @param mixed  ...$args
     *
     * @return PendingDispatch[]
     */
    public function executeSubscriptionsTo(string $webhook_type, ...$args): array
    {
        return $this->webhook_subscriptions()
            ->to($webhook_type)
            ->get()
            ->map(function (WebhookSubscription $subscription) use ($args) {
                return call_user_func_array([$subscription, 'executeWebhook'], $args);
            })
            ->toArray();
    }
}
