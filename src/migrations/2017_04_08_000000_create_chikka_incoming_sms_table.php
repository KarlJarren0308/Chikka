<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateChikkaIncomingSmsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('chikka_incoming_sms', function (Blueprint $table) {
            $table->increments('id');
            $table->string('mobile_number');
            $table->string('request_id')->unique();
            $table->string('message');
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
        Schema::drop('chikka_incoming_sms');
    }
}
