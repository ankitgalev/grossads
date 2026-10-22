<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {

            $table->id();

            $table->string('name');

            $table->string('email')->unique();

            $table->timestamp('email_verified_at')
                ->nullable();

            $table->string('password');

            /*
            |--------------------------------------------------------------------------
            | GrossAds Role
            |--------------------------------------------------------------------------
            */

            $table->enum('role', [
                'admin',
                'publisher',
                'advertiser'
            ])->default('publisher');

            /*
            |--------------------------------------------------------------------------
            | Company Information
            |--------------------------------------------------------------------------
            */

            $table->string('company_name')
                ->nullable();

            $table->string('phone', 30)
                ->nullable();

            /*
            |--------------------------------------------------------------------------
            | Account Status
            |--------------------------------------------------------------------------
            */

            $table->tinyInteger('status')
                ->default(1)
                ->comment('1=Active, 0=Inactive');

            $table->rememberToken();

            $table->timestamps();

            $table->index('role');
            $table->index('status');
        });
    }

    public function down()
    {
        Schema::dropIfExists('users');
    }
}