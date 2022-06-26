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
        Schema::create('bsc_topics', function (Blueprint $table) {
            $table->id();
            $table->string('username_created',100);
            $table->string('username_updated',100)->nullable();
            $table->string('name');
            $table->timestamps();

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
        Schema::dropIfExists('bsc_topics');
    }
};
