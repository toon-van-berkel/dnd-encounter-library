<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    // Build/Run migration
    public function up(): void {
        Schema::create('encounter_difficulties', function (Blueprint $table) {
            $table->id();
            $table->string('name', 50)->unique();
            $table->timestamps();
        });
    }

    // Undo the migration
    public function down(): void {
        Schema::dropIfExists('encounter_difficulties');
    }
};
