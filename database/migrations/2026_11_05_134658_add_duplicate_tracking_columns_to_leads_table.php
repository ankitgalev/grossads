<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDuplicateTrackingColumnsToLeadsTable extends Migration
{
    public function up()
    {
        Schema::table('leads', function (Blueprint $table) {
            $table->boolean('is_duplicate')
                ->default(false)
                ->after('status');

            $table->unsignedBigInteger('duplicate_of_lead_id')
                ->nullable()
                ->after('is_duplicate');

            $table->string('duplicate_remark', 500)
                ->nullable()
                ->after('duplicate_of_lead_id');

            $table->index(
                'duplicate_of_lead_id',
                'leads_duplicate_of_lead_id_index'
            );
        });
    }

    public function down()
    {
        Schema::table('leads', function (Blueprint $table) {
            $table->dropIndex('leads_duplicate_of_lead_id_index');

            $table->dropColumn([
                'is_duplicate',
                'duplicate_of_lead_id',
                'duplicate_remark',
            ]);
        });
    }
}