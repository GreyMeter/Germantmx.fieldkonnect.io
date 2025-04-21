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
        Schema::create('random_stocks', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('plant_id')->unsigned()->index()->nullable();
            $table->bigInteger('category_id')->unsigned()->index()->nullable();
            $table->string('random_cut')->nullable();
            $table->string('stock')->nullable();
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
        Schema::dropIfExists('random_stocks');
    }
};
