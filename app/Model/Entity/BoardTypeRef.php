<?php

namespace App\Model\Entity;

use \WilliamCosta\DatabaseManager\Database;

class BoardTypeRef {
    public $idx;
    public $board_type;
    public $model_name;
    public $maker;
    public $use_count;
    public $data1;
    public $data2;
    public $data3;
    public $data4;
    public $data5;
    public $data6;
    public $data7;
    public $data8;
    public $created_at;


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
            'maker' => $this->maker,
            'use_count' => $this->use_count,
            'data1' => $this->data1,
            'data2' => $this->data2,
            'data3' => $this->data3,
            'data4' => $this->data4,
            'data5' => $this->data5,
            'data6' => $this->data6,
            'data7' => $this->data7,
            'data8' => $this->data8,
            'created_at' => $this->created_at,
        ]);
    }

    public function updated() {
        $this->idx = (new Database('board_type_ref'))->update('idx ='.$this->idx,[
            'board_type' => $this->board_type,
            'model_name' => $this->model_name,
            'maker' => $this->maker,
            'use_count' => $this->use_count,
            'data1' => $this->data1,
            'data2' => $this->data2,
            'data3' => $this->data3,
            'data4' => $this->data4,
            'data5' => $this->data5,
            'data6' => $this->data6,
            'data7' => $this->data7,
            'data8' => $this->data8,
        ]);
    }

    public function deleted() {
        $this->idx = (new Database('board_type_ref'))->delete('idx ='.$this->idx);
    }
}