<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Armincms\EasyLicense\Nova\License;

class CreateEasyLicenseLicensesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('el_licenses', function (Blueprint $table) {
            $table->bigIncrements('id');   
            $table->json('name')->nullable(); 
            $table->json('abstract')->nullable(); 
            $table->price();        
            $table->discount();       
            $table->auth();       
            $table->enum('delivery', array_keys(License::deliveryMethods()))
                    ->default('system');
            $table->unsignedInteger('users')->default(1);  
            $table->boolean('marked_as')->default(0);  
            $table->unsignedBigInteger('product_id')->index();  
            $table->unsignedBigInteger('duration_id')->index();  
            $table->softDeletes();  
            $table->timestamps();

            $table
                ->foreign('product_id')
                ->references('id')->on('el_products')
                ->onDelete('cascade')->onUpdate('cascade');

            $table
                ->foreign('duration_id')
                ->references('id')->on('durations')
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
        Schema::dropIfExists('el_licenses');
    }
}
