<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('blog_documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('blog_id')->constrained()->onDelete('cascade');
            $table->string('document_path');
            $table->string('document_name');
            $table->unsignedBigInteger('document_size');
            $table->string('document_type');
            $table->timestamps();
            
            $table->index('blog_id');
        });
    }

    public function down()
    {
        Schema::dropIfExists('blog_documents');
    }
};
