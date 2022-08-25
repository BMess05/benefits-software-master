<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableLincomeDistribution extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('lfundDistribution', function (Blueprint $table) {
            $table->increments('id');
            $table->date('date')->nullable();
            $table->string('lfund_type')->nullable();
            $table->float('gfund')->default(0);
            $table->float('ffund')->default(0);
            $table->float('cfund')->default(0);
            $table->float('sfund')->default(0);
            $table->float('ifund')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('lfundDistribution');
    }
}
