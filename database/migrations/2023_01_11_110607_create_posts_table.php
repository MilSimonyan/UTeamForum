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
        Schema::create('posts', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('content');
            $table->string('media')->default(null)->nullable();
            $table->enum('user_role', [
                'teacher',
                'student',
            ]);
            $table->json('author');
            $table->unsignedInteger('likes')->default(0);
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('course_id');
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
        Schema::dropIfExists('posts');
    }
};
