<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLaravelWebhooksWebhookDeliveriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $table_names = config('laravel-webhooks.table_names');

        Schema::create($table_names['webhook_deliveries'], function (Blueprint $table) use ($table_names) {
            $table->increments('id');
            $table->string('webhook_type')->index();
            $table->unsignedInteger('subscription_id')->nullable();
            $table->boolean('success');
            $table->longText('result');
            $table->timestamps();

            $table->foreign('subscription_id')->references('id')->on($table_names['webhook_subscriptions'])->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists(config('laravel-webhooks.table_names.webhook_deliveries'));
    }
}
