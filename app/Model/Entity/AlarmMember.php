<?php

namespace App\Model\Entity;

use \WilliamCosta\DatabaseManager\Database;

class AlarmMember {
    public $idx;

    public $alarm_idx;

    public $member_idx;

    public $created_at;

    public function created() {
        $this->created_at = date('Y-m-d H:i:s');

        $this->idx = (new Database('alarm_member'))->insert([
            'alarm_idx' => $this->alarm_idx,
            'member_idx' => $this->member_idx,
            'created_at' => $this->created_at,
        ]);

        return $this->idx;
    }
    public static function getAlarmMemberByIdx($idx) {
        return self::getAlarmMember('alarm_idx ='.$idx);
    }
    public static function getAlarmMember($where = null, $order = null, $limit = null, $fields = '*') {
        return (new Database('alarm_member'))->select($where, $order, $limit, $fields);
    }


    public static function deleted($idx) {
        //$this->idx = (new Database('alarm_member'))->delete('settin_idx ='.$this->idx);

        return (new Database('alarm_member'))->execute("delete from alarm_member where alarm_idx=".$idx);
    }

}