<?php

namespace Database\Seeders;

use App\Helpers\CommonUtil;
use App\Models\Role;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MenusTableSeeder extends Seeder
{
    private $menuId = null;
    private $dropdownId = array();
    private $dropdown = false;
    private $sequence = 1;
    private $joinData = array();

    public function join($roles, $menusId)
    {
        $roles = explode(',', $roles);
        foreach ($roles as $role) {
            array_push($this->joinData, array('role_name' => $role, 'menus_id' => $menusId));
        }
    }

    /*
        Function assigns menu elements to roles
        Must by use on end of this seeder
    */
    public function joinAllByTransaction()
    {
        DB::beginTransaction();
        foreach ($this->joinData as $data) {
            DB::table('menu_roles')->insert([
                'id' => CommonUtil::generateUUID(),
                'role_name' => $data['role_name'],
                'menus_id' => $data['menus_id']
            ]);
        }
        DB::commit();
    }

    public function insertLink($roles, $name, $href, $icon = null)
    {
        if ($this->dropdown === false) {
            DB::table('menus')->insert([
                'id' => CommonUtil::generateUUID(),
                'slug' => 'link',
                'name' => $name,
                'icon' => $icon,
                'href' => $href,
                'menu_id' => $this->menuId,
                'sequence' => $this->sequence
            ]);
        } else {
            DB::table('menus')->insert([
                'id' => CommonUtil::generateUUID(),
                'slug' => 'link',
                'name' => $name,
                'icon' => $icon,
                'href' => $href,
                'menu_id' => $this->menuId,
                'parent_id' => $this->dropdownId[count($this->dropdownId) - 1],
                'sequence' => $this->sequence
            ]);
        }
        $this->sequence++;
        $lastId = DB::getPdo()->lastInsertId();
        $this->join($roles, $lastId);
        return $lastId;
    }

    public function insertTitle($roles, $name)
    {
        DB::table('menus')->insert([
            'id' => CommonUtil::generateUUID(),
            'slug' => 'title',
            'name' => $name,
            'menu_id' => $this->menuId,
            'sequence' => $this->sequence
        ]);
        $this->sequence++;
        $lastId = DB::getPdo()->lastInsertId();
        $this->join($roles, $lastId);
        return $lastId;
    }

    public function beginDropdown($roles, $name, $href = '', $icon = '')
    {
        if (count($this->dropdownId)) {
            $parentId = $this->dropdownId[count($this->dropdownId) - 1];
        } else {
            $parentId = null;
        }
        DB::table('menus')->insert([
            'id' => CommonUtil::generateUUID(),
            'slug' => 'dropdown',
            'name' => $name,
            'icon' => $icon,
            'menu_id' => $this->menuId,
            'sequence' => $this->sequence,
            'parent_id' => $parentId,
            'href' => $href
        ]);
        $lastId = DB::getPdo()->lastInsertId();
        array_push($this->dropdownId, $lastId);
        $this->dropdown = true;
        $this->sequence++;
        $this->join($roles, $lastId);
        return $lastId;
    }

    public function endDropdown()
    {
        $this->dropdown = false;
        array_pop($this->dropdownId);
    }

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        /* Get roles */
        $this->adminRole = Role::where('name', '=', 'admin')->first();
        $this->userRole = Role::where('name', '=', 'user')->first();
        $this->guestRole = Role::where('name', '=', 'guest')->first();

        $dropdownId = array();
        /* Create Sidebar menu */
        DB::table('menu_lists')->insert([
            'id' => CommonUtil::generateUUID(),
            'name' => 'sidebar_menu'
        ]);
        $this->menuId = DB::getPdo()->lastInsertId();  //set menuId

        /* guest menu */
        $this->insertLink('guest,user,admin', 'Dashboard', '/', 'pie_chart');
        $this->endDropdown();
        $this->beginDropdown('user,admin', 'Master', '/masters', 'ballot');
            $this->insertLink('admin', 'Users', '/masters/users');
            $this->insertLink('admin', 'Menu', '/masters/menu');
            $this->insertLink('admin', 'Roles', '/masters/menu/role');
        $this->endDropdown();
        $this->beginDropdown('admin', 'Settings', '/', 'settings');
            $this->insertLink('user,admin', 'Notification', '/notification/read');
        $this->endDropdown();

        $this->menuId = DB::getPdo()->lastInsertId();  //set menuId
        /* Create top menu */
        DB::table('menu_lists')->insert([
            'id' => CommonUtil::generateUUID(),
            'name' => 'top_menu'
        ]);

        $this->joinAllByTransaction(); ///   <===== Must by use on end of this seeder
    }
}
