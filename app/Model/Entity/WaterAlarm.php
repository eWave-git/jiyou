<?php

namespace App\Model\Entity;

use \WilliamCosta\DatabaseManager\Database;

class WaterAlarm {
    public $idx;
    public $member_idx;

    public $device_idx;

    public $board_type_field;

    public $board_type_name;

    public $alarm_range;

    public $min;

    public $max;

    public $activation;

    public $created_at;


    public static function UpdateActiveValue($idx, $value) {
        return (new Database('water_alarm'))->execute(
            "update water_alarm set `activation`= '".$value."' where `idx` = '".$idx."'"
        );
    }

    public function created() {
        $this->created_at = date('Y-m-d H:i:s');

        $this->idx = (new Database('water_alarm'))->insert([
            'member_idx' => $this->member_idx,
            'device_idx' => $this->device_idx,
            'alarm_range' => $this->alarm_range,
            'board_type_field' => $this->board_type_field,
            'board_type_name' => $this->board_type_name,
            'min' => $this->min,
            'max' => $this->max,
            'activation' => $this->activation,
            'created_at' => $this->created_at,
        ]);

        return $this->idx;
    }

    public function updated() {
        $this->idx = (new Database('water_alarm'))->update('idx = '.$this->idx,[
            'member_idx' => $this->member_idx,
            'device_idx' => $this->device_idx,
            'alarm_range' => $this->alarm_rangem,
            'board_type_field' => $this->board_type_field,
            'board_type_name' => $this->board_type_name,
            'min' => $this->min,
            'max' => $this->max,
            'activation' => $this->activation,
        ]);
    }

    public static function getWaterAlarmByMemberIdx($member_idx) {
        return self::getWaterAlarm('member_idx ='.$member_idx,'created_at desc','','*');
    }

    public static function getWaterAlarmByDeviceIdx($idx) {
        return self::getWaterAlarm('device_idx ='.$idx,'created_at desc');
    }
    public static function getWaterAlarmByIdx($idx) {
        return self::getWaterAlarm('idx ='.$idx)->fetchObject(self::class);
    }
    public static function getWaterAlarm($where = null, $order = null, $limit = null, $fields = '*') {
        return (new Database('water_alarm'))->select($where, $order, $limit, $fields);
    }

    public function deleted() {
        $this->idx = (new Database('water_alarm'))->delete('idx ='.$this->idx);
    }
}