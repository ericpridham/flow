<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFlowEventsTable extends Migration
{
    public function up()
    {
        Schema::create('flow_events', function (Blueprint $table){
            $table->bigIncrements('id');
            $table->string('request_id');
            $table->string('event_id');
            $table->string('payload_class');
            $table->json('payload_data');
            $table->timestamps();
        });
    }
}
