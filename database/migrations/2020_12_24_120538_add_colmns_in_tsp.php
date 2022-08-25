<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColmnsInTsp extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('TSP', function (Blueprint $table) {
            $table->decimal('L2025')->after('L2020')->default(0);
            $table->decimal('L2035')->after('L2030')->default(0);
            $table->decimal('L2045')->after('L2040')->default(0);
            $table->decimal('L2055')->after('L2050')->default(0);
            $table->decimal('L2060')->after('L2055')->default(0);
            $table->decimal('L2065')->after('L2060')->default(0);
            $table->decimal('L2025Dist')->after('L2020Dist')->default(0);
            $table->decimal('L2035Dist')->after('L2030Dist')->default(0);
            $table->decimal('L2045Dist')->after('L2040Dist')->default(0);
            $table->decimal('L2055Dist')->after('L2050Dist')->default(0);
            $table->decimal('L2060Dist')->after('L2055Dist')->default(0);
            $table->decimal('L2065Dist')->after('L2060Dist')->default(0);
            $table->decimal('loan_balance_general')->default(0);
            $table->decimal('loan_repayment_general')->default(0);
            $table->dateTime('payoff_date_general')->nullable();
            $table->decimal('loan_balance_residential')->default(0);
            $table->decimal('loan_repayment_residential')->default(0);
            $table->dateTime('payoff_date_residential')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('TSP', function (Blueprint $table) {
            $table->dropColumn(['L2025', 'L2035', 'L2045', 'L2055', 'L2060', 'L2065', 'L2025Dist', 'L2035Dist', 'L2045Dist', 'L2055Dist', 'L2060Dist', 'L2065Dist']);
        });
    }
}
