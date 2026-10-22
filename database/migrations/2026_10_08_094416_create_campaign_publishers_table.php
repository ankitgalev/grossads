<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCampaignPublishersTable extends Migration
{
    public function up()
    {
        Schema::create('campaign_publishers', function (Blueprint $table) {

            $table->id();

            $table->unsignedBigInteger('campaign_id');

            $table->unsignedBigInteger('publisher_id');

            $table->decimal(
                'publisher_payout',
                10,
                2
            )->default(0);

            $table->integer(
                'daily_limit'
            )->default(0);

            $table->integer(
                'total_limit'
            )->default(0);

            $table->tinyInteger(
                'status'
            )->default(1);

            $table->timestamps();
            $table->string('api_key', 100)->nullable()->unique();
            $table->string('api_secret', 255)->nullable();
            $table->timestamp('assigned_at')->nullable();

            /*
             * One publisher should only be assigned
             * once to a campaign.
             */
            $table->unique([
                'campaign_id',
                'publisher_id'
            ]);


            $table->foreign(
                'campaign_id'
            )
            ->references('id')
            ->on('campaigns')
            ->onDelete('cascade');


            $table->foreign(
                'publisher_id'
            )
            ->references('id')
            ->on('users')
            ->onDelete('cascade');

        });
    }


    public function down()
    {
        Schema::dropIfExists(
            'campaign_publishers'
        );
    }
}