<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() : void
    {
        Schema::create('comment_rates', function (Blueprint $table) {
            $table->id();
            $table->enum('user_role', [
                'teacher',
                'student',
            ]);
            $table->tinyInteger('value');
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('comment_id');
            $table->unique(['user_role', 'user_id', 'comment_id']);
            $table->foreign('comment_id')
                ->references('id')
                ->on('comments')
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
        Schema::dropIfExists('comment_rates');
    }
};
