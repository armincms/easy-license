<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration; 

class CreateEasyLicenseManualsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('el_manuals', function (Blueprint $table) {
            $table->bigIncrements('id');    
            $table->json('data')->nullable();
            $table->auth();       
            $table->boolean('sold')->default(0);  
            $table->unsignedBigInteger('card_id')->index();   
            $table->softDeletes();  
            $table->timestamps();
            $table->timestamp('sold_at')->nullable();

            $table
                ->foreign('card_id')
                ->references('id')->on('el_cards')
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
        Schema::dropIfExists('el_manuals');
    }
}
