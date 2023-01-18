<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() : void
    {
        Schema::create('question_likes', function (Blueprint $table) {
            $table->id();
            $table->enum('user_role', [
                'teacher',
                'student',
            ]);
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('question_id');
            $table->unique(['user_role', 'user_id', 'question_id']);
            $table->foreign('question_id')
                ->references('id')
                ->on('questions')
                ->onUpdate('cascade')
                ->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() : void
    {
        Schema::dropIfExists('question_rates');
    }
};
