<?php

namespace App\Model\Entity;

use http\Encoding\Stream\Inflate;
use \WilliamCosta\DatabaseManager\Database;

class Widget {
    public $idx;
    public $member_idx;
    public $widget_name;
    public $device_idx;
    public $address;
    public $board_type;
    public $board_number;
    public $created_at;


    public static function UpdateWidgetName($idx, $widget_name) {
        return (new Database('widget'))->execute(
            "update widget set `widget_name`= '".$widget_name."' where `idx` = '".$idx."'"
        );
    }

    public static function getWidgeTablebyMemberIdx($user_idx) {
        return self::getWidgets("member_idx ='".$user_idx."' and widget_type='text' ");
    }

    public static function getWidgeChartbyMemberIdx($user_idx) {
        return self::getWidgets("member_idx ='".$user_idx."' and widget_type='graph' ");
    }

    public static function getWidgetByDeviceIdx($device_idx) {
        return self::getWidgets("device_idx ='".$device_idx."'");
    }
    public static function getWidgetByIdx($idx) {
        return self::getWidgets("idx ='".$idx."'");
    }

    public static function getWidgets($where = null, $order = null, $limit = null, $fields = '*') {
        return (new Database('widget'))->select($where, $order, $limit, $fields);
    }

    public static function getWidgetByMemberIdx($user_idx) {
        return self::getWidgets("member_idx ='".$user_idx."'");
    }


    public function created() {
        $this->created_at = date('Y-m-d H:i:s');

        $this->idx = (new Database('widget'))->insert([
            'member_idx' => $this->member_idx,
            'widget_name' => $this->widget_name,
            'device_idx' => $this->device_idx,
            'address' => $this->address,
            'board_type' => $this->board_type,
            'board_number' => $this->board_number,
            'created_at' => $this->created_at,
        ]);

        return $this->idx;
    }

    public function updated() {
        $this->idx = (new Database('widget'))->update('idx ='.$this->idx,[
            'member_idx' => $this->member_idx,
            'widget_name' => $this->widget_name,
            'device_idx' => $this->device_idx,
            'address' => $this->address,
            'board_type' => $this->board_type,
            'board_number' => $this->board_number,
        ]);
    }

    public function deleted() {
        $this->idx = (new Database('widget'))->delete('idx ='.$this->idx);
    }
}