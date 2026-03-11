<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('product_translations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->string('locale', 5);
            $table->string('title');
            $table->string('slug');
            $table->text('description')->nullable();
            $table->text('content')->nullable();
            $table->timestamps();

            $table->unique(['product_id', 'locale']);
            $table->unique(['product_id', 'slug']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_translations');
    }
};
