<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCustomersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('customers', function (Blueprint $table) {
            $table->increments('id');
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->string('password')->nullable();
            $table->smallInteger('status')->comment('0-notconfirmed,1-confirmed,2-Guest')->default(0);
            $table->boolean('is_active')->comment('1-active,0-inactive')->default(0);
            $table->boolean('is_access')->comment('1-access,0-no access')->default(0);
            /*$table->char('confirm_mail', 36)->nullable();
            $table->char('confirm_phone', 36)->nullable();*/
            $table->rememberToken();
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
        Schema::dropIfExists('customers');
    }
}
