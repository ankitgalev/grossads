
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAdvertiserLeadDeliveriesTable extends Migration
{
    public function up()
    {
        Schema::create('advertiser_lead_deliveries', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->unsignedBigInteger('lead_id');
            $table->unsignedBigInteger('campaign_id');
            $table->unsignedBigInteger('advertiser_id')->nullable();

            // Delivery configuration snapshot
            $table->enum('delivery_mode', [
                'real_time',
                'cron'
            ]);

            $table->enum('trigger_status', [
                'new',
                'approved',
                'both'
            ])->default('both');

            // pending, processing, success, failed
            $table->enum('status', [
                'pending',
                'processing',
                'success',
                'failed'
            ])->default('pending');

            // Exact payload prepared for advertiser
            $table->longText('request_payload')->nullable();

            $table->unsignedInteger('attempt_count')->default(0);
            $table->unsignedInteger('max_attempts')->default(3);

            $table->unsignedSmallInteger('last_http_status')->nullable();

            $table->longText('last_response')->nullable();
            $table->text('last_error')->nullable();

            $table->timestamp('next_attempt_at')->nullable();
            $table->timestamp('last_attempt_at')->nullable();
            $table->timestamp('locked_at')->nullable();
            $table->timestamp('delivered_at')->nullable();

            $table->timestamps();

            // One delivery record per lead
            $table->unique('lead_id');

            $table->index(['status', 'next_attempt_at']);
            $table->index(['campaign_id', 'status']);
            $table->index('advertiser_id');

            $table->foreign('lead_id')
                ->references('id')
                ->on('leads')
                ->onDelete('cascade');

            $table->foreign('campaign_id')
                ->references('id')
                ->on('campaigns')
                ->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('advertiser_lead_deliveries');
    }
}
