<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLaravelWebhooksWebhookSubscriptionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(config('laravel-webhooks.table_names.webhook_subscriptions'), function (Blueprint $table) {
            $table->increments('id');
            $table->morphs('subscriber');
            $table->string('webhook_type')->index();
            $table->jsonb('options');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists(config('laravel-webhooks.table_names.webhook_subscriptions'));
    }
}
