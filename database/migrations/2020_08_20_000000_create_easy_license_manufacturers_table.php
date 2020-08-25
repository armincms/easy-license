<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEasyLicenseManufacturersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('el_manufacturers', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->hits();
            $table->string('operator')->nullable(); 
            $table->boolean('marked_as')->default(0);   
            $table->auth();
            $table->timestamps();    
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('el_manufacturers');
    }
}
