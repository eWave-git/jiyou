<?php

namespace App\Model\Entity;

use \WilliamCosta\DatabaseManager\Database;

class SettinMember {
    public $idx;

    public $settin_idx;

    public $member_idx;

    public $created_at;

    public function created() {
        $this->created_at = date('Y-m-d H:i:s');

        $this->idx = (new Database('settin_member'))->insert([
            'settin_idx' => $this->settin_idx,
            'member_idx' => $this->member_idx,
            'created_at' => $this->created_at,
        ]);

        return $this->idx;
    }
    public static function getSettinMemberByIdx($idx) {
        return self::getSettinMember('settin_idx ='.$idx);
    }
    public static function getSettinMember($where = null, $order = null, $limit = null, $fields = '*') {
        return (new Database('settin_member'))->select($where, $order, $limit, $fields);
    }


    public static function deleted($idx) {
        //$this->idx = (new Database('settin_member'))->delete('settin_idx ='.$this->idx);

        return (new Database('settin_member'))->execute("delete from settin_member where settin_idx=".$idx);
    }

}