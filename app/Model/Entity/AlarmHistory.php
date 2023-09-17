<?php

namespace App\Model\Entity;

use \WilliamCosta\DatabaseManager\Database;

class AlarmHistory {
    public $idx;
    public $member_idx;
    public $member_name;
    public $push_subscription_id;
    public $device_idx;
    public $board_type_field;
    public $board_type_name;
    public $alarm_idx;
    public $alarm_contents;
    public $min;
    public $max;
    public $raw_data_idx;
    public $raw_data_value;
    public $raw_data_created_at;
    public $created_at;

    public static function getAlarmHistoryByMemberIdx($idx) {
        return self::getAlarmHistory('member_idx ='.$idx);
    }

    public function created() {
        $this->created_at = date('Y-m-d H:i:s');

        $this->idx = (new Database('alarm_history'))->insert([
            'member_idx' => $this->member_idx,
            'member_name' => $this->member_name,
            'push_subscription_id' => $this->push_subscription_id,
            'device_idx' => $this->device_idx,
            'board_type_field' => $this->board_type_field,
            'board_type_name' => $this->board_type_name,
            'alarm_idx' => $this->alarm_idx,
            'alarm_contents' => $this->alarm_contents,
            'min' => $this->min,
            'max' => $this->max,
            'raw_data_idx' => $this->raw_data_idx,
            'raw_data_value' => $this->raw_data_value,
            'raw_data_created_at' => $this->raw_data_created_at,

            'created_at' => $this->created_at,
        ]);

        return $this->idx;
    }

    public function updated() {
        $this->idx = (new Database('alarm_history'))->update('idx = '.$this->idx,[
            'member_idx' => $this->member_idx,
            'member_name' => $this->member_name,
            'push_subscription_id' => $this->push_subscription_id,
            'device_idx' => $this->device_idx,
            'board_type_field' => $this->board_type_field,
            'board_type_name' => $this->board_type_name,
            'alarm_idx' => $this->alarm_idx,
            'alarm_contents' => $this->alarm_contents,
            'min' => $this->min,
            'max' => $this->max,
            'raw_data_idx' => $this->raw_data_idx,
            'raw_data_value' => $this->raw_data_value,
            'raw_data_created_at' => $this->raw_data_created_at,
        ]);
    }

    public static function getAlarmHistory($where = null, $order = null, $limit = null, $fields = '*') {
        return (new Database('alarm_history'))->select($where, $order, $limit, $fields);
    }


}