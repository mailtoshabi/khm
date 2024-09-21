<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('products', function (Blueprint $table) {
            $table->increments('id');
            /*$table->char('uuid', 36);*/
            // $table->integer('user_id')->unsigned();
            $table->text('uuid');
            $table->string('name');
            $table->string('image')->nullable();
            $table->text('images')->nullable();
            $table->text('video')->nullable();
            $table->text('description')->nullable();
            $table->string('brochure')->nullable();
            $table->string('unit_om');
            $table->string('hsn_code')->nullable();
            $table->smallInteger('tax');
            $table->double('delivery_unit')->default(0);
            $table->double('delivery_min')->default(0);
            $table->double('delivery_max')->default(0);
            $table->string('site_title')->nullable();
            $table->text('site_keywords')->nullable();
            $table->text('site_description')->nullable();
            $table->boolean('is_featured')->comment('1-featured,0-normal')->default(0);
            $table->boolean('is_home')->comment('1-in home,0-not in home')->default(0);
            $table->boolean('is_active')->comment('1-active,0-inactive')->default(1);
            $table->foreignId('user_id')->nullable();
            $table->foreign('user_id')->references('id')->on('users')->cascadeOnDelete();
            $table->timestamps();
        });
        // Schema::table('products', function (Blueprint $table) {
        //     $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        // });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('products');
    }
}
