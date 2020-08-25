<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLicenceAccessesMetaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('licence_accesses_meta', function (Blueprint $table) { 
            $table->unsignedBigInteger("licence_access_id")->index();

            $table->string('type')->default('null'); 
            $table->string('key')->index();
            $table->text('value')->nullable();

            $table->increments('id'); 
            $table->timestamps();

            $table->foreign("licence_access_id")->references('id')->on('licence_accesses')
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
        Schema::drop('licence_accesses_meta');
    }
}
