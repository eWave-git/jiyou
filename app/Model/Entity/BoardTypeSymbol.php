<?php

namespace App\Model\Entity;

use \WilliamCosta\DatabaseManager\Database;

class BoardTypeSymbol {
    public $idx;
    public $name;
    public $symbol;
    public $created_at;


    public static function getSymbolByIdx($idx) {
        return self::getBoardTypeSymbol('idx ='.$idx,'','','symbol')->fetchObject(self::class);
    }

    public static function getBoardTypeSymbol($where = null, $order = null, $limit = null, $fields = '*') {

        return (new Database('board_type_symbol'))->select($where, $order, $limit, $fields);
    }

    public function created() {
        $this->created_at = date('Y-m-d H:i:s');

        $this->idx = (new Database('board_type_symbol'))->insert([
            'name' => $this->name,
            'symbol' => $this->symbol,
            'created_at' => $this->created_at,
        ]);
    }

    public function updated() {
        $this->idx = (new Database('board_type_symbol'))->update('idx ='.$this->idx,[
            'name' => $this->name,
            'symbol' => $this->symbol,
        ]);
    }

    public function deleted() {
        $this->idx = (new Database('board_type_symbol'))->delete('idx ='.$this->idx);
    }
}