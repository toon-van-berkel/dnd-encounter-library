<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    // Build/Run migration
    public function up(): void {
        Schema::create('encounters', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')->constrained();
            $table->foreignId('type_id')->constrained('encounter_types');
            $table->foreignId('difficulty_id')->constrained('encounter_difficulties');

            $table->string('title', 200);
            $table->text('description');

            $table->unsignedTinyInteger('min_level');
            $table->unsignedTinyInteger('max_level');
            $table->unsignedTinyInteger('party_size');

            $table->string('image_path')->nullable();
            $table->boolean('is_active')->default(true);

            $table->timestamps();
        });
    }

    // Undo the migration
    public function down(): void {
        Schema::dropIfExists('encounters');
    }
};
