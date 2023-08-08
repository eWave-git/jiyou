<?php

namespace App\Model\Entity;

use \WilliamCosta\DatabaseManager\Database;

class Farm {
    public $idx;
    public $farm_name;
    public $farm_ceo;
    public $farm_address;
    public $address;
    public $member_idx;
    public $created_at;


    public static function getFarmsByIdx($idx) {
        return self::getFarms('idx ='.$idx)->fetchObject(self::class);
    }

    public static function getFarms($where = null, $order = null, $limit = null, $fields = '*') {
        return (new Database('farm'))->select($where, $order, $limit, $fields);
    }

    public function created() {
        $this->created_at = date('Y-m-d H:i:s');

        $this->idx = (new Database('farm'))->insert([
            'farm_name' => $this->farm_name,
            'farm_ceo' => $this->farm_ceo,
            'farm_address' => $this->farm_address,
            'address' => $this->address,
            'member_idx' => $this->member_idx,
            'created_at' => $this->created_at,
        ]);
    }

    public function updated() {
        $this->idx = (new Database('farm'))->update('idx ='.$this->idx,[
            'farm_name' => $this->farm_name,
            'farm_ceo' => $this->farm_ceo,
            'farm_address' => $this->farm_address,
            'member_idx' => $this->member_idx,
            'address' => $this->address,
        ]);
    }

    public function deleted() {
        $this->idx = (new Database('farm'))->delete('idx ='.$this->idx);
    }


}