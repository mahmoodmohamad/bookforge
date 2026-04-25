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
        Schema::create('notes', function (Blueprint $table) {
            $table->id();
     $table->foreignId('booking_id')->constrained()->cascadeOnDelete();
    $table->text('symptoms');
    $table->text('prescription')->nullable();
    $table->text('notes')->nullable();
     $table->foreignId('tenant_id')
          ->constrained()
          ->onDelete('cascade');
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
        Schema::dropIfExists('notes');
    }
};
