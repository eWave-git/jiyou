<?php

namespace App\Model\Entity;

use \WilliamCosta\DatabaseManager\Database;

class WaterAlarmMember {
    public $idx;

    public $water_alarm_idx;

    public $member_idx;

    public $created_at;

    public function created() {
        $this->created_at = date('Y-m-d H:i:s');

        $this->idx = (new Database('water_alarm_member'))->insert([
            'water_alarm_idx' => $this->water_alarm_idx,
            'member_idx' => $this->member_idx,
            'created_at' => $this->created_at,
        ]);

        return $this->idx;
    }
    public static function getWaterAlarmMemberByIdx($idx) {
        return self::getWaterAlarmMember('water_alarm_idx ='.$idx,'','','*');
    }
    public static function getWaterAlarmMember($where = null, $order = null, $limit = null, $fields = '*') {
        return (new Database('water_alarm_member'))->select($where, $order, $limit, $fields);
    }


    public static function deleted($idx) {
        //$this->idx = (new Database('alarm_member'))->delete('settin_idx ='.$this->idx);

        return (new Database('water_alarm_member'))->execute("delete from water_alarm_member where water_alarm_idx=".$idx);
    }

}