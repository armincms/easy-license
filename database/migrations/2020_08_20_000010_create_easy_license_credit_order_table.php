<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration; 

class CreateEasyLicenseCreditOrderTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('el_credit_order', function (Blueprint $table) { 
            $table->unsignedBigInteger('credit_id')->index(); 
            $table->unsignedBigInteger('order_id')->index();  

            $table->foreign('credit_id')->references('id')->on('el_credits'); 
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('el_credit_order');
    }
}
