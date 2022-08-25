<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class TspCalculate extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tsp_calculate', function (Blueprint $table) {
            $table->increments('id');
            $table->string('year')->nullable();
            $table->string('g')->nullable();
            $table->string('f')->nullable();
            $table->string('c')->nullable();
            $table->string('s')->nullable();
            $table->string('i')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tsp_calculate');
    }
}
