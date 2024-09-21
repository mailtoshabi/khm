<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAffiliateProductPivotTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('affiliate_product', function(Blueprint $table)
        {
            $table->increments('id');
            $table->integer('affiliate_id')->unsigned()->index();
            $table->foreign('affiliate_id')->references('id')->on('affiliates')->onDelete('cascade');
            $table->integer('product_id')->unsigned()->index();
            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
            $table->boolean('is_home')->comment('1-in home,0-not in home')->default(1);
            $table->boolean('is_offer')->comment('1-in offer,0-not in offer')->default(0);
            $table->double('distributor_price')->nullable();
            $table->string('site_title')->nullable();
            $table->text('site_keywords')->nullable();
            $table->text('site_description')->nullable();
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
        Schema::dropIfExists('affiliate_product');
    }
}
