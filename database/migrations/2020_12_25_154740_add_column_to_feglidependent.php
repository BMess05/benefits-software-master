<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnToFeglidependent extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('FEGLIDependent', function (Blueprint $table) {
            $table->string('DependentName')->nullable()->change();
            $table->date('DateOfBirth')->nullable()->change();
            $table->integer('age')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('FEGLIDependent', function (Blueprint $table) {
            $table->string('DependentName')->change();
            $table->dropColumn(['age']);
        });
    }
}
