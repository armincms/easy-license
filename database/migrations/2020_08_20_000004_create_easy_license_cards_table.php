<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration; 

class CreateEasyLicenseCardsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('el_cards', function (Blueprint $table) {
            $table->bigIncrements('id');   
            $table->json('name');  
            $table->auth();       
            $table->boolean('marked_as')->default(0);  
            $table->unsignedBigInteger('license_id')->index();   
            $table->softDeletes();  
            $table->timestamps();

            $table
                ->foreign('license_id')
                ->references('id')->on('el_licenses')
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
        Schema::dropIfExists('el_cards');
    }
}
