<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSalesTable extends Migration
{

     public function up()
    {
        Schema::create('sales', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('customer_id')->unsigned();
            $table->string('order_no')->nullable();
            $table->smallInteger('pay_method')->default(1);
            $table->boolean('is_paid')->default(0);
            $table->text('payment_id')->nullable();
            $table->text('payment_request_id')->nullable();
            $table->double('sub_total')->default(0);
            $table->double('delivery_charge')->default(0);
            $table->boolean('delivery_type')->default(0);
            $table->smallInteger('status')->default(1);
            $table->string('address')->nullable();
            $table->tinyInteger('courier')->nullable();
            $table->string('courier_track')->nullable();
            $table->string('delivery')->nullable();
            $table->boolean('is_cancelled_customer')->default(0);
            $table->string('utr_no')->nullable();
            $table->boolean('is_utr_cust')->default(0);
            $table->text('sms_content')->nullable();

            // $table->integer('user_id')->unsigned();
            $table->foreignId('user_id')->nullable();
            $table->foreign('user_id')->references('id')->on('users')->cascadeOnDelete();
            $table->timestamps();
        });

        Schema::table('sales', function (Blueprint $table) {
            $table->foreign('customer_id')->references('id')->on('customers')->onDelete('cascade');
            // $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }


    public function down()
    {
        Schema::dropIfExists('sales');
    }
}
