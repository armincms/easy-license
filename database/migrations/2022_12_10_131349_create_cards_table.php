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
        Schema::create('cards', function (Blueprint $table) {
            $table->id();
            $table->resourceSummary('note')->nullable();
            $table->string('number', 10)->unique();
            $table->boolean('enable')->default(0);
            $table->configuration();
            $table->foreignIdFor(\Armincms\EasyLicense\Models\License::class)->constrained();
            $table->foreignIdFor(config('auth.providers.users.model'))->nullable()->constrained();
            $table->timestamp('sold_at')->nullable();
            $table->timestamp('activated_at')->nullable();
            $table->timestamp('expires_on')->nullable();
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
        Schema::dropIfExists('cards');
    }
};
