
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddAdvertiserDeliverySettingsToCampaignsTable extends Migration
{
    public function up()
    {
        Schema::table('campaigns', function (Blueprint $table) {

            // Enable or disable advertiser lead forwarding
            $table->boolean('advertiser_delivery_enabled')
                ->default(false)
                ->after('postback_url');

            // real_time = send immediately
            // cron = send through scheduled batches
            $table->enum('advertiser_delivery_mode', [
                'real_time',
                'cron'
            ])->default('real_time');

            // Campaign-level forwarding rule
            $table->enum('advertiser_delivery_status', [
                'new',
                'approved',
                'both'
            ])->default('both');

            // Campaign-specific advertiser API
            $table->text('advertiser_api_url')->nullable();

            $table->enum('advertiser_http_method', [
                'GET',
                'POST',
                'PUT',
                'PATCH'
            ])->default('POST');

            // json = application/json
            // form = application/x-www-form-urlencoded
            // query = URL query parameters
            $table->enum('advertiser_payload_format', [
                'json',
                'form',
                'query'
            ])->default('json');

            // Authentication configuration
            $table->enum('advertiser_auth_type', [
                'none',
                'bearer',
                'api_key',
                'basic',
                'custom_headers'
            ])->default('none');

            // Stores authentication values securely at application level
            //$table->json('advertiser_auth_config')->nullable();

            // Maps GrossAds campaign fields to advertiser API fields
            //$table->json('advertiser_field_mapping')->nullable();

            $table->longText('advertiser_auth_config')->nullable();
            $table->longText('advertiser_field_mapping')->nullable();
            $table->unsignedSmallInteger('advertiser_timeout')
                ->default(15);

            $table->unsignedTinyInteger('advertiser_retry_attempts')
                ->default(3);

            $table->unsignedSmallInteger('advertiser_cron_batch_size')
                ->default(100);
        });
    }

    public function down()
    {
        Schema::table('campaigns', function (Blueprint $table) {
            $table->dropColumn([
                'advertiser_delivery_enabled',
                'advertiser_delivery_mode',
                'advertiser_delivery_status',
                'advertiser_api_url',
                'advertiser_http_method',
                'advertiser_payload_format',
                'advertiser_auth_type',
                'advertiser_auth_config',
                'advertiser_field_mapping',
                'advertiser_timeout',
                'advertiser_retry_attempts',
                'advertiser_cron_batch_size'
            ]);
        });
    }
}
