<?php

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
        Schema::create('purchases', function (Blueprint $table) {
            $table->id();
            $table->markable()->default('pending');
            $table->unsignedMediumInteger('number')->unique()->index();
            $table->text('note')->nullable();
            $table->price('amount');
            $table->tinyInteger('count')->unsigned()->default(1);
            $table->currency();
            $table->details();
            // invoice and error handler
            $table->foreignIdFor(\Zareismail\Gutenberg\Models\GutenbergFragment::class)->nullable();
            // tos store purchase detail
            $table->foreignIdFor(\Armincms\EasyLicense\Models\License::class)->nullable()->constrained();
            $table->foreignIdFor(\Armincms\EasyLicense\Models\Card::class)->nullable()->constrained();
            // to store customer detail
            $table->foreignIdFor(config('auth.providers.users.model'))->nullable()->constrained();
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
        Schema::dropIfExists('purchases');
    }
};
