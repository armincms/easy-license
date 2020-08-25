<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateExplanationLicenceProductTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('explanation_licence_product', function (Blueprint $table) {
            $table->bigIncrements('id'); 
            $table->unsignedBigInteger('licence_product_id');
            $table->unsignedBigInteger('explanation_id');

            $table->foreign('explanation_id')->references('id')->on('explanations')
                    ->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('licence_product_id')->references('id')->on('licence_products')
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
        Schema::dropIfExists('explanation_licence_product');
    }
}
