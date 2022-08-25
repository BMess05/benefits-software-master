<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeDefaultValuesForParttimeservices extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('PartTimeService', function (Blueprint $table) {
            $table->dateTime('FromDate')->nullable()->change();
            $table->dateTime('ToDate')->nullable()->change();
            $table->decimal('HoursWeek', 32,16)->default(0)->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('PartTimeService', function (Blueprint $table) {
            //
        });
    }
}
