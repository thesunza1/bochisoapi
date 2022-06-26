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
        Schema::create('bsc_detail_set_indicators', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('set_indicator_id');
            $table->string('username_created', 100);
            $table->string('username_updated', 100)->nullable();
            $table->integer('total_plan')->default(0);
            $table->timestamps();


            $table->foreign('set_indicator_id')->references('id')->on('bsc_set_indicators')->cascadeOnDelete()->cascadeOnUpdate();
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
        Schema::dropIfExists('bsc_detail_set_indicators');
    }
};
