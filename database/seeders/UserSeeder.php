<?php

namespace Database\Seeders;

use App\Models\User;
use App\Utils\Constants;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $admin = User::create([
            "name" => "admin",
            "username" => "admin@picker",
            "email" => "admin@picker",
            "password" => Hash::make("Admin!23"),
            "phone_number" => "6287885876037",
            "company_id" => 1,
            "branch_id" => 1,
            "api_roles" => Constants::ADMIN,
            "status" => Constants::ACTIVE,
            "coordinate" => "429840288284",
            "fcm_token" => "oiurowqrnqjfnqhro",
        ]);

        $admin->assignRole('admin');

        $admin = User::create([
            "name" => "picker",
            "username" => "picker@picker",
            "email" => "picker@picker",
            "password" => Hash::make("Pdmin!23"),
            "phone_number" => "0878858760xx",
            "company_id" => 1,
            "branch_id" => 1,
            "api_roles" => Constants::PICKER,
            "status" => Constants::ACTIVE,
            "coordinate" => "429840288284",
            "fcm_token" => "oiurowqrnqjfnqhro",
        ]);

        $admin->assignRole('picker');

        $admin = User::create([
            "name" => "boss",
            "username" => "boss@picker",
            "email" => "boss@picker",
            "password" => Hash::make("Boss!23"),
            "phone_number" => "087885876xxx",
            "company_id" => 2,
            "branch_id" => 1,
            "api_roles" => Constants::BOSS,
            "status" => Constants::ACTIVE,
            "coordinate" => "429840288284",
            "fcm_token" => "oiurowqrnqjfnqhro",
        ]);

        $admin->assignRole('boss');

        $admin = User::create([
            "name" => "partner",
            "username" => "partner@picker",
            "email" => "partner@picker",
            "password" => Hash::make("Opr!23"),
            "phone_number" => "08788587xxxx",
            "company_id" => 2,
            "branch_id" => 2,
            "api_roles" => Constants::PARTNER,
            "status" => Constants::ACTIVE,
            "coordinate" => "429840288284",
            "fcm_token" => "oiurowqrnqjfnqhro",
        ]);

        $admin->assignRole('opr');
    }
}
