<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddRelationshipFieldsToGeneratesTable extends Migration
{
    public function up()
    {
        Schema::table('generates', function (Blueprint $table) {
            $table->unsignedBigInteger('train_id')->nullable();
            $table->foreign('train_id', 'train_fk_10415034')->references('id')->on('trains');
            $table->unsignedBigInteger('user_id')->nullable();
            $table->foreign('user_id', 'user_fk_10415030')->references('id')->on('users');
        });
    }
}
