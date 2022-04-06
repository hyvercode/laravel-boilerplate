<?php

namespace App\Repositories;

use App\Models\Menus;
use App\Services\MenuRenderService;

class MenuRepository extends CrudRepository
{

    public function model()
    {
        return Menus::class;
    }

    /**
     * @param $menuName
     * @param $role
     * @return mixed
     */
    private function getMenuFromDB($menuName, $role)
    {
        return Menus::join('menu_roles', 'menus.id', '=', 'menu_roles.menus_id')
            ->join('menu_lists', 'menu_lists.id', '=', 'menus.menu_id')
            ->where('menu_lists.name', '=', $menuName)
            ->where('menu_roles.role_name', '=', $role)
            ->where('menus.active', '=', true)
            ->where('menu_roles.active', '=', true)
            ->orderBy('menus.sequence', 'asc')
            ->get(['menus.*']);
    }

    /**
     * @param $menuName
     * @return mixed
     * @author mohirwanh <mohirwanh@gmail.com>
     */
    private function getGuestMenu($menuName)
    {
        return $this->getMenuFromDB($menuName, 'guest');
    }

    /**
     * @param $menuName
     * @return mixed
     * @author mohirwanh <mohirwanh@gmail.com>
     */
    private function getUserMenu($menuName)
    {
        return $this->getMenuFromDB($menuName, 'user');
    }

    /**
     * @param $menuName
     * @return mixed
     * @author mohirwanh <mohirwanh@gmail.com>
     */
    private function getAdminMenu($menuName)
    {
        return $this->getMenuFromDB($menuName, 'admin');
    }

    /**
     * @param $roles
     * @param $menuName
     * @return MenuRepository[]|array|\Illuminate\Database\Eloquent\Collection
     */
    public function getMenu($roles, $menuName = 'sidebar menu')
    {
        $roles = explode(',', $roles);
        if (empty($roles)) {
            $menu = $this->getGuestMenu($menuName);
        } elseif (in_array('admin', $roles)) {
            $menu = $this->getAdminMenu($menuName);
        } elseif (in_array('user', $roles)) {
            $menu = $this->getUserMenu($menuName);
        } else {
            $menu = $this->getGuestMenu($menuName);
        }
        $rfd = new MenuRenderService();
        return $rfd->render($menu);
    }

    /**
     * @param $menuId
     * @return array
     */
    public function getAll($menuId = 1)
    {
        $menu = Menus::select('menus.*')
            ->where('menus.menu_id', '=', $menuId)
            ->where('menus.active', '=', true)
            ->orderBy('menus.sequence', 'asc')->get();
        $rfd = new MenuRenderService();
        return $rfd->render($menu);
    }
}
