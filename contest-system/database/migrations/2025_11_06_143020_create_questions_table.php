<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('questions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('contest_id')->constrained('contests')->onDelete('cascade');
            $table->enum('type', ['single', 'multi', 'true_false']);
            $table->text('question_text');
            $table->json('options'); // e.g., ["A"=>"Yes", "B"=>"No"]
            $table->json('correct_answers'); // e.g., ["A"]
            $table->integer('marks')->default(1);
            $table->timestamps();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('questions');
    }
};
