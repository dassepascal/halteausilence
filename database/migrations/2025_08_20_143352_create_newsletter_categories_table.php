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
        Schema::create('newsletter_categories', function (Blueprint $table) {
         $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->string('color')->default('#3B82F6');
            $table->boolean('active')->default(true);
            $table->timestamps();
        });
         // Relation many-to-many entre newsletters et catégories
        Schema::create('newsletter_category_pivot', function (Blueprint $table) {
            $table->id();
            $table->foreignId('newsletter_id')->constrained()->onDelete('cascade');
            $table->foreignId('newsletter_category_id')->constrained()->onDelete('cascade');
            $table->timestamps();

            // Correction du nom de l'index unique pour éviter la limite MySQL
            $table->unique(['newsletter_id', 'newsletter_category_id'], 'newsletter_cat_pivot_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('newsletter_category_pivot');
        Schema::dropIfExists('newsletter_categories');
        Schema::dropIfExists('newsletter_subscribers');
        Schema::dropIfExists('newsletters');
    }
};
