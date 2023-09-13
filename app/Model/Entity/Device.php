<?php

namespace App\Model\Entity;

use \WilliamCosta\DatabaseManager\Database;

class Device {
    public $idx;
    public $farm_idx;
    public $address;
    public $board_type;
    public $board_number;
    public $created_at;


    public static function getDevicesByIdxAddress($farm_idx, $address) {
        return self::getDevices("farm_idx = ".$farm_idx." and address = '".$address."'");
    }

    public static function getDevicesByIdx($idx) {
        return self::getDevices('idx = '.$idx)->fetchObject(self::class);
    }

    public static function getDevices($where = null, $order = null, $limit = null, $fields = '*') {
        return (new Database('device'))->select($where, $order, $limit, $fields);
    }

    public function created() {
        $this->created_at = date('Y-m-d H:i:s');

        $this->idx = (new Database('device'))->insert([
            'farm_idx' => $this->farm_idx,
            'address' => $this->address,
            'board_type' => $this->board_type,
            'board_number' => $this->board_number,
            'created_at' => $this->created_at,
        ]);

        return $this->idx;
    }

    public function updated() {
        $this->idx = (new Database('device'))->update('idx ='.$this->idx,[
            'farm_idx' => $this->farm_idx,
            'address' => $this->address,
            'board_type' => $this->board_type,
            'board_number' => $this->board_number,
        ]);
    }

    public function deleted() {
        $this->idx = (new Database('device'))->delete('idx ='.$this->idx);
    }

}