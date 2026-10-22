<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAdvertisersTable extends Migration
{
    public function up()
    {
        Schema::create('advertisers', function (Blueprint $table) {

            $table->id();

            /*
            |--------------------------------------------------------------------------
            | Login User
            |--------------------------------------------------------------------------
            */

            $table->unsignedBigInteger('user_id');

            /*
            |--------------------------------------------------------------------------
            | Company Information
            |--------------------------------------------------------------------------
            */

            $table->string('company_name');

            $table->string('contact_person')
                ->nullable();

            $table->string('phone', 30)
                ->nullable();

            $table->string('website', 500)
                ->nullable();

            $table->string('industry')
                ->nullable();

            /*
            |--------------------------------------------------------------------------
            | Address
            |--------------------------------------------------------------------------
            */

            $table->text('address')
                ->nullable();

            $table->string('city')
                ->nullable();

            $table->string('state')
                ->nullable();

            $table->string('country')
                ->default('India');

            $table->string('pincode', 20)
                ->nullable();

            /*
            |--------------------------------------------------------------------------
            | Notes
            |--------------------------------------------------------------------------
            */

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
        Schema::dropIfExists('advertisers');
    }
}