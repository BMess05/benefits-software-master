<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddReductionAfterRetirement extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('FEGLI', function (Blueprint $table) {
            $table->string('basicReductionAfterRetirement')->nullable();
            $table->string('optionAReductionAfterRetirement')->nullable();
            $table->string('optionBReductionAfterRetirement')->nullable();
            $table->string('optionCReductionAfterRetirement')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('FEGLI', function (Blueprint $table) {
            $table->dropColumn(['basicReductionAfterRetirement', 'optionAReductionAfterRetirement', 'optionBReductionAfterRetirement', 'optionCReductionAfterRetirement']);
        });
    }
}
