<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration; 

class AddFeaturesColumnToManufacturersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('el_manufacturer_translations', function (Blueprint $table) {
            $table->json('features')->nullable();   
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('el_manufacturer_translations', function (Blueprint $table) {
            $table->dropColumn('features');   
        });
    }
}
