<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bsc_topic_orders', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->unsignedBigInteger('set_indicator_id');
            $table->unsignedBigInteger('topic_id');


            $table->foreign('set_indicator_id')->references('id')->on('bsc_set_indicators')->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreign('topic_id')->references('id')->on('bsc_topics')->cascadeOnDelete()->cascadeOnUpdate();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('bsc_topic_orders');
    }
};
