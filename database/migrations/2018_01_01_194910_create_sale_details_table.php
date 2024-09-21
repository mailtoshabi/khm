<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSaleDetailsTable extends Migration
{

    public function up()
    {
        Schema::create('sale_details', function (Blueprint $table) {
            $table->integer('sale_id')->unsigned();
            $table->integer('product_id')->unsigned();
            $table->string('type_size');
            $table->string('quantity')->default(0);
            $table->double('price')->nullable();
            $table->timestamps();
        });
        Schema::table('sale_details', function (Blueprint $table) {
            $table->foreign('sale_id')->references('id')->on('sales')->onDelete('cascade');
            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('sale_details');
    }
}
