<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEmployeesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('employees', function (Blueprint $table) {
            $table->id();
            $table->integer('id_user');
            $table->integer('id_senior');
            $table->integer('id_region');
            $table->integer('id_facility');
            $table->integer('id_department');
            $table->string('title');
            $table->integer('holidays_total');
            $table->integer('sick_total');
            $table->integer('maternity_total');
            $table->integer('paternity_total');
            $table->date('employee_start_date');
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
        Schema::dropIfExists('employees');
    }
}
