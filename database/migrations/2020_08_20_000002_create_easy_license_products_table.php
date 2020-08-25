<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEasyLicenseProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('el_products', function (Blueprint $table) {
            $table->bigIncrements('id');  
            $table->json('name')->nullable();
            $table->json('abstract')->nullable();
            $table->string('driver')->nullable(); 
            $table->boolean('marked_as')->default(0);    
            $table->json('fields')->nullable();    
            $table->auth();
            $table->timestamps();    
            $table->softDeletes();
            $table->unsignedBigInteger('manufacturer_id')->index(); 

            $table->foreign('manufacturer_id')
                ->references('id')
                ->on('el_manufacturers');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('el_products');
    }
}
