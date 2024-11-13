<?php

namespace App\Model\Entity;

use \WilliamCosta\DatabaseManager\Database;

class WidgetData {
    public $idx;
    public $widget_idx;
    public $device_idx;
    public $address;
    public $board_type;
    public $board_number;
    public $board_type_field;
    public $board_type_name;
    public $created_at;


    public static function getWidgetDatesByWidgetIdx($widget_idx) {
        return self::getWidgetDates("widget_idx ='".$widget_idx."' ");
    }

    public static function getWidgetDates($where = null, $order = null, $limit = null, $fields = '*') {
        return (new Database('widget_data'))->select($where, $order, $limit, $fields);
    }

    public function created() {
        $this->created_at = date('Y-m-d H:i:s');

        $this->idx = (new Database('widget_data'))->insert([
            'widget_idx' => $this->widget_idx,
            'device_idx' => $this->device_idx,
            'address' => $this->address,
            'board_type' => $this->board_type,
            'board_number' => $this->board_number,
            'board_type_field' => $this->board_type_field,
            'board_type_name' => $this->board_type_name,
            'created_at' => $this->created_at,
        ]);
    }

    public function updated() {
        $this->idx = (new Database('widget_data'))->update('idx ='.$this->idx,[
            'widget_idx' => $this->widget_idx,
            'device_idx' => $this->device_idx,
            'address' => $this->address,
            'board_type' => $this->board_type,
            'board_number' => $this->board_number,
            'board_type_field' => $this->board_type_field,
            'board_type_name' => $this->board_type_name,
        ]);
    }

    public function deleted() {
        $this->idx = (new Database('widget_data'))->delete('idx ='.$this->idx);
    }


}