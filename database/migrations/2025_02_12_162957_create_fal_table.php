<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFalTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('fal', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('model_name');
            $table->string('model_type');
            $table->string('base_url');
            $table->string('payload');
            $table->string('icon');
            $table->string('file_type')->nullable();
            $table->boolean('enabled')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fal');
    }
};
