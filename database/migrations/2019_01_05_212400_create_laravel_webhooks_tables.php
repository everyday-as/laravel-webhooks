<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLaravelWebhooksTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $table_names = config('laravel-webhooks.table_names');

        Schema::create($table_names['webhook_subscriptions'], function (Blueprint $table) {
            $table->increments('id');
            $table->morphs('subscriber');
            $table->string('webhook_type');
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
        $table_names = config('laravel-webhooks.table_names');

        Schema::dropIfExists($table_names['webhook_subscriptions']);
    }
}
