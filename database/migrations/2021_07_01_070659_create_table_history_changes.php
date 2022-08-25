<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableHistoryChanges extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('history_changes', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('emp_id');
            $table->integer('column_id');
            $table->integer('row_id')->default(0);
            $table->string('old_val')->nullable();
            $table->string('new_val')->nullable();
            $table->integer('updated_by');
            $table->timestamps();
        });

        // Schema::table('history_changes', function (Blueprint $table) {
        //     $table->foreign('emp_id')->references('EmployeeId')->on('Employee');
        //     $table->foreign('column_id')->references('id')->on('history_columns');
        // });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('history_changes');
    }
}
