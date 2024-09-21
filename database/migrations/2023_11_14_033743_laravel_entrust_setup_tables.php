<?php

use App\Http\Utilities\Utility;
use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class LaravelEntrustSetupTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Schema to create roles table
        Schema::create('roles', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name')->unique();
            $table->string('display_name')->nullable();
            $table->string('description')->nullable();
            $table->foreignId('created_by');
            $table->foreign('created_by')->references('id')->on('users')->cascadeOnDelete();
            $table->timestamps();
        });

        Role::create(['name'=>'admin','display_name'=>'Administrator','created_at' => now(), 'created_by' => Utility::ADMIN_ID]);
        Role::create(['name'=>'affiliate','display_name'=>'Affiliate','created_at' => now(), 'created_by' => Utility::ADMIN_ID]);
        Role::create(['name'=>'clinic','display_name'=>'Clinic','created_at' => now(), 'created_by' => Utility::ADMIN_ID]);
        Role::create(['name'=>'brand','display_name'=>'Brand Store','created_at' => now(), 'created_by' => Utility::ADMIN_ID]);

        // Schema to create permissions table
        Schema::create('permissions', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name')->unique();
            $table->string('display_name')->nullable();
            $table->string('description')->nullable();
            $table->foreignId('created_by');
            $table->foreign('created_by')->references('id')->on('users')->cascadeOnDelete();
            $table->timestamps();
        });

        Permission::create(['name'=>'admin-dashboard','display_name'=>'Admin Dashboard','created_at' => now(), 'created_by' => Utility::ADMIN_ID]);
        Permission::create(['name'=>'affiliate-dashboard','display_name'=>'Affiliate Dashboard','created_at' => now(), 'created_by' => Utility::ADMIN_ID]);

        // Schema to create role_users table
        Schema::create('role_user', function (Blueprint $table) {
            $table->unsignedBigInteger('role_id');
            $table->unsignedBigInteger('user_id');

            $table->foreign('user_id')->references('id')->on('users')
                ->onUpdate('cascade')->onDelete('cascade');

            $table->foreign('role_id')->references('id')->on('roles')
                ->onUpdate('cascade')->onDelete('cascade');

            $table->primary(['user_id', 'role_id']);
        });

        DB::table('role_user')->insert([
            ['role_id' => 1, 'user_id' => Utility::ADMIN_ID],
            ['role_id' => 2, 'user_id' => Utility::ADMIN_ID],
            ['role_id' => 1, 'user_id' => 2],
        ]);

        // Schema to create permission_role table
        Schema::create('permission_role', function (Blueprint $table) {
            $table->unsignedBigInteger('permission_id');
            $table->unsignedBigInteger('role_id');

            $table->foreign('permission_id')->references('id')->on('permissions')
                ->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('role_id')->references('id')->on('roles')
                ->onUpdate('cascade')->onDelete('cascade');

            $table->primary(['permission_id', 'role_id']);
        });

        DB::table('permission_role')->insert([
            ['permission_id' => 1, 'role_id' => 1],
            ['permission_id' => 2, 'role_id' => 2],
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('permission_role');
        Schema::dropIfExists('role_user');
        Schema::dropIfExists('permissions');
        Schema::dropIfExists('roles');
    }
}
