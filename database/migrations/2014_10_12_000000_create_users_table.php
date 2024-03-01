<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->integer('id_senior');
            $table->integer('id_region');
            $table->integer('id_facility');
            $table->integer('id_department');
            $table->string('name');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->string('title');
            $table->date('employee_start_date');
            $table->integer('holiday_total');
            $table->integer('sick_total');
            $table->integer('maternity_total');
            $table->integer('paternity_total');
            $table->integer('user_role');
            $table->string('mobile_number');
            $table->string('personal_email');
            $table->string('emergency_contact_name');
            $table->string('emergency_contact_number');
            $table->string('profile_picture');
            $table->string('address');
            $table->string('bio');
            $table->rememberToken();
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
        Schema::dropIfExists('users');
    }
}
