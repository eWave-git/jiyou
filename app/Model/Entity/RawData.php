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


    public static function LastTotal($address, $board_type, $board_number, $field, $ago) {                                      // 대쉬보드 --> 표보기 최소, 최대, 평균값
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

    public static function LastLimitOne($address, $board_type, $board_number) {                                                 // 대쉬보드 --> 페이지에 마지막 데이터 보여주기
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

    // 20240112 메인 대쉬보드에 카드상에 표현을 최근 24시간 동안 먹은 물의 양에서 금일 0시부터 지금시간까지 먹은 물의 양으로 변경, 변수 interval이 갈곳이 없어서 아래로 내림
    public static function LastLimitWaterDataSum($address, $board_type, $board_number, $field, $name, $interval) {              // 대쉬보드 --> 메인카드에 음수양 표현 board_type = 3인 경우          public static function getCardItem($rew_obj, $board_name) {
        return (new Database('raw_data'))->execute("
            select sum({$name}) as {$name} from (select
                (max({$field})-ifnull(LAG(max({$field})) OVER (ORDER BY created_at), {$field}))*10 as '{$name}'
            from raw_data
            where address={$address} and board_type={$board_type} and board_number={$board_number} and  created_at > current_date()
            group by FLOOR(DAY(created_at)/1)*10*{$interval}
            order BY idx asc) as temp        
        ");
    }

    // 20240112 메인 대쉬보드에 카드상에 표현을 최근 24시간 동안 먹은 물의 양에서 금일 0시부터 지금시간까지 먹은 물의 양으로 변경, 변수 interval이 갈곳이 없어서 아래로 내림
    public static function LastLimitWaterDataSumExcept_1($address, $board_type, $board_number, $field, $name, $interval) {      // 대쉬보드 --> 메인카드에 음수양 표현 board_type = 6 또는 35인 경우
        return (new Database('raw_data'))->execute("
            select sum({$name}) as {$name} from (select
                (max({$field})-ifnull(LAG(max({$field})) OVER (ORDER BY created_at), {$field})) as '{$name}'
            from raw_data
            where address={$address} and board_type={$board_type} and board_number={$board_number} and created_at > current_date()
            group by FLOOR(DAY(created_at)/1)*10*{$interval}
            order BY idx asc) as temp        
        ");
    }
/*
    public static function LastLimitWaterDataSum($address, $board_type, $board_number, $field, $name, $interval) {              // 대쉬보드 --> 메인카드에 음수양 표현 board_type = 3인 경우          public static function getCardItem($rew_obj, $board_name) {
        return (new Database('raw_data'))->execute("
            select sum({$name}) as {$name} from (select
                (max({$field})-ifnull(LAG(max({$field})) OVER (ORDER BY created_at), {$field}))*10 as '{$name}'
            from raw_data
            where address={$address} and board_type={$board_type} and board_number={$board_number} and  created_at >= (now() - INTERVAL {$interval} DAY) and created_at <= now()
            group by FLOOR(DAY(created_at)/1)*10
            order BY idx asc) as temp        
        ");
    }

    public static function LastLimitWaterDataSumExcept_1($address, $board_type, $board_number, $field, $name, $interval) {      // 대쉬보드 --> 메인카드에 음수양 표현 board_type = 6 또는 35인 경우
        return (new Database('raw_data'))->execute("
            select sum({$name}) as {$name} from (select
                (max({$field})-ifnull(LAG(max({$field})) OVER (ORDER BY created_at), {$field})) as '{$name}'
            from raw_data
            where address={$address} and board_type={$board_type} and board_number={$board_number} and  created_at >= (now() - INTERVAL {$interval} DAY) and created_at <= now()
            group by FLOOR(DAY(created_at)/1)*10
            order BY idx asc) as temp        
        ");
    }
*/
    public static function LastLimitWaterDataSumExcept_2($address, $board_type, $board_number, $field, $name, $interval) {      // 대쉬보드 --> 메인카드에 음수양 표현 board_type = 4인 경우
        return (new Database('raw_data'))->execute("
            select sum({$name}) as {$name} from (select
                sum({$field}) as '{$name}'
            from raw_data
            where address={$address} and board_type={$board_type} and board_number={$board_number} and  created_at >= (now() - INTERVAL {$interval} DAY) and created_at <= now()
            group by FLOOR(DAY(created_at)/1)*10
            order BY idx asc) as temp        
        ");
    }

    public static function NowLastLimitDataOne($address, $board_type, $board_number, $field, $name) {                           // 대쉬보드 --> 표보기 에서 최근값 "1분전 최근값 나타내기"
        return (new Database('raw_data'))->execute("
            select {$field} as '{$name}'
            from raw_data
            where address={$address} and board_type={$board_type} and board_number={$board_number} and  created_at > (now() - INTERVAL 1 minute )
            order by idx desc limit 1
        ");
    }

    public static function WaterDatesDay($address, $board_type, $board_number, $field, $name, $ago, $interval) {            //대쉬보드 --> 표보기 에서 최근 24시간 물 사용 테이블 불러오는 쿼리  //dashboard.php의 public static function getTableInWidgetDataWater($widget_obj, $board_type_array) {
        return (new Database('raw_data'))->execute("
            select
                date_format(created_at, '%Y-%m-%d') as created,
                (max({$field})-ifnull(LAG(max({$field})) OVER (ORDER BY created_at), {$field}))*1 as '{$name}'
            from raw_data
            where address={$address} and board_type={$board_type} and board_number={$board_number} and created_at >= (now() - INTERVAL {$ago} day )
            group by FLOOR(DAY(created_at)/{$interval})*10
            order BY idx asc  limit  0, {$ago};        
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

    public static function WaterDatesBetweenDatesMinute($address, $board_type, $board_number, $field, $name, $start, $end) {            //조회-데이터에서 물 사용량 조회  inquiry.php public static function getTableSearch($request) {                 //group by DAY(created_at),HOUR(created_at),FLOOR(MINUTE(created_at)/1)*10     1분단위 조회 코드         //group by DAY(created_at),FLOOR(HOUR(created_at)/1)*10   1시간 단위 코드
        return (new Database('raw_data'))->execute("
            select
                date_format(created_at, '%Y-%m-%d %H:%i:00') as created,
                (max({$field})-ifnull(LAG(max({$field})) OVER (ORDER BY created_at), {$field}))*1 as '{$name}'
            from raw_data
            where address={$address} and board_type={$board_type} and board_number={$board_number} and (created_at >= '{$start} 00:00:00' and created_at <= '{$end} 23:59:59')
            group by DAY(created_at),FLOOR(HOUR(created_at)/1)*10
            order BY idx asc
        ");
    }
    // 수정본 최근 24시간에서 최근 일주일로 그래프를 변경
    public static function WaterDates24HourAgo($address, $board_type, $board_number, $field, $name) {                // 대쉬보드 --> 그래프 보기에서 최근 일주일 물 그래프 불러오는 쿼리  
        return (new Database('raw_data'))->execute("
             select
                date_format(created_at, '%Y-%m-%d %h:00:00' ) as created,
                (max({$field})-ifnull(LAG(max({$field})) OVER (ORDER BY created_at), {$field}))*1 as '{$name}'
            from raw_data
            where address={$address} and board_type={$board_type} and board_number={$board_number} and  created_at > (now() - INTERVAL 7 day ) 
            group by FLOOR(DAY(created_at)/1)*10
            order BY idx asc
            Limit 0, 7
        ");
    }
    /*  // 원본
    public static function WaterDates24HourAgo($address, $board_type, $board_number, $field, $name) {                // 대쉬보드 --> 그래프 보기에서 최근 24시간 물 그래프 불러오는 쿼리  //dashboard.php의 public static function getChart($request) {                 //  group by DAY(created_at),HOUR(created_at),FLOOR(MINUTE(created_at)/1)*10
        return (new Database('raw_data'))->execute("
             select
                date_format(created_at, '%Y-%m-%d %H:%i:00') as created,
                (max({$field})-ifnull(LAG(max({$field})) OVER (ORDER BY created_at), {$field}))*1 as '{$name}'
            from raw_data
            where address={$address} and board_type={$board_type} and board_number={$board_number} and  created_at > (now() - INTERVAL 24 HOUR ) and created_at < now()
            group by DAY(created_at),FLOOR(HOUR(created_at)/1)*10
            order BY idx asc
            Limit 1, 36
        ");
    }
    */
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

    public static function WaterDatesBetweenDate($address, $board_type, $board_number, $interval, $field, $name, $start, $end) {        //조회-그래프에서 물 사용량 조회  inquiry.php public static function getTableSearch($request) {

        return (new Database('raw_data'))->execute("
            select
                DATE_FORMAT(created_at, '%Y-%m-%d %H:%i:00') as created,
                (max({$field})-ifnull(LAG(max({$field})) OVER (ORDER BY created_at), {$field}))*1 as '{$name}'
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