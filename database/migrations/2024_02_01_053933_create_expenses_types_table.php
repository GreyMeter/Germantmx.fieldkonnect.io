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
        Schema::create('expenses_types', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->float('rate')->default('0.00');
            $table->tinyInteger('is_active')->default('1');
            $table->bigInteger('allowance_type_id');
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
        Schema::dropIfExists('expenses_types');
    }
};
