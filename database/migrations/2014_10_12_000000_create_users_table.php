<?php

use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name');
            $table->string('email')->unique();
            $table->string('username')->unique();
            $table->string('password');
            $table->boolean('is_active')->comment('1-active,0-inactive')->default(1);
            $table->rememberToken();
            $table->timestamps();
        });

        User::create(['name' => 'Kerala Health Mart','email' => 'mailtoshabi@gmail.com','username'=>'mailtoshabi','password' => Hash::make('123456'),'created_at' => now(),]);
        User::create(['name' => 'Kerala Health Mart','email' => 'keralahealthmart@gmail.com','username'=>'keralahealthmart','password' => Hash::make('123456'),'created_at' => now(),]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
