<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePriceDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('price_details', function (Blueprint $table) {
            $table->integer('tp_pivot_id')->unsigned();
            $table->integer('quantity_from');
            $table->integer('quantity_to')->nullable();
            $table->double('price');
            $table->timestamps();
        });
        Schema::table('price_details', function (Blueprint $table) {
            $table->foreign('tp_pivot_id')->references('id')->on('type_product_pivot')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('price_details');
    }
}
