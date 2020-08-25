<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLicenceAccessesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('licence_accesses', function (Blueprint $table) { 
            $table->bigIncrements('id');   
            $table->unsignedBigInteger('licence_id');    
            $table->timestamp('expiration_date')->nullable(); 
            $table->unsignedInteger('interval')->default(0); // for save duration timestamps    
            $table->text('data')->nullable(); 
            $table->string('description', 500)->nullable();
            $table->ownables();   
            $table->softDeletes(); 
            $table->timestamps(); 

            $table->foreign('licence_id')->references('id')->on('licences')
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
        Schema::drop('licence_accesses');
    }
}
