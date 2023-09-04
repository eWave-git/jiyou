<?php

namespace App\Model\Entity;

use \WilliamCosta\DatabaseManager\Database;

class BoardTypeRef {
    public $idx;
    public $board_type;
    public $model_name;

    public $sensor;
    public $data1;
    public $data2;
    public $data3;
    public $data4;
    public $data5;
    public $data6;
    public $data7;
    public $data8;
    public $constrol_type;
    public $created_at;


    public static function getBoardTypeRefByBoardType($board_type) {
        return self::getBoardTypeRef('board_type ='.$board_type)->fetchObject(self::class);
    }

    public static function getBoardTypeRefByIdx($idx) {
        return self::getBoardTypeRef('idx ='.$idx)->fetchObject(self::class);
    }

    public static function getBoardTypeRef($where = null, $order = null, $limit = null, $fields = '*') {

        return (new Database('board_type_ref'))->select($where, $order, $limit, $fields);
    }

    public function created() {
        $this->created_at = date('Y-m-d H:i:s');

        $this->idx = (new Database('board_type_ref'))->insert([
            'board_type' => $this->board_type,
            'model_name' => $this->model_name,
            'sensor' => $this->sensor,
            'data1' => $this->data1,
            'data2' => $this->data2,
            'data3' => $this->data3,
            'data4' => $this->data4,
            'data5' => $this->data5,
            'data6' => $this->data6,
            'data7' => $this->data7,
            'data8' => $this->data8,
            'control_type' => $this->control_type,
            'created_at' => $this->created_at,
        ]);
    }

    public function updated() {
        $this->idx = (new Database('board_type_ref'))->update('idx ='.$this->idx,[
            'board_type' => $this->board_type,
            'model_name' => $this->model_name,
            'sensor' => $this->sensor,
            'data1' => $this->data1,
            'data2' => $this->data2,
            'data3' => $this->data3,
            'data4' => $this->data4,
            'data5' => $this->data5,
            'data6' => $this->data6,
            'data7' => $this->data7,
            'data8' => $this->data8,
            'control_type' => $this->control_type,
        ]);
    }

    public function deleted() {
        $this->idx = (new Database('board_type_ref'))->delete('idx ='.$this->idx);
    }
}