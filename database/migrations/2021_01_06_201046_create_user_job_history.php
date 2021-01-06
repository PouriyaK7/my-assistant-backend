<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserJobHistory extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_job_history', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('roles');
            $table->string('start_year');
            $table->string('end_year');
            $table->string('start_month');
            $table->string('end_month');
            $table->text('awards');
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
        Schema::dropIfExists('user_job_history');
    }
}
