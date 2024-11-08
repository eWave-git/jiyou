<?php

namespace App\Model\Entity;

use \WilliamCosta\DatabaseManager\Database;

class WaterAlarmHistory {
    public $idx;
    public $member_idx;
    public $member_name;
    public $push_subscription_id;
    public $device_idx;
    public $board_type_field;
    public $board_type_name;
    public $water_alarm_idx;
    public $alarm_contents;
    public $min;
    public $max;
    public $created_at;

    public static function getWaterAlarmHistoryByMemberIdx($idx) {
        return self::getWaterAlarmHistory('member_idx ='.$idx, 'created_at desc limit 100');     // 240108 /manager/alarm_log_list 알람 발생기록 페이지에서 알람 발생한 내역의 전체 표현 갯수 나타내는 숫자 제한
    }

    public static function getWaterAlarmHistoryByDeviceIdx($device_idx) {
        return self::getWaterAlarmHistory('device_idx ='.$device_idx);
    }

    public function created() {
        $this->created_at = date('Y-m-d H:i:s');

        $this->idx = (new Database('water_alarm_history'))->insert([
            'member_idx' => $this->member_idx,
            'member_name' => $this->member_name,
            'push_subscription_id' => $this->push_subscription_id,
            'device_idx' => $this->device_idx,
            'board_type_field' => $this->board_type_field,
            'board_type_name' => $this->board_type_name,
            'water_alarm_idx' => $this->water_alarm_idx,
            'alarm_contents' => $this->alarm_contents,
            'min' => $this->min,
            'max' => $this->max,
            'created_at' => $this->created_at,
        ]);

        return $this->idx;
    }

    public function updated() {
        $this->idx = (new Database('water_alarm_history'))->update('idx = '.$this->idx,[
            'member_idx' => $this->member_idx,
            'member_name' => $this->member_name,
            'push_subscription_id' => $this->push_subscription_id,
            'device_idx' => $this->device_idx,
            'board_type_field' => $this->board_type_field,
            'board_type_name' => $this->board_type_name,
            'water_alarm_idx' => $this->water_alarm_idx,
            'alarm_contents' => $this->alarm_contents,
            'min' => $this->min,
            'max' => $this->max,
        ]);
    }

    public static function getWaterAlarmHistory($where = null, $order = null, $limit = null, $fields = '*') {
        return (new Database('water_alarm_history'))->select($where, $order, $limit, $fields);
    }

    public function deleted() {
        $this->idx = (new Database('water_alarm_history'))->delete('idx = '.$this->idx);
    }
}