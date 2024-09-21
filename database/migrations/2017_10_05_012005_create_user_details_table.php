<?php

use App\Models\UserDetail;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUserDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_details', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->text('phone')->nullable();
            $table->text('address')->nullable();
            $table->text('profile_pic')->nullable();
            $table->foreignId('user_id')->nullable();
            $table->foreign('user_id')->references('id')->on('users')->cascadeOnDelete();
            $table->timestamps();
        });
        // Schema::table('user_details', function (Blueprint $table) {
        //     $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        // });

        UserDetail::create(['phone' => '9847638678','address' => 'asd asdf','profile_pic'=>'','user_id' => 1,]);
        UserDetail::create(['phone' => '9847767424','address' => 'asdf asdf','profile_pic'=>'','user_id' => 2]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('user_details');
    }
}
