<?php

namespace App\Model\Entity;

use \WilliamCosta\DatabaseManager\Database;

class SettingMember {
    public $idx;

    public $setting_idx;

    public $member_idx;

    public $created_at;

    public function created() {
        $this->created_at = date('Y-m-d H:i:s');

        $this->idx = (new Database('setting_member'))->insert([
            'setting_idx' => $this->setting_idx,
            'member_idx' => $this->member_idx,
            'created_at' => $this->created_at,
        ]);

        return $this->idx;
    }
    public static function getSettingMemberByIdx($idx) {
        return self::getSettingMember('setting_idx ='.$idx);
    }
    public static function getSettingMember($where = null, $order = null, $limit = null, $fields = '*') {
        return (new Database('setting_member'))->select($where, $order, $limit, $fields);
    }


    public static function deleted($idx) {
        //$this->idx = (new Database('setting_member'))->delete('settin_idx ='.$this->idx);

        return (new Database('setting_member'))->execute("delete from setting_member where setting_idx=".$idx);
    }

}