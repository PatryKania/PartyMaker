<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('event_pages', function (Blueprint $table) {
            $table->id();
             $table->foreignId('event_id')->constrained()->cascadeOnDelete();
            $table->string('slug')->unique();
            $table->string('main_banner')->nullable();
            $table->longText('content')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('event_pages');
    }
};