<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('category_translations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained()->cascadeOnDelete();
            $table->string('locale', 5);
            $table->string('slug');
            $table->string('name');
            $table->text('description')->nullable();
            $table->timestamps();

            $table->unique(['category_id', 'locale']);
            $table->unique(['category_id', 'slug']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('category_translations');
    }
};
