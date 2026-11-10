
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAdvertiserApiLogsTable extends Migration
{
    public function up()
    {
        Schema::create('advertiser_api_logs', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->unsignedBigInteger('delivery_id');

            $table->unsignedInteger('attempt_number')->default(1);

            $table->string('request_url', 2000)->nullable();
            $table->string('http_method', 10)->nullable();

            $table->longText('request_payload')->nullable();

            $table->unsignedSmallInteger('http_status')->nullable();

            $table->longText('response_body')->nullable();

            $table->boolean('is_success')->default(false);

            $table->text('error_message')->nullable();

            $table->timestamp('attempted_at')->nullable();

            $table->timestamps();

            $table->index(['delivery_id', 'attempt_number']);

            $table->foreign('delivery_id')
                ->references('id')
                ->on('advertiser_lead_deliveries')
                ->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('advertiser_api_logs');
    }
}
