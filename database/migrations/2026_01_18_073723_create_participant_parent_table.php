<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {

        Schema::create('participant_parent', function (Blueprint $table) {
            $table->id();
            $table->foreignId('participant_id')->constrained('participants')->cascadeOnDelete();
            $table->foreignId('parent_id')->constrained('participants')->cascadeOnDelete();
            $table->unique(['participant_id', 'parent_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('participant_parent');
    }
};
