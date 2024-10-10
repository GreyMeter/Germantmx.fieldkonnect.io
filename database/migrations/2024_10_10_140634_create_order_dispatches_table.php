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
        Schema::create('order_dispatches', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('order_id')->nullable();
            $table->string('po_no')->nullable();
            $table->string('confirm_po_no')->nullable();
            $table->string('dispatch_po_no')->nullable();
            $table->bigInteger('qty')->nullable();
            $table->bigInteger('unit_id')->nullable();
            $table->bigInteger('brand_id')->nullable();
            $table->bigInteger('category_id')->nullable();
            $table->bigInteger('base_price')->nullable();
            $table->bigInteger('soda_price')->nullable();
            $table->integer('status')->default(0);
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
        Schema::dropIfExists('order_dispatches');
    }
};
