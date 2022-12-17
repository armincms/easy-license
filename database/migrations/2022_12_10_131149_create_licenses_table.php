<?php

use Armincms\EasyLicense\Nova\License;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('licenses', function (Blueprint $table) {
            $deliveryMethods = array_keys(License::deliveryMethods());

            $table->id();
            $table->json('name')->nullable();
            $table->price();
            $table->enum('delivery', $deliveryMethods)->default($deliveryMethods[0]);
            $table->unsignedInteger('users')->default(1);
            $table->boolean('enable')->default(0);
            $table->configuration();
            $table->foreignIdFor(\Armincms\Duration\Models\Duration::class)->constrained();
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
        Schema::dropIfExists('licenses');
    }
};
