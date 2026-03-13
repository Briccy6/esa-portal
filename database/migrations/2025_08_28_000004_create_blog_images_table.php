<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('blog_images', function (Blueprint $table) {
            $table->id();
            $table->foreignId('blog_id')->constrained()->onDelete('cascade');
            $table->string('image_path');
            $table->string('image_alt')->nullable();
            $table->text('image_caption')->nullable();
            $table->integer('sort_order')->default(0);
            $table->timestamps();
            
            $table->index(['blog_id', 'sort_order']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('blog_images');
    }
};
