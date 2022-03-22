<?php

namespace Database\Seeders;

use App\Helpers\CommonUtil;
use App\Helpers\Constants;
use App\Helpers\DateTimeConverter;
use App\Models\Branch;
use App\Models\Company;
use App\Models\Employee;
use App\Models\RoleHierarchy;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;

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
        $roleAdmin = Role::create(['name' => 'admin']);
        $user = Role::create(['name' => 'user']);
        $guest = Role::create(['name' => 'guest']);


        $admin = User::create([
            "email" => "admin@agreesip.com",
            "name" => "Aggregator SIP",
            "phone_number" => "6287885876037",
            'password' => bcrypt('Admin@123!'),
            "menu_roles" => 'user,admin',
            "created_by" => Constants::SYSTEM,
            'remember_token' => Str::random(10),
            'active' => true,
        ]);
    }
}
