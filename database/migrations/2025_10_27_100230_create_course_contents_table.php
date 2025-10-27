<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('course_contents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('module_id')->constrained('course_modules')->onDelete('cascade');
            $table->string('title');
            $table->enum('type', ['video', 'text', 'image', 'link', 'document']);
            $table->text('content')->nullable();
            $table->enum('video_source_type', ['youtube', 'upload'])->nullable();
            $table->string('video_url')->nullable();
            $table->string('video_file')->nullable();
            $table->string('video_length')->nullable();
            $table->string('file')->nullable();
            $table->string('link_url')->nullable();
            $table->integer('order')->default(0);

            $table->enum('status', ['Active', 'Inactive', 'Deleted'])->default('Active');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down()
    {
        Schema::dropIfExists('course_contents');
    }
};