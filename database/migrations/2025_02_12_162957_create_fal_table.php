<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFalsTable extends Migration
{
    public function up()
    {
        Schema::create('fal', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('title');
            $table->string('model_name');
            $table->string('model_type');
            $table->string('base_url')->nullable();
            $table->longText('payload')->nullable();
            $table->string('icon')->nullable();
            $table->boolean('enabled')->default(0)->nullable();
            $table->string('file_type')->nullable();
            $table->timestamps();
            $table->softDeletes();
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
