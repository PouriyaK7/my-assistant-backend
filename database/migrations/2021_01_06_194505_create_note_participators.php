<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateNoteParticipators extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('note_participators', function (Blueprint $table) {
            $table->bigInteger('user');
            $table->bigInteger('note');
            // 1 => read only, 2 => write
            $table->integer('permission');
            $table->primary(['user', 'note']);
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
        Schema::dropIfExists('note_participators');
    }
}
