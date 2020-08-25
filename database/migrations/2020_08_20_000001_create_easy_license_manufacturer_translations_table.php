<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEasyLicenseManufacturerTranslationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('el_manufacturer_translations', function (Blueprint $table) {
            $table->bigIncrements('id'); 
            $table->description();
            $table->unsignedBigInteger('manufacturer_id')->index(); 
            $table->timestamps();    
            $table->softDeletes();


            $table
                ->foreign('manufacturer_id')
                ->references('id')->on('el_manufacturers')
                ->onDelete('cascade')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('el_manufacturer_translations');
    }
}
