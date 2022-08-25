<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnsToAdvisors extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('Advisor', function (Blueprint $table) {
            $table->string('company_name')->nullable();
            $table->string('workshop_code')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('Advisor', function (Blueprint $table) {
            $table->dropColumn(['company_name', 'workshop_code']);
        });
    }
}
