<?php

namespace Database\Seeders;

use App\Helpers\CommonUtil;
use App\Helpers\Constants;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class StartupSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        /* Create roles */
        Role::create(['id' => CommonUtil::generateUUID(), 'name' => 'admin']);
        Role::create(['id' => CommonUtil::generateUUID(), 'name' => 'user']);
        Role::create(['id' => CommonUtil::generateUUID(), 'name' => 'guest']);


        $admin = User::create([
            'id' => CommonUtil::generateUUID(),
            "email" => "admin@homestead.com",
            "name" => "homestead.com",
            "phone_number" => "6287885876037",
            'password' => bcrypt('Admin@123!'),
            "menu_roles" => 'user,admin',
            "created_by" => Constants::SYSTEM,
            'remember_token' => Str::random(10),
            'active' => true,
        ]);
    }
}
