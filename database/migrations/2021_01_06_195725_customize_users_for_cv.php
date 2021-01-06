<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CustomizeUsersForCv extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->text('bio')->nullable();
            $table->text('address')->nullable();
            $table->text('spotify')->nullable();
            $table->text('discord')->nullable();
            $table->text('instagram')->nullable();
            $table->text('github')->nullable();
            $table->text('gitlab')->nullable();
            $table->string('phone_number')->unique()->nullable();
            $table->text('twitter')->nullable();
            $table->text('telegram')->nullable();
            // 0 => single, 1 => in rel, 2 => married
            $table->integer('relationship_status')->nullable();
            $table->integer('age');
            // 0 => male, 1 => female
            $table->integer('gender');
            $table->text('job_samples')->nullable();
            $table->text('projects')->nullable();
            $table->date('birth_date')->nullable();
            // 0 => mashmool, 1 => moaf tahsili, 2 => moafe ...
            $table->integer('soldiery_situation')->nullable();
            $table->string('status')->nullable();
            $table->string('username')->unique();
            $table->string('education_status')->nullable();
            $table->string('office_number')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            //
        });
    }
}
