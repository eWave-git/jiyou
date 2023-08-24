<?php

namespace App\Model\Entity;

use http\Encoding\Stream\Inflate;
use \WilliamCosta\DatabaseManager\Database;

class Member {
    public $idx;
    public $member_id;
    public $member_name;
    public $member_password;
    public $member_email;
    public $member_phone;
    public $member_type;
    public $member_group;
    public $member_farm_idx;
    public $created_at;


    public static function getMemberByGroup($member_idx) {
        return (new Database('member'))->execute("select * from member where member_group=".$member_idx);
    }

    public static function getMembersControlDevice($member_idx) {
        return (new Database('member'))->execute("select *, d.idx as idx from member as m left join farm as f on m.idx = f.member_idx left join device as d on f.idx = d.farm_idx left join board_type_ref btr on d.board_type = btr.board_type where m.member_type = 'manager' and f.idx is not null and btr.control_type !='' and m.idx=".$member_idx."");
    }

    public static function getMembersDevice($member_idx) {
        return (new Database('member'))->execute("select * from member as m left join farm as f on m.idx = f.member_idx left join device as d on f.idx = d.farm_idx where m.member_type='manager' and f.idx is not null and m.idx=".$member_idx."");
    }

    public static function getMembersFarm($member_idx) {
        return (new Database('member'))->execute("select f.* from member as m left join farm as f on m.idx = f.member_idx where m.member_type='manager' and f.idx is not null and m.idx=".$member_idx."");
    }

    public static function getMemberDetail($farm_idx) {
        return (new Database('member'))->execute("select * , m.created_at as member_created_at, f.idx as farm_idx from member as m left join farm as f on m.idx = f.member_idx where m.member_type='manager' and f.idx={$farm_idx} and f.idx is not null order by f.idx desc");
    }

    public static function getMemberDetailList() {
        return (new Database('member'))->execute("select * , f.idx as farm_idx from member as m left join farm as f on m.idx = f.member_idx where m.member_type='manager' and f.idx is not null order by f.idx desc");
    }

    public static function getMemberJoinFarm() {
        return (new Database('member'))->execute("select f.* from farm as f left join member as m on f.idx = m.member_farm_idx where m.idx is null");
    }

    public static function getMemberByIdx($idx) {
        return self::getMembers('idx ='.$idx)->fetchObject(self::class);
    }

    public static function getMemberById($member_id) {
        return self::getMembers("member_id='".$member_id."'")->fetchObject(self::class);
    }

    public static function getAdminMemberById($member_id) {
        return self::getMembers("member_type='admin' and member_id='".$member_id."'")->fetchObject(self::class);
    }

    public static function getManagerMemberById($member_id) {
        return self::getMembers("member_type='manager' and member_id='".$member_id."'")->fetchObject(self::class);
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
        ]);
    }

    public function deleted() {
        $this->idx = (new Database('member'))->delete('idx ='.$this->idx);
    }
}