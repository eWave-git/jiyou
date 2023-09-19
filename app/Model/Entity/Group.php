<?php

namespace App\Model\Entity;

use \WilliamCosta\DatabaseManager\Database;

class Group {
    public $idx;

    public $member_idx;
    public $group_name;
    public $start_date;

    public $end_date;

    public $created_at;

    public static function getGroupByMemberIdx($idx) {
        return self::getGroup('member_idx ='.$idx,'created_at desc');
    }

    public static function getGroupByIdx($idx) {
        return self::getGroup('idx ='.$idx)->fetchObject(self::class);
    }

    public static function getGroup($where = null, $order = null, $limit = null, $fields = '*') {
        return (new Database('date_group'))->select($where, $order, $limit, $fields);
    }

    public function created() {
        $this->created_at = date('Y-m-d H:i:s');

        $this->idx = (new Database('date_group'))->insert([
            'member_idx' => $this->member_idx,
            'group_name' => $this->group_name,
            'start_date' => $this->start_date,
            'end_date' => $this->end_date,
            'created_at' => $this->created_at,
        ]);

        return $this->idx;
    }

    public function updated() {
        $this->idx = (new Database('date_group'))->update('idx ='.$this->idx,[
            'member_idx' => $this->member_idx,
            'group_name' => $this->group_name,
            'start_date' => $this->start_date,
            'end_date' => $this->end_date,
        ]);
    }

    public function deleted() {
        $this->idx = (new Database('date_group'))->delete('idx ='.$this->idx);
    }

}