<?php

namespace App\Model\Entity;

use app\Utils\Common;
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


    public static function LastTotal($address, $board_type, $board_number, $field, $ago) {
        return (new Database('raw_data'))->execute("
            select
               min({$field}) as min,
               max({$field}) as max,
               avg({$field}) as avg
            from raw_data
            where address={$address} and board_type={$board_type} and board_number={$board_number} and created_at > (now() - INTERVAL {$ago} HOUR) and created_at < now()
            order by created_at desc
        ");
    }

    public static function LastLimitOne($address, $board_type, $board_number) {
        return (new Database('raw_data'))->execute("
            select *
            from raw_data
            where address={$address} and board_type={$board_type} and board_number={$board_number}
            order by idx desc limit 1
        ");
    }

    public static function LastLimitDataOne($address, $board_type, $board_number, $field, $name) {
        return (new Database('raw_data'))->execute("
            select {$field} as '{$name}'
            from raw_data
            where address={$address} and board_type={$board_type} and board_number={$board_number}
            order by idx desc limit 1
        ");
    }

    public static function LastLimitWaterDataSum($address, $board_type, $board_number, $field, $name, $interval) {
        return (new Database('raw_data'))->execute("
            select sum({$name}) as {$name} from (select
                (max({$field})-ifnull(LAG(max({$field})) OVER (ORDER BY created_at), {$field}))*10 as '{$name}'
            from raw_data
            where address={$address} and board_type={$board_type} and board_number={$board_number} and  created_at >= (now() - INTERVAL {$interval} DAY) and created_at <= now()
            group by FLOOR(DAY(created_at)/1)*10
            order BY idx asc) as temp        
        ");
    }

    public static function LastLimitWaterDataSumExcept_1($address, $board_type, $board_number, $field, $name, $interval) {
        return (new Database('raw_data'))->execute("
            select sum({$name}) as {$name} from (select
                (max({$field})-ifnull(LAG(max({$field})) OVER (ORDER BY created_at), {$field})) as '{$name}'
            from raw_data
            where address={$address} and board_type={$board_type} and board_number={$board_number} and  created_at >= (now() - INTERVAL {$interval} DAY) and created_at <= now()
            group by FLOOR(DAY(created_at)/1)*10
            order BY idx asc) as temp        
        ");
    }

    public static function LastLimitWaterDataSumExcept_2($address, $board_type, $board_number, $field, $name, $interval) {
        return (new Database('raw_data'))->execute("
            select sum({$name}) as {$name} from (select
                sum({$field}) as '{$name}'
            from raw_data
            where address={$address} and board_type={$board_type} and board_number={$board_number} and  created_at >= (now() - INTERVAL {$interval} DAY) and created_at <= now()
            group by FLOOR(DAY(created_at)/1)*10
            order BY idx asc) as temp        
        ");
    }

    public static function NowLastLimitDataOne($address, $board_type, $board_number, $field, $name) {
        return (new Database('raw_data'))->execute("
            select {$field} as '{$name}'
            from raw_data
            where address={$address} and board_type={$board_type} and board_number={$board_number} and  created_at > (now() - INTERVAL 1 minute )
            order by idx desc limit 1
        ");
    }

    public static function WaterDatesDay($address, $board_type, $board_number, $field, $name, $ago, $interval) {
        return (new Database('raw_data'))->execute("
            select
                date_format(created_at, '%Y-%m-%d') as created,
                (max({$field})-ifnull(LAG(max({$field})) OVER (ORDER BY created_at), {$field}))*10 as '{$name}'
            from raw_data
            where address={$address} and board_type={$board_type} and board_number={$board_number} and created_at >= (now() - INTERVAL {$ago} day )
            group by FLOOR(DAY(created_at)/{$interval})*10
            order BY idx asc  limit  1, {$ago};        
        ");
    }

    public static function AvgDatas($address, $board_type, $board_number, $field, $name, $ago, $interval) {
        return (new Database('raw_data'))->execute("
            select
                DATE_FORMAT(created_at, '%Y-%m-%d %H:%i:00') as created,
                avg({$field}) as '{$name}'
            from raw_data
            where address={$address} and board_type={$board_type} and board_number={$board_number} and  created_at > (now() - INTERVAL {$ago} HOUR ) and created_at < now()
            group by DAY(created_at),HOUR(created_at),FLOOR(MINUTE(created_at)/{$interval})*10
            order by created asc
        ");
    }

    public static function DatesBetweenDate($address, $board_type, $board_number, $field, $name, $start, $end) {
        return (new Database('raw_data'))->execute("
            select
                DATE_FORMAT(created_at, '%Y-%m-%d %H:%i:00') as created,
                {$field} as '{$name}'
            from raw_data
            where address={$address} and board_type={$board_type} and board_number={$board_number} and (created_at >= '{$start} 00:00:00' and created_at <= '{$end} 23:59:59')
            order by created asc
        ");
    }

    public static function WaterDatesBetweenDatesMinute($address, $board_type, $board_number, $field, $name, $start, $end) {
        return (new Database('raw_data'))->execute("
             select
                date_format(created_at, '%Y-%m-%d %H:%i:00') as created,
                (max({$field})-ifnull(LAG(max({$field})) OVER (ORDER BY created_at), {$field}))*10 as '{$name}'
            from raw_data
            where address={$address} and board_type={$board_type} and board_number={$board_number} and (created_at >= '{$start} 00:00:00' and created_at <= '{$end} 23:59:59')
            group by DAY(created_at),HOUR(created_at),FLOOR(MINUTE(created_at)/1)*10
            order BY idx asc
        ");
    }

    public static function WaterDates24HourAgo($address, $board_type, $board_number, $field, $name) {
        return (new Database('raw_data'))->execute("
             select
                date_format(created_at, '%Y-%m-%d %H:%i:00') as created,
                (max({$field})-ifnull(LAG(max({$field})) OVER (ORDER BY created_at), {$field}))*10 as '{$name}'
            from raw_data
            where address={$address} and board_type={$board_type} and board_number={$board_number} and  created_at > (now() - INTERVAL 24 HOUR ) and created_at < now()
            group by DAY(created_at),HOUR(created_at),FLOOR(MINUTE(created_at)/1)*10
            order BY idx asc
        ");
    }

    public static function AvgDatesBetweenDate($address, $board_type, $board_number, $interval, $field, $name, $start, $end) {

        return (new Database('raw_data'))->execute("
            select
                DATE_FORMAT(created_at, '%Y-%m-%d %H:%i:00') as created,
                avg({$field}) as '{$name}'
            from raw_data
            where address={$address} and board_type={$board_type} and board_number={$board_number} and (created_at >= '{$start} 00:00:00' and created_at <= '{$end} 23:59:59')
            group by DAY(created_at),HOUR(created_at),FLOOR(MINUTE(created_at)/{$interval})*10
            order by created asc
        ");
    }

    public static function WaterDatesBetweenDate($address, $board_type, $board_number, $interval, $field, $name, $start, $end) {

        return (new Database('raw_data'))->execute("
             select
                DATE_FORMAT(created_at, '%Y-%m-%d %H:%i:00') as created,
                (max({$field})-ifnull(LAG(max({$field})) OVER (ORDER BY created_at), {$field}))*10 as '{$name}'
            from raw_data
            where address={$address} and board_type={$board_type} and board_number={$board_number} and (created_at >= '{$start} 00:00:00' and created_at <= '{$end} 23:59:59')
            group by DAY(created_at),FLOOR(HOUR(created_at)/{$interval})*10
            order BY idx asc
        ");
    }


    public static function AccumulateDatas($address, $board_type, $field, $name, $ago, $interval) {
        return (new Database('raw_data'))->execute("
            select
                date_format(created_at, '%Y-%m-%d %H:%i:00') as created,
                (max({$field})-ifnull(LAG(max({$field})) OVER (ORDER BY created_at), {$field}))*10 as '{$name}'
            from raw_data
            where address={$address} and board_type={$board_type} and  created_at <= (now() - INTERVAL {$ago} HOUR )
            group by DAY(created_at),HOUR(created_at),FLOOR(MINUTE(created_at)/{$interval})*10
            order BY idx asc
        ");
    }
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