<?php

namespace App\Model\Entity;

use \WilliamCosta\DatabaseManager\Database;

class Alarm {
    public $idx;
    public $member_idx;
    public $group_idx;
    public $device_idx;

    public $board_type_field;

    public $board_type_name;

    public $alarm_range;

    public $min;

    public $max;

    public $alarm_satisfaction;

    public $activation;

    public $created_at;


    public static function UpdateActiveValue($idx, $value) {
        return (new Database('alarm'))->execute(
            "update alarm set `activation`= '".$value."' where `idx` = '".$idx."'"
        );
    }

    public function created() {
        $this->created_at = date('Y-m-d H:i:s');

        $this->idx = (new Database('alarm'))->insert([
            'member_idx' => $this->member_idx,
            'group_idx' => $this->group_idx ?? 0,
            'device_idx' => $this->device_idx,
            'alarm_range' => $this->alarm_range,
            'board_type_field' => $this->board_type_field,
            'board_type_name' => $this->board_type_name,
            'min' => $this->min,
            'max' => $this->max,
            'alarm_satisfaction' => $this->alarm_satisfaction,
            'activation' => $this->activation,
            'created_at' => $this->created_at,
        ]);

        return $this->idx;
    }

    public function updated() {
        $this->idx = (new Database('alarm'))->update('idx = '.$this->idx,[
            'member_idx' => $this->member_idx,
            'device_idx' => $this->device_idx,
            'alarm_range' => $this->alarm_rangem,
            'board_type_field' => $this->board_type_field,
            'board_type_name' => $this->board_type_name,
            'min' => $this->min,
            'max' => $this->max,
            'alarm_satisfaction' => $this->alarm_satisfaction,
            'activation' => $this->activation,
        ]);
    }

    public static function getAlarmByMemberIdx($member_idx, $group_idx = 0) {
        return self::getAlarm('member_idx ='.$member_idx.' and group_idx ='.$group_idx,'created_at asc','','*');
    }

    public static function searchAlarm($device, $board) {
        return (new Database('alarm'))->execute("select count(*) as cnt from alarm where device_idx =".$device." and board_type_field='".$board."'");
    }

    public static function getAlarmByDeviceIdx($idx) {
        return self::getAlarm('device_idx ='.$idx,'created_at desc');
    }
    public static function getAlarmByIdx($idx) {
        return self::getAlarm('idx ='.$idx)->fetchObject(self::class);
    }
    public static function getAlarm($where = null, $order = null, $limit = null, $fields = '*') {
        return (new Database('alarm'))->select($where, $order, $limit, $fields);
    }

    public function deleted() {
        $this->idx = (new Database('alarm'))->delete('idx ='.$this->idx);
    }
}