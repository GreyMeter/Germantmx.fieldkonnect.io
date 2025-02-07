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
        Schema::create('order_dispactch_details', function (Blueprint $table) {
            $table->id();
            $table->string('order_dispatch_po_no')->nullable();
            $table->string('driver_name')->nullable();
            $table->string('driver_contact_number')->nullable();
            $table->string('vehicle_number')->nullable(); 
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
        Schema::dropIfExists('order_dispactch_details');
    }
};
