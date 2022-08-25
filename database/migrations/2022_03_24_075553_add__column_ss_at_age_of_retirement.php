<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnSsAtAgeOfRetirement extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('Employee', function (Blueprint $table) {
            $table->decimal('SSAtAgeOfRetirement', 32, 16)->default(0)->after('SSMonthlyAtStartAge');
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
            $table->dropColumn(['SSAtAgeOfRetirement']);
        });
    }
}
