<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('product_identity');
            $table->string('title');
            $table->text('shopee_link');
            $table->text('video_link');
            $table->text('description')->nullable();
            $table->text('description_ai')->nullable();
            $table->json('tags')->nullable();
            $table->longText('raw_html')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
