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
    public function up()
    {
        Schema::create('comments', function (Blueprint $table) {
            $table->id();
            $table->string('content');
            $table->integer('rate');
            $table->enum('user_role', [
                'teacher',
                'student',
            ]);
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('parent_id');
            $table->unsignedBigInteger('question_id');
            $table->foreign('parent_id')
                ->references('id')
                ->on('comments')
                ->onUpdate('cascade')
                ->onDelete('cascade');
            $table->foreign('question_id')
                ->references('id')
                ->on('questions')
                ->onDelete('cascade')
                ->onUpdate('cascade');
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
        Schema::dropIfExists('comments');
    }
};