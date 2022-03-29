<?php

namespace Database\Seeders;

use App\Helpers\CommonUtil;
use App\Helpers\Constants;
use App\Models\Company;
use App\Models\Inbox;
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
        Role::create(['name' => 'admin']);
        Role::create(['name' => 'user']);
        Role::create(['name' => 'guest']);


        $admin = User::create([
            "email" => "admin@homestead.com",
            "name" => "homestead",
            "phone_number" => "6287885876037",
            'password' => bcrypt('Admin@123!'),
            "menu_roles" => 'user,admin',
            "created_by" => Constants::SYSTEM,
            'remember_token' => Str::random(10),
            'active' => true,
        ]);

        $user = User::create([
            "email" => "user@homestead.com",
            "name" => "user homestead",
            "phone_number" => "6287885876037",
            'password' => bcrypt('User@123!'),
            "menu_roles" => 'user',
            "created_by" => Constants::SYSTEM,
            'remember_token' => Str::random(10),
            'active' => true,
        ]);

        $company = Company::create([
            "company_code" => "BASE",
            "company_name" => "PT Homestead",
            "company_alias" => "Homestead",
            'phone_number' => "6287885876037",
            "email" => 'user@homestead.com',
            "created_by" => Constants::SYSTEM,
            'active' => true,
        ]);

        $inbox = Inbox::create([
            "user_id" => $admin->id,
            "subject" => "Greeting",
            "body" => "Hello Homestead ,<br> Awesome your project <br> thanks",
            "type" => "INBOX",
            'read' => false,
            "icon" => "",
            "created_by" => $user->id,
        ]);
    }
}
