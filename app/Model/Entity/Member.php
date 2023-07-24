<?php

namespace App\Model\Entity;

use \WilliamCosta\DatabaseManager\Database;

class Member {
    public $idx;
    public $member_id;
    public $member_name;
    public $member_password;
    public $member_email;
    public $member_phone;
    public $member_type;
    public $member_farm_idx;
    public $created_at;

    public static function getMemberByIdx($idx) {
        return self::getMembers('idx ='.$idx)->fetchObject(self::class);
    }

    public static function getMemberById($member_id) {
        return self::getMembers("member_id='".$member_id."'")->fetchObject(self::class);
    }


    public static function getMembers($where = null, $order = null, $limit = null, $fields = '*') {

        return (new Database('member'))->select($where, $order, $limit, $fields);
    }

    public function created() {
        $this->created_at = date('Y-m-d H:i:s');

        $this->idx = (new Database('member'))->insert([
            'member_id' => $this->member_id,
            'member_name' => $this->member_name,
            'member_password' => $this->member_password,
            'member_email' => $this->member_email,
            'member_phone' => $this->member_phone,
            'member_type' => $this->member_type,
            'member_farm_idx' => $this->member_farm_idx,
            'created_at' => $this->created_at,
        ]);
    }

    public function updated() {
        $this->idx = (new Database('member'))->update('idx ='.$this->idx,[
            'member_id' => $this->member_id,
            'member_name' => $this->member_name,
            'member_password' => $this->member_password,
            'member_email' => $this->member_email,
            'member_phone' => $this->member_phone,
            'member_type' => $this->member_type,
            'member_farm_idx' => $this->member_farm_idx,
        ]);
    }

    public function deleted() {
        $this->idx = (new Database('member'))->delete('idx ='.$this->idx);
    }
}