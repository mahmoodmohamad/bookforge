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
        Schema::create('bookings', function (Blueprint $table) {
            $table->id();
    $table->foreignId('client_id')->constrained()->onDelete('cascade');
    $table->foreignId('provider_id')->constrained()->onDelete('cascade');
    $table->foreignId('staff_id')->nullable()->constrained()->onDelete('set null');
    $table->dateTime('booking_date');
	$table->time('booking_time');
    $table->enum('status', [
        'scheduled',
        'completed',
        'cancelled'
    ])->default('scheduled');
    $table->text('notes')->nullable();
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
        Schema::dropIfExists('bookings');
    }
};
