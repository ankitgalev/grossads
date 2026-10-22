<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLeadsTable extends Migration
{
    public function up()
    {
        Schema::create('leads', function (Blueprint $table) {

            $table->id();

            $table->unsignedBigInteger('campaign_id');

            $table->unsignedBigInteger('publisher_id')->nullable();

            $table->unsignedBigInteger('advertiser_id')->nullable();

            $table->string('lead_reference')->unique();

            $table->string('status')->default('new');

            $table->string('source')->nullable();

            $table->string('ip_address')->nullable();

            $table->text('user_agent')->nullable();

            $table->timestamp('submitted_at')->nullable();

            $table->timestamps();

            $table->foreign('campaign_id')
                ->references('id')
                ->on('campaigns')
                ->onDelete('cascade');

            $table->index('publisher_id');
            $table->index('advertiser_id');
            $table->index('status');
        });
    }

    public function down()
    {
        Schema::dropIfExists('leads');
    }
}