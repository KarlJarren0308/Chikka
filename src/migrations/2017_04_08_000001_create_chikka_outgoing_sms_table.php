<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateChikkaOutgoingSmsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('chikka_outgoing_sms', function (Blueprint $table) {
            $table->increments('id');
            $table->string('message_type');
            $table->string('mobile_number');
            $table->string('request_id')->unique()->nullable();
            $table->foreign('request_id')->references('request_id')->on('chikka_incoming_sms');
            $table->string('message_id');
            $table->string('message');
            $table->string('request_cost')->nullable();
            $table->timestamp('timestamp');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('chikka_outgoing_sms');
    }
}
