<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTrainsTable extends Migration
{
    public function up()
    {
        Schema::create('trains', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('requestid')->nullable();
            $table->string('title');
            $table->string('status')->nullable();
            $table->longText('diffusers_lora_file')->nullable();
            $table->longText('config_file')->nullable();
            $table->string('file_size')->nullable();
            $table->string('error_log')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }
}
