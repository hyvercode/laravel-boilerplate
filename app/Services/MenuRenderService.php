<?php
/*
*   08.11.2019
*   MenuRenderFromDatabaseDataService
*/

namespace App\Services;

use App\Services\MenuBuilderService;

class MenuRenderService
{

    private $mb; // MenuBuilderService
    private $data;

    public function __construct()
    {
        $this->mb = new MenuBuilderService();
    }

    /**
     * @param $data
     * @return void
     * @author mohirwanh <mohirwanh@gmail.com>
     */
    private function addTitle($data)
    {
        $this->mb->addTitle($data['id'], $data['name'], $data['icon'], $data['is_icon']);
    }

    /**
     * @param $data
     * @return void
     * @author mohirwanh <mohirwanh@gmail.com>
     */
    private function addLink($data)
    {
        if ($data['parent_id'] === NULL) {
            $this->mb->addLink($data['id'], $data['name'], $data['href'], $data['icon'], $data['is_icon']);
        }
    }

    /**
     * @param $id
     * @return void
     * @author mohirwanh <mohirwanh@gmail.com>
     */
    private function dropdownLoop($id)
    {
        for ($i = 0; $i < count($this->data); $i++) {
            if ($this->data[$i]['parent_id'] == $id) {
                if ($this->data[$i]['slug'] === 'dropdown') {
                    $this->addDropdown($this->data[$i]);
                } elseif ($this->data[$i]['slug'] === 'link') {
                    $this->mb->addLink($this->data[$i]['id'], $this->data[$i]['name'], $this->data[$i]['href']);
                } else {
                    $this->addTitle($this->data[$i]);
                }
            }
        }
    }

    /**
     * @param $data
     * @return void
     * @author mohirwanh <mohirwanh@gmail.com>
     */
    private function addDropdown($data)
    {
        $this->mb->beginDropdown($data['id'], $data['href'], $data['name'], $data['icon'], $data['is_icon']);
        $this->dropdownLoop($data['id']);
        $this->mb->endDropdown();
    }

    /**
     * @return void
     * @author mohirwanh <mohirwanh@gmail.com>
     */
    private function mainLoop()
    {
        for ($i = 0; $i < count($this->data); $i++) {
            switch ($this->data[$i]['slug']) {
                case 'title':
                    $this->addTitle($this->data[$i]);
                    break;
                case 'link':
                    $this->addLink($this->data[$i]);
                    break;
                case 'dropdown':
                    if ($this->data[$i]['parent_id'] == null) {
                        $this->addDropdown($this->data[$i]);
                    }
                    break;
            }
        }
    }

    /***
     * $data - array
     * return - array
     */
    public function render($data)
    {
        if (!empty($data)) {
            $this->data = $data;
            $this->mainLoop();
        }
        return $this->mb->getResult();
    }

}
