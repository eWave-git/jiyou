<?php

namespace App\Model\Entity;

use \WilliamCosta\DatabaseManager\Database;

class FarmAddress {
    public $farm_idx;

    public $address;

    public $created_at;

    public static function getAddressByFarmIdx($farm_idx) {
        return self::getFarmAddress('farm_idx = '. $farm_idx,  'idx ASC', null);
    }

    public static function getAddressCnt($address) {
        return self::getFarmAddress("address ='".$address."'", null, null, 'COUNT(*) as cnt')->fetchObject(self::class)->cnt;
    }

    public static function getFarmAddress($where = null, $order = null, $limit = null, $fields = '*') {
        return (new Database('farm_address'))->select($where, $order, $limit, $fields);
    }

    public function save() {
        $this->created_at = date('Y-m-d H:i:s');

        $this->idx = (new Database('farm_address'))->insert([
            'farm_idx' => $this->farm_idx,
            'address' => $this->address,
            'created_at' => $this->created_at,
        ]);
    }
}