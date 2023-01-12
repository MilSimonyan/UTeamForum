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
        Schema::create('post_rates', function (Blueprint $table) {
            $table->id();
            $table->integer('like');
            $table->enum('user_role', [
                'teacher',
                'student',
            ]);
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('post_id');
            $table->unique(['user_role', 'user_id', 'post_id']);
            $table->foreign('post_id')
                ->references('id')
                ->on('posts')
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
    public function down()
    {
        Schema::dropIfExists('post_rates');
    }
};
