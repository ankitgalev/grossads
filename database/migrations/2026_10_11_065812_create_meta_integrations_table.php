<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMetaIntegrationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('meta_integrations', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->string('name')->default('Meta Lead Ads');
            $table->string('page_id', 100);
            $table->string('form_id', 100)->nullable();

            $table->unsignedBigInteger('campaign_id');
            $table->unsignedBigInteger('publisher_id');
            $table->unsignedBigInteger('campaign_publisher_id')->nullable();

            // These secrets will be encrypted by the model.
            $table->text('app_secret');
            $table->text('page_access_token');
            $table->text('verify_token');

            $table->boolean('status')->default(false);
            $table->timestamps();

            $table->index(['page_id', 'form_id']);

            $table->foreign('campaign_id')
                ->references('id')->on('campaigns')
                ->onDelete('cascade');

            $table->foreign('publisher_id')
                ->references('id')->on('users')
                ->onDelete('cascade');

            $table->foreign('campaign_publisher_id')
                ->references('id')->on('campaign_publishers')
                ->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::dropIfExists('meta_integrations');
    }
}
