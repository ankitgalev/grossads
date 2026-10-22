<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCampaignsTable extends Migration
{
    public function up()
    {
        Schema::create('campaigns', function (Blueprint $table) {
            $table->id();

            $table->string('name');
            $table->string('slug')->unique();

            $table->text('description')->nullable();
            $table->unsignedBigInteger('advertiser_id');
            $table->enum('campaign_type', [
                'lead',
                'call',
                'survey',
                'other'
            ])->default('lead');

            $table->enum('status', [
                'active',
                'inactive',
                'paused'
            ])->default('active');

            $table->decimal('publisher_payout', 12, 2)->default(0);
            $table->decimal('advertiser_price', 12, 2)->default(0);

            $table->string('api_key')->nullable()->unique();

            $table->text('postback_url')->nullable();

            $table->timestamps();
            $table->foreign('advertiser_id')
                ->references('id')
                ->on('advertisers')
                ->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('campaigns');
    }
}