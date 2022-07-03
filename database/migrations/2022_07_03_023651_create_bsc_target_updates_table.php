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
        Schema::create('bsc_target_updates', function (Blueprint $table) {
            $table->id();
            $table->string('username', 100);
            $table->unsignedBigInteger('target_id');
            $table->integer('role')->default(1);
            $table->timestamps();

            $table->foreign('username')->references('username')->on('users')->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreign('target_id')->references('id')->on('bsc_targets')->cascadeOnDelete()->cascadeOnUpdate();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('bsc_target_updates');
    }
};
