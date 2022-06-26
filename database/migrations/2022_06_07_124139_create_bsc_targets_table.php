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
        Schema::create('bsc_targets', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('target_id')->nullable();
            $table->unsignedBigInteger('topic_id');
            $table->string('username_created',100);
            $table->string('username_updated',100)->nullable();
            $table->integer('order');
            $table->string('name');
            $table->string('comment')->nullable();
            $table->integer('active')->default(1);
            $table->timestamps();

            $table->foreign('target_id')->references('id')->on('bsc_targets')->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreign('topic_id')->references('id')->on('bsc_topics')->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreign('username_created')->references('username')->on('users')->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreign('username_updated')->references('username')->on('users')->cascadeOnDelete()->cascadeOnUpdate();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('bsc_targets');
    }
};
