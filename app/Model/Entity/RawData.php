<?php

namespace App\Model\Entity;

use http\Encoding\Stream\Inflate;
use \WilliamCosta\DatabaseManager\Database;

class RawData {

    public $idx;

    public $address;

    public $board_type;

    public $board_number;

    public $data1;
    public $data2;
    public $data3;
    public $data4;
    public $data5;
    public $data6;
    public $data7;
    public $data8;
    public $created_at;


    public static function TwoAvgData($data1, $data2, $interval, $minute_interval) {
        return (new Database('raw_data'))->execute("
            select
                DATE_FORMAT(created_at, '%Y-%m-%d %H:%i:00') as created,
                avg({$data1}) as {$data1},
                avg({$data2}) as {$data2}
            from raw_data
            where (created_at >= now() - INTERVAL {$interval} HOUR )
            group by HOUR(created_at),FLOOR(MINUTE(created_at)/{$minute_interval})*10
            order by created asc
        ");
    }

    public static function TwoAvgDataTest($data1, $data2, $interval, $minute_interval) {
        return (new Database('raw_data'))->execute("
            select
                DATE_FORMAT(created_at, '%Y-%m-%d %H:%i:00') as created,
                avg({$data1}) as {$data1},
                avg({$data2}) as {$data2}
            from raw_data
            where (created_at >= '2023-08-02 12:00:00' - INTERVAL {$interval} HOUR )
            group by HOUR(created_at),FLOOR(MINUTE(created_at)/{$minute_interval})*10
            order by created asc
        ");
    }

    public static function getRawData($where = null, $order = null, $limit = null, $fields = '*') {
        return (new Database('raw_data'))->select($where, $order, $limit, $fields);
    }
}