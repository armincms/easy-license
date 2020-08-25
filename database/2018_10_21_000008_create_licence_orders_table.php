<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLicenceOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('licence_orders', function (Blueprint $table) { 
            $table->increments('id'); 
            $table->timestamps(); 
            $table->softDeletes(); 
            $table->string('status')->default('init');       
            $table->ownables();       
            $table->text('data')->nullable();   
            $table->integer('count')->default(1);   
            $table->unsignedInteger('licence_id'); 
            $table->unsignedInteger('transaction_id')->nullable(); 

            $table->foreign('licence_id')->references('id')->on('licences');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('licence_orders');
    }
}
