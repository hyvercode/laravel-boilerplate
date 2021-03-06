<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->uuid('id')->primary()->unique();
            $table->string('email')->unique()->unique();
            $table->string('password');
            $table->string('name', 60);
            $table->string('phone_number', 20);
            $table->timestamp('email_verified_at')->nullable();
            $table->string('menu_roles')->nullable();
            $table->string('fcm_token')->nullable()->unique();
            $table->string('personal_access_token')->nullable()->unique();
            $table->string('avatar')->nullable();
            $table->boolean('active')->nullable();
            $table->uuid('created_by');
            $table->uuid('updated_by')->nullable();
            $table->rememberToken();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
    }
}
