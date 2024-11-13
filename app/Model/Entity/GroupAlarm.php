<?php

namespace App\Model\Entity;

use \WilliamCosta\DatabaseManager\Database;

class GroupAlarm {
    public $idx;
    public $member_idx;
    public $group_name;
    public $activation;
    public $created_at;

    public static function GroupUpdateActiveValue($idx, $value) {
        return (new Database('group_alarm'))->execute(
            "update group_alarm set `activation`= '".$value."' where `idx` = '".$idx."'"
        );
    }
    public static function getGroupAlarmByIdx($idx) {
        return self::getGroupAlarm('idx ='.$idx)->fetchObject(self::class);
    }
    public static function getGroupAlarmByMemberIdx($member_idx) {
        return self::getGroupAlarm('member_idx ='.$member_idx,'created_at asc','','*');
    }

    public function created() {
        $this->created_at = date('Y-m-d H:i:s');

        $this->idx = (new Database('group_alarm'))->insert([
            'member_idx' => $this->member_idx,
            'group_name' => $this->group_name,
            'activation' => $this->activation,
            'created_at' => $this->created_at,
        ]);

        return $this->idx;
    }

    public function updated() {
        $this->idx = (new Database('group_alarm'))->update('idx = '.$this->idx,[
            'member_idx' => $this->member_idx,
            'group_naem' => $this->group_name,
            'activation' => $this->activation,
        ]);
    }

    public static function getGroupAlarm($where = null, $order = null, $limit = null, $fields = '*') {
        return (new Database('group_alarm'))->select($where, $order, $limit, $fields);
    }

    public function deleted() {
        $this->idx = (new Database('group_alarm'))->delete('idx ='.$this->idx);
    }
}