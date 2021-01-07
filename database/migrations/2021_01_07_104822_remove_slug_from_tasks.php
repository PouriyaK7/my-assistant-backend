<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RemoveSlugFromTasks extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tasks', function (Blueprint $table) {
            $table->dropColumn('slug');
            $table->dropColumn('has_sub_task');
            $table->dropColumn('is_sub_task');
            $table->bigInteger('is_subtask')->nullable();
            $table->bigInteger('user');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('tasks', function (Blueprint $table) {
            $table->string('slug')->unique();
            $table->integer('is_sub_task')->nullable();
            $table->integer('has_sub_task')->default(0);
            $table->dropColumn('is_subtask');
            $table->dropColumn('user');
        });
    }
}
