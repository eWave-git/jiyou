<?php
include_once __DIR__."/crontab_init.php";

use \WilliamCosta\DatabaseManager\Database;
use \App\Utils\Common;
use \App\Model\Entity\Device as EntityDevice;


$activation =  (new Database('alarm'))->execute(
    "select * ,a.idx as alarm_idx
            from alarm as a
                     left join alarm_member am on a.idx = am.alarm_idx
                     left join member as m on am.member_idx = m.idx
            where a.idx in (select max(idx)
                            from alarm
                            where activation = 'Y'
                            group by device_idx, board_type_field)
            and m.push_subscription_id is not null
            ");

$array = array();
$key = 0;

while ($activation_obj = $activation->fetchObject()) {

    $device_info = EntityDevice::getDevicesByIdx($activation_obj->device_idx);
    $raw_data_info = (new Database('raw_data'))->execute(
        "select idx, created_at, {$activation_obj->board_type_field} from raw_data
                where address='{$device_info->address}'
                  and board_type='{$device_info->board_type}'
                  and board_number='{$device_info->board_number}'
                order by idx desc limit 0, 1 ")->fetchObject();

    if ($activation_obj->alarm_range == "between") {
        if ($activation_obj->min > $raw_data_info->{$activation_obj->board_type_field} || $activation_obj->max < $raw_data_info->{$activation_obj->board_type_field}  ) {
            $_txt = "";
            $array[$key]['member_idx'] = $activation_obj->member_idx;
            $array[$key]['member_name'] = $activation_obj->member_name;
            $array[$key]['push_subscription_id'] = $activation_obj->push_subscription_id;

            $array[$key]['device_idx'] =  $activation_obj->device_idx;
            $array[$key]['board_type_field'] = $activation_obj->board_type_field;
            $array[$key]['board_type_name'] = $activation_obj->board_type_name;

            $array[$key]['alarm_idx'] = $activation_obj->alarm_idx;
            $_txt = "설정 ".$activation_obj->board_type_name." ".$activation_obj->min."~".$activation_obj->max." 범위를 넘어 알람 발생";
            $array[$key]['alarm_contents'] = $_txt;
            $array[$key]['min'] = $activation_obj->min;
            $array[$key]['max'] = $activation_obj->max;

            $array[$key]['raw_data_idx'] = $raw_data_info->idx;
            $array[$key]['raw_data_value'] = $raw_data_info->{$activation_obj->board_type_field};
            $array[$key]['raw_data_created_at'] = $raw_data_info->created_at;
        }

    } else if ($activation_obj->alarm_range == "up") {
        if ($activation_obj->max < $raw_data_info->{$activation_obj->board_type_field} ) {
            $_txt = "";
            $array[$key]['member_idx'] = $activation_obj->member_idx;
            $array[$key]['member_name'] = $activation_obj->member_name;
            $array[$key]['push_subscription_id'] = $activation_obj->push_subscription_id;

            $array[$key]['device_idx'] =  $activation_obj->device_idx;
            $array[$key]['board_type_field'] = $activation_obj->board_type_field;
            $array[$key]['board_type_name'] = $activation_obj->board_type_name;

            $array[$key]['alarm_idx'] = $activation_obj->alarm_idx;
            $_txt = "설정 ".$activation_obj->board_type_name." ".$activation_obj->max." 이상 알람 발생이지롱";
            $array[$key]['alarm_contents'] = $_txt;
            $array[$key]['min'] = $activation_obj->min;
            $array[$key]['max'] = $activation_obj->max;

            $array[$key]['raw_data_idx'] = $raw_data_info->idx;
            $array[$key]['raw_data_value'] = $raw_data_info->{$activation_obj->board_type_field};
            $array[$key]['raw_data_created_at'] = $raw_data_info->created_at;
        }
    } else if ($activation_obj->alarm_range == "down") {
        if ($activation_obj->min > $raw_data_info->{$activation_obj->board_type_field}) {
            $_txt = "";
            $array[$key]['member_idx'] = $activation_obj->member_idx;
            $array[$key]['member_name'] = $activation_obj->member_name;
            $array[$key]['push_subscription_id'] = $activation_obj->push_subscription_id;

            $array[$key]['device_idx'] =  $activation_obj->device_idx;
            $array[$key]['board_type_field'] = $activation_obj->board_type_field;
            $array[$key]['board_type_name'] = $activation_obj->board_type_name;

            $array[$key]['alarm_idx'] = $activation_obj->alarm_idx;
            $_txt = "설정 ".$activation_obj->board_type_name." ".$activation_obj->min." 이하 알람 발생";
            $array[$key]['alarm_contents'] = $_txt;
            $array[$key]['min'] = $activation_obj->min;
            $array[$key]['max'] = $activation_obj->max;

            $array[$key]['raw_data_idx'] = $raw_data_info->idx;
            $array[$key]['raw_data_value'] = $raw_data_info->{$activation_obj->board_type_field};
            $array[$key]['raw_data_created_at'] = $raw_data_info->created_at;
        }
    }

    $key++;
}

$alarmHistoryDatabases = new Database('alarm_history');                                                             //알람 히스토리에서 검색을 해서 최근 알람 보낸 간격이 'h' 기준으로 1시간 이상일때만 알람을 보낸다.

foreach ($array as $k => $v) {

    $results  = $alarmHistoryDatabases->select("alarm_idx = '{$v['alarm_idx']}'","created_at desc")->fetchObject();

    if (isset($results->alarm_idx)) {
        // "있다면";

        $diff = Common::date_diff($results->created_at, date("Y-m-d H:i:s"), 'i');                                          // 밑 뒤에 파라미터 값이 i이면 분, h이면 시간 d이면 날짜 마다 보냄
        if ($diff >= 1) {
           alarmHistoryInsert($v);
           Common::sendPush($v['board_type_name']." 경보발생", $v['alarm_contents'],$v['push_subscription_id'],"");
        }

    } else {
        // "없다면";

        alarmHistoryInsert($v);
        Common::sendPush($v['board_type_name']." 경보발생", $v['alarm_contents'],$v['push_subscription_id'],"");
    }
}

function alarmHistoryInsert($v) {
    $alarmHistoryDatabases = new Database('alarm_history');

        $alarmHistoryDatabases->insert([
            'member_idx' => $v['member_idx'],
            'member_name' => $v['member_name'],
            'push_subscription_id' => $v['push_subscription_id'],

            'device_idx' => $v['device_idx'],
            'board_type_field' => $v['board_type_field'],
            'board_type_name' => $v['board_type_name'],

            'alarm_idx' => $v['alarm_idx'],
            'alarm_contents' => $v['alarm_contents'],
            'min' => $v['min'],
            'max' => $v['max'],

            'raw_data_idx' => $v['raw_data_idx'],
            'raw_data_value' => $v['raw_data_value'],
            'raw_data_created_at' => $v['raw_data_created_at'],

            'created_at' => date("Y-m-d H:i:s"),
        ]);
}