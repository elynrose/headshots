<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddModelRelationshipFieldsToGeneratesTable extends Migration
{
    public function up()
    {
        Schema::table('Generates', function (Blueprint $table) {
            $table->unsignedBigInteger('fal_model_id')->nullable();
            $table->foreign('fal_model_id', 'fal_fk_10415034')->references('id')->on('fal');
        });
    }
}
