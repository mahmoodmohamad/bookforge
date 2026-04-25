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
        Schema::create('tenants', function (Blueprint $table) {
            $table->id();
    $table->string('name');
    $table->string('slug')->unique();          // e.g. "city-clinic"
    $table->string('business_type');           // healthcare, salon, gym, legal
    $table->string('logo')->nullable();
    $table->string('primary_color')->default('#3B82F6');
    $table->json('config')->nullable();        // custom labels, features
    $table->boolean('active')->default(true);
    $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tenants');
    }
};
