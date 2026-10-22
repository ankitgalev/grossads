<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMetaLeadLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('meta_lead_logs', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->unsignedBigInteger('integration_id');
            $table->string('meta_lead_id', 100)->unique();
            $table->string('page_id', 100)->nullable();
            $table->string('form_id', 100)->nullable();

            $table->unsignedBigInteger('lead_id')->nullable();

            $table->enum('status', [
                'received',
                'processing',
                'processed',
                'failed'
            ])->default('received');

            $table->text('error_message')->nullable();
            $table->timestamps();

            $table->foreign('integration_id')
                ->references('id')->on('meta_integrations')
                ->onDelete('cascade');

            $table->foreign('lead_id')
                ->references('id')->on('leads')
                ->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::dropIfExists('meta_lead_logs');
    }
}
