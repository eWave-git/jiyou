<?php

namespace App\Model\Entity;

use \WilliamCosta\DatabaseManager\Database;

class ControlData {
    public $idx;

    public $device_idx;

    public $address;

    public $board_type;

    public $board_number;

    public $name;

    public $type;

    public $relay1;

    public $relay2;

    public $temperature;

    public $create_at;

    public function created() {
        $this->create_at = date('Y-m-d H:i:s');

        $this->idx = (new Database('control_data'))->insert([
            'device_idx' => $this->device_idx,
            'address' => $this->address,
            'board_type' => $this->board_type,
            'board_number' => $this->board_number,
            'name' => $this->name,
            'type' => $this->type,
            'relay1' => $this->relay1,
            'relay2' => $this->relay2,
            'temperature' => $this->temperature,
            'create_at' => $this->create_at,
        ]);

        return $this->idx;
    }


    public function updated() {
        $this->idx = (new Database('control_data'))->update('idx = '.$this->idx,[
            'device_idx' => $this->device_idx,
            'address' => $this->address,
            'board_type' => $this->board_type,
            'board_number' => $this->board_number,
            'name' => $this->name,
            'type' => $this->type,
            'relay1' => $this->relay1,
            'relay2' => $this->relay2,
            'temperature' => $this->temperature,
        ]);
    }

    public static function relayUpdate($idx, $field, $val) {
        return (new Database('control_data'))->execute(
            "update control_data set ".$field."= ".$val." where idx = ".$idx
        );
    }

    public static function temperatureUpdate($idx, $val) {
        return (new Database('control_data'))->execute(
            "update control_data set temperature = ".$val." where idx = ".$idx
        );
    }

    public static function getControlDataByDeviceIdx($idx) {
        return self::getControlData('device_idx ='.$idx);
    }

    public static function getControlDataByIDX($idx) {
        return self::getControlData('idx ='.$idx)->fetchObject(self::class);
    }

    public static function getControlData($where = null, $order = null, $limit = null, $fields = '*') {
        return (new Database('control_data'))->select($where, $order, $limit, $fields);
    }

    public function deleted() {
        $this->idx = (new Database('control_data'))->delete('idx ='.$this->idx);
    }

}