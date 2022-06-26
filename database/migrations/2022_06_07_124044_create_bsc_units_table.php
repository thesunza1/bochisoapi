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
        Schema::create('bsc_units', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('unit_id')->nullable();
            $table->string('name')->nullable();
            $table->string('username_created', 100)->nullable();
            $table->timestamps();

            $table->foreign('unit_id')->references('id')->on('bsc_units')->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreign('username_created')->references('username')->on('users')->cascadeOnDelete()->cascadeOnUpdate();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('bsc_units');
    }
};
