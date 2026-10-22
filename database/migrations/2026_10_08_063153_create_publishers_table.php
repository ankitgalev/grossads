<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePublishersTable extends Migration
{
    public function up()
    {
        Schema::create('publishers', function (Blueprint $table) {

            $table->id();

            /*
            |--------------------------------------------------------------------------
            | Login User
            |--------------------------------------------------------------------------
            */

            $table->unsignedBigInteger('user_id');

            /*
            |--------------------------------------------------------------------------
            | Basic Information
            |--------------------------------------------------------------------------
            */

            $table->string('company_name');

            $table->string('contact_person')
                ->nullable();

            $table->string('phone', 30)
                ->nullable();

            $table->string('website', 500)
                ->nullable();

            /*
            |--------------------------------------------------------------------------
            | Publisher Integration
            |--------------------------------------------------------------------------
            */

            $table->string('callback_url', 1000)
                ->nullable();

            $table->text('notes')
                ->nullable();

            /*
            |--------------------------------------------------------------------------
            | Status
            |--------------------------------------------------------------------------
            */

            $table->tinyInteger('status')
                ->default(1)
                ->comment('1=Active, 0=Inactive');

            $table->timestamps();

            $table->unique('user_id');

            $table->index('status');
        });
    }

    public function down()
    {
        Schema::dropIfExists('publishers');
    }
}