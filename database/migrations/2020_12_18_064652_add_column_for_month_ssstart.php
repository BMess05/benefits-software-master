<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnForMonthSsstart extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('Employee', function (Blueprint $table) {
            $table->renameColumn('SSStartAge', 'SSStartAge_year');
            $table->integer('SSStartAge_month')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('Employee', function (Blueprint $table) {
            $table->renameColumn('SSStartAge_year', 'SSStartAge');
            $table->dropColumn(['SSStartAge_month']);
        });
    }
}
