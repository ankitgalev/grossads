<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLeadValuesTable extends Migration
{
    public function up()
    {
        Schema::create('lead_values', function (Blueprint $table) {

            $table->id();

            $table->unsignedBigInteger('lead_id');

            $table->unsignedBigInteger('campaign_field_id');

            $table->text('field_value')->nullable();

            $table->timestamps();

            $table->foreign('lead_id')
                ->references('id')
                ->on('leads')
                ->onDelete('cascade');

            $table->foreign('campaign_field_id')
                ->references('id')
                ->on('campaign_fields')
                ->onDelete('cascade');

            $table->unique([
                'lead_id',
                'campaign_field_id'
            ]);
        });
    }

    public function down()
    {
        Schema::dropIfExists('lead_values');
    }
}