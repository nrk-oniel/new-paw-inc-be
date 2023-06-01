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
            $table->string('username');
            $table->string('email');
            $table->string('password');
            $table->string('phone_number')->default('');
            $table->string('address')->default('');
            $table->unsignedBigInteger('clinic_id')->nullable();
            $table->unsignedBigInteger('role_id')->default(\App\Models\Role::ROLE_CUSTOMER);

            $table->foreign('clinic_id')->references('id')->on('clinics')->cascadeOnDelete();
            $table->foreign('role_id')->references('id')->on('roles');
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
