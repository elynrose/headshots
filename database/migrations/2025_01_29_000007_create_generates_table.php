<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGeneratesTable extends Migration
{
    public function up()
    {
        Schema::create('generates', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->longText('prompt')->nullable();
            $table->integer('width')->nullable();
            $table->integer('height')->nullable();
            $table->string('status')->nullable();
            $table->longText('image_url')->nullable();
            $table->string('content_type')->nullable();
            $table->string('inference')->nullable();
            $table->string('seed')->nullable();
            $table->integer('credit')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }
}
