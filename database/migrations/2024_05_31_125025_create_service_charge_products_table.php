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
        Schema::create('service_charge_products', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('charge_type_id')->nullable();
            $table->string('product_name')->nullable();
            $table->bigInteger('division_id')->nullable();
            $table->bigInteger('category_id')->nullable();
            $table->string('price')->nullable();
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
        Schema::dropIfExists('service_charge_products');
    }
};
