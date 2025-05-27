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
    Schema::create('actual_loads', function (Blueprint $table) {
        $table->id();
        $table->foreignId('planned_load_id')->nullable()->constrained()->nullOnDelete();
        $table->foreignId('teacher_id')->constrained('users')->cascadeOnDelete();
        $table->foreignId('discipline_id')->constrained()->cascadeOnDelete();
        $table->foreignId('group_id')->constrained()->cascadeOnDelete();
        $table->string('type');
        $table->integer('hours');
        $table->string('semester');
        $table->year('year');
        $table->text('comment')->nullable();
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('actual_loads');
    }
};
