<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration; 

class CreateEasyLicenseCreditsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('el_credits', function (Blueprint $table) {
            $table->bigIncrements('id');    
            $table->json('data')->nullable();   
            $table->auth();
            $table->unsignedBigInteger('license_id')->index(); 
            $table->timestamp('expires_on')->nullable();
            $table->timestamps();
            $table->softDeletes();  

            $table->foreign('license_id')->references('id')->on('el_licenses'); 
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('el_credits');
    }
}
