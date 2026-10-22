<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCampaignFieldsTable extends Migration
{
    public function up()
    {
        Schema::create('campaign_fields', function (Blueprint $table) {

            $table->id();

            $table->unsignedBigInteger('campaign_id');

            $table->string('field_name');
            $table->string('field_key');

            $table->enum('field_type', [
                'text',
                'email',
                'phone',
                'number',
                'date',
                'select',
                'textarea'
            ])->default('text');

            $table->text('options')->nullable();

            $table->boolean('is_required')->default(false);

            $table->integer('sort_order')->default(0);

            $table->timestamps();

            $table->foreign('campaign_id')
                ->references('id')
                ->on('campaigns')
                ->onDelete('cascade');

            $table->index([
                'campaign_id',
                'field_key'
            ]);
        });
    }

    public function down()
    {
        Schema::dropIfExists('campaign_fields');
    }
}