<?php

namespace App\Model\Entity;

use \WilliamCosta\DatabaseManager\Database;

class Setting {
    public $idx;
    public $device_idx;

    public $address;

    public $board_type;

    public $board_number;

    public $board_type_field;

    public $board_type_name;

    public $min;

    public $max;

    public $activation;

    public $created_at;


    public function created() {
        $this->created_at = date('Y-m-d H:i:s');

        $this->idx = (new Database('setting'))->insert([
            'device_idx' => $this->device_idx,
            'address' => $this->address,
            'board_type' => $this->board_type,
            'board_number' => $this->board_number,
            'board_type_field' => $this->board_type_field,
            'board_type_name' => $this->board_type_name,
            'min' => $this->min,
            'max' => $this->max,
            'activation' => $this->activation,
            'created_at' => $this->created_at,
        ]);

        return $this->idx;
    }

    public function updated() {
        $this->idx = (new Database('setting'))->update('idx = '.$this->idx,[
            'device_idx' => $this->device_idx,
            'address' => $this->address,
            'board_type' => $this->board_type,
            'board_number' => $this->board_number,
            'board_type_field' => $this->board_type_field,
            'board_type_name' => $this->board_type_name,
            'min' => $this->min,
            'max' => $this->max,
            'activation' => $this->activation,
        ]);
    }

    public static function getSettingByDeviceIdx($idx) {
        return self::getSetting('device_idx ='.$idx);
    }
    public static function getSettingByIdx($idx) {
        return self::getSetting('idx ='.$idx)->fetchObject(self::class);
    }
    public static function getSetting($where = null, $order = null, $limit = null, $fields = '*') {
        return (new Database('setting'))->select($where, $order, $limit, $fields);
    }

    public function deleted() {
        $this->idx = (new Database('setting'))->delete('idx ='.$this->idx);
    }
}