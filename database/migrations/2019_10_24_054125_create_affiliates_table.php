<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAffiliatesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('affiliates', function (Blueprint $table) {
            $table->increments('id');
            // $table->integer('user_id')->unsigned();

            /*$table->string('name');
            $table->string('email')->nullable();
            $table->string('user_name')->unique();
            $table->string('password');
            $table->string('phone')->nullable();*/

            $table->text('image')->nullable();
            $table->text('description')->nullable();
            $table->string('location')->nullable();
            $table->string('city')->nullable();
            $table->string('district')->nullable();
            $table->string('pin')->nullable();
            $table->string('contact_email')->nullable();
            $table->string('contact_phone')->nullable();
            $table->string('contact_whatsapp')->nullable();


            $table->string('site_title')->nullable();
            $table->text('site_keywords')->nullable();
            $table->text('site_description')->nullable();
            $table->text('upi_id')->nullable();
            $table->text('g_pay')->nullable();
            $table->text('bank_account')->nullable();
            $table->text('footer_description')->nullable();
            /*$table->boolean('is_active')->comment('1-active,0-inactive')->default(1);*/
            $table->foreignId('user_id')->nullable();
            $table->foreign('user_id')->references('id')->on('users')->cascadeOnDelete();
            $table->timestamps();
        });
        // Schema::table('affiliates', function (Blueprint $table) {
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
        Schema::dropIfExists('affiliates');
    }
}
