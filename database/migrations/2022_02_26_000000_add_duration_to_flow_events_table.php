<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDurationToFlowEventsTable extends Migration
{
    public function up()
    {
        Schema::table('flow_events', function (Blueprint $table) {
            $table->integer('duration_ms')->default(0);
        });
    }

    public function down()
    {
        Schema::table('flow_events', function (Blueprint $table) {
            $table->drop('duration_ms');
        });
    }
}
