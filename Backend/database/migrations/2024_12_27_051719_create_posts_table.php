<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('posts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('subject_id')->constrained()->cascadeOnDelete(); // Added subject_id
            $table->foreignId('chapter_id')->constrained()->cascadeOnDelete(); // Added chapter_id
            $table->foreignId('question_id')->nullable()->constrained()->nullOnDelete(); // Made question_id nullable
            $table->text('post_text');
            $table->text('text');
            $table->json('media_url')->nullable(); // Changed media_url to JSON as required
            $table->timestamps();
        });

    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('posts');
    }
};
