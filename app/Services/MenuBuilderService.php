<?php

namespace App\Services;

class MenuBuilderService
{

    private $menu;
    private $dropdown;
    private $dropdownDeep;

    public function __construct()
    {
        $this->menu = array();
        $this->dropdown = false;
        $this->dropdownDeep = 0;
    }

    /**
     * @param $menu
     * @param $element
     * @param $offset
     * @return bool
     * @author mohirwanh <mohirwanh@gmail.com>
     */
    private function innerAddElementToMenuLastPosition(&$menu, $element, $offset)
    {
        $z = 1;
        $result = false;
        $menu = &$menu[count($menu) - 1];
        while (is_array($menu)) {
            if ($z == $this->dropdownDeep - $offset) {
                array_push($menu['elements'], $element);
                $result = true;
                break;
            }
            $menu = &$menu['elements'][count($menu['elements']) - 1];
            $z++;
        }
        return $result;
    }

    /**
     * @param $element
     * @param $offset
     * @return bool
     * @author mohirwanh <mohirwanh@gmail.com>
     */
    private function addElementToMenuLastPosition($element, $offset = 0)
    {
        return $this->innerAddElementToMenuLastPosition($this->menu, $element, $offset);
    }

    /**
     * @param $id
     * @param $name
     * @param $href
     * @param $icon
     * @param $iconType
     * @return void
     * @author mohirwanh <mohirwanh@gmail.com>
     */
    private function addRegularLink($id, $name, $href, $icon, $isIcon)
    {
        $hasIcon = $icon === false ? false : true;
        if ($hasIcon) {
            array_push($this->menu, array(
                'id' => $id,
                'slug' => 'link',
                'name' => $name,
                'href' => $href,
                'hasIcon' => $hasIcon,
                'isIcon' => $isIcon,
                'icon' => $icon
            ));
        } else {
            array_push($this->menu, array(
                'id' => $id,
                'slug' => 'link',
                'name' => $name,
                'href' => $href,
                'isIcon' => $isIcon,
                'hasIcon' => $hasIcon
            ));
        }
    }

    /**
     * @param $id
     * @param $name
     * @param $href
     * @param $icon
     * @param $iconType
     * @return void
     * @author mohirwanh <mohirwanh@gmail.com>
     */
    private function addDropdownLink($id, $name, $href, $icon, $isIcon)
    {
        $num = count($this->menu);
        $hasIcon = $icon === false ? false : true;
        if ($hasIcon) {
            $this->addElementToMenuLastPosition(array(
                'id' => $id,
                'slug' => 'link',
                'name' => $name,
                'href' => $href,
                'hasIcon' => $hasIcon,
                'icon' => $icon,
                'isIcon' => $isIcon
            ));
        } else {
            $this->addElementToMenuLastPosition(array(
                'id' => $id,
                'slug' => 'link',
                'name' => $name,
                'href' => $href,
                'hasIcon' => $hasIcon
            ));
        }
    }

    /**
     * @param $id
     * @param $name
     * @param $href
     * @param $icon
     * @param $iconType
     * @return void
     * @author mohirwanh <mohirwanh@gmail.com>
     */
    public function addLink($id, $name, $href, $icon = false, $iconType = 'png')
    {
        if ($this->dropdown === true) {
            $this->addDropdownLink($id, $name, $href, $icon, $iconType);
        } else {
            $this->addRegularLink($id, $name, $href, $icon, $iconType);
        }
    }

    /**
     * @param $id
     * @param $name
     * @param $icon
     * @param $iconType
     * @return void
     * @author mohirwanh <mohirwanh@gmail.com>
     */
    public function addTitle($id, $name, $icon = false, $isIcon)
    {
        $hasIcon = $icon === false ? false : true;
        if ($hasIcon) {
            array_push($this->menu, array(
                'id' => $id,
                'slug' => 'title',
                'name' => $name,
                'hasIcon' => $hasIcon,
                'icon' => $icon,
                'isIcon' => $isIcon
            ));
        } else {
            array_push($this->menu, array(
                'id' => $id,
                'slug' => 'title',
                'name' => $name,
                'hasIcon' => $hasIcon
            ));
        }
    }

    /**
     * @param $id
     * @param $href
     * @param $name
     * @param $icon
     * @param $iconType
     * @return void
     * @author mohirwanh <mohirwanh@gmail.com>
     */
    public function beginDropdown($id, $href, $name, $icon = false, $isIcon)
    {
        $this->dropdown = true;
        $this->dropdownDeep++;
        $hasIcon = $icon === false ? false : true;
        if ($this->dropdownDeep === 1) {
            if ($hasIcon) {
                array_push($this->menu, array(
                    'id' => $id,
                    'slug' => 'dropdown',
                    'name' => $name,
                    'hasIcon' => $hasIcon,
                    'icon' => $icon,
                    'isIcon' => $isIcon,
                    'elements' => array(),
                    'href' => $href
                ));
            } else {
                array_push($this->menu, array(
                    'id' => $id,
                    'slug' => 'dropdown',
                    'name' => $name,
                    'hasIcon' => $hasIcon,
                    'elements' => array(),
                    'href' => $href
                ));
            }
        } else {
            if ($hasIcon) {
                $this->addElementToMenuLastPosition(array(
                    'id' => $id,
                    'slug' => 'dropdown',
                    'name' => $name,
                    'hasIcon' => $hasIcon,
                    'icon' => $icon,
                    'isIcon' => $isIcon,
                    'elements' => array(),
                    'href' => $href
                ), 1);
            } else {
                $this->addElementToMenuLastPosition(array(
                    'id' => $id,
                    'slug' => 'dropdown',
                    'name' => $name,
                    'hasIcon' => $hasIcon,
                    'elements' => array(),
                    'href' => $href
                ), 1);
            }
        }

    }

    /**
     * @return void
     * @author mohirwanh <mohirwanh@gmail.com>
     */
    public function endDropdown()
    {
        $this->dropdownDeep--;
        if ($this->dropdownDeep < 0) {
            $this->dropdownDeep = 0;
        }
        if ($this->dropdownDeep <= 0) {
            $this->dropdown = false;
        }
    }

    /**
     * @return array
     * @author mohirwanh <mohirwanh@gmail.com>
     */
    public function getResult()
    {
        return $this->menu;
    }


}
