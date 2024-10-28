<?php
namespace app\Utils;

use App\Model\Entity\AlarmControl as EntityAlarmControl;
use \WilliamCosta\DatabaseManager\Database;
use App\Model\Entity\RawData as EntityRawData;
use App\Controller\Admin\BoardTypeRef;
use App\Model\Entity\BoardTypeRef as EntityBoardTypeRef;
use App\Model\Entity\Member as EntityMmeber;
use App\Model\Entity\Widget as EntityWidget;
use App\Model\Entity\WidgetBoardType as EntityWidgetBoardType;
use App\Model\Entity\BoardTypeSymbol as EntityBoardTypeSymbol;
use App\Model\Entity\PushSendLog as EntityPushSendLog;
use App\Model\Entity\SmsSendLog as EntitySmsSendLog;
use App\Model\Entity\AligoSmsSendLog as EntityAligoSmsSendLog;
use App\Model\Entity\Device as EntityDevice;
use DateInterval;
use DatePeriod;
use DateTime;
use Twilio\Rest\Client as TwilioRestClient;


class Common{

    private static $vars = [];

    public static function init($vars = []) {
        self::$vars = $vars;
    }


     public static function print_r2($vars) {
        echo "<pre>";
        print_r($vars);
        echo "</pre>";
        exit;
    }

    public static function var_dump2($vars) {
        echo "<pre>";
        var_dump($vars);
        echo "</pre>";
        exit;
    }


    public static function str_chekc($str, $msg) {

        if (!isset($str) || empty($str)) {
            self::error_msg($msg);
            exit;
        }

        return $str;
    }

    public static function int_check($int, $msg) {

        if (!is_numeric($int)) {
            self::error_msg($msg);
            exit;
        }

        return $int;
    }

    public static function get_member_info($idx) {
        $obj = (array) EntityMmeber::getMemberByIdx($idx);

        return $obj;
    }

    public static function get_manager() {
        if (empty($_SESSION['manager'])) return null;

        return $_SESSION['manager']['user']['id'];
    }

    public static function getInterval($key = '') {
        if ($key) {
            $interval = array(
                "PT1M" => "1",
                "PT5M" => "5",
                "PT10M" => "10",
                "PT30M" => "30",
                "PT60M" => "60",
            );

            $interval = $interval[$key];
        } else {
            $interval = array(
                "PT1M" => "1분",
                "PT5M" => "5분",
                "PT10M" => "10분",
                "PT30M" => "30분",
                "PT60M" => "1시간",
            );
        }
        return $interval;
    }


    // TODO : date_range
    /**
     *
     * @param $startDate
     * @param $lastDate
     * @return array|string
     *
     * getDatesStartToLast("2020-09-25", "2020-09-25")
     */
    public static function getDatesStartToLast($startDate, $lastDate) {
        $regex = "/^\d{4}-(0[1-9]|1[012])-(0[1-9]|[12][0-9]|3[0-1])$/";
        if(!(preg_match($regex, $startDate) && preg_match($regex, $lastDate))) return "Not Date Format";
        $period = new DatePeriod( new DateTime($startDate), new DateInterval('PT1M'), new DateTime($lastDate." +1 day"));
        foreach ($period as $date) $dates[] = $date->format("Y-m-d H:i:s");
        return $dates;
    }

    public static function date_range($first, $last, $step = '+1 day', $output_format = 'd/m/Y' )  {
        $dates = array();
        $current = strtotime($first);
        $last = strtotime($last);

        while( $current <= $last ) {
            //$dates[] = date($output_format, $current);
            $dates[] = date($output_format, $current);
            $current = strtotime($step, $current);
        }

        return $dates;
    }

    public static function date_diff($startDate, $lastDate, $format='days') {
        $startDate = new DateTime($startDate);
        $lastDate = new DateTime($lastDate);

        $date_diff = date_diff($startDate, $lastDate);

        return $date_diff->$format;
    }

    public static function date_diff_minutes($startDate, $lastDate) {
        $startDate = new DateTime($startDate);
        $lastDate = new DateTime($lastDate);

        $interval = date_diff($startDate, $lastDate);
        $minutes = ($interval->days * 24 * 60) + ($interval->h * 60) + $interval->i;

        return $minutes;
    }

    public static function error_msg($msg) {
        echo "<script language='javascript'>alert('$msg');history.back();</script>";
        exit;
    }

    public static function error_loc_msg($loc, $msg, $target=null)  {
        if($target) { echo "<script language='javascript'>alert('$msg');".$target.".location.href=('${loc}');</script>"; }
        else { echo "<script language='javascript'>alert('$msg');location.href=('${loc}');</script>"; }
        exit;
    }

    public static function getBoardTypeNameSelect($device_idx, $board_type, $field) {
        $array = array();

        foreach (Common::getbordTypeNameByWidgetNameArray($device_idx, $board_type) as $k => $v) {
            if ($v['field'] == $field) {
                $array = $v;
            }
        }

        return $array;
    }

    public static function getBoardTypeSymbol() {
        $symbols_array = array();

        $symbols_result = EntityBoardTypeSymbol::getBoardTypeSymbol('','','','*');
        while ($obj_symbols = $symbols_result->fetchObject(EntityBoardTypeSymbol::class)) {
            $symbols_array[] = (array) $obj_symbols;
        }

        return $symbols_array;
    }

    public static function findSymbol($board_type_name) {
        $symbols_array = self::getBoardTypeSymbol();

        $array = array_filter(  $symbols_array, function($v, $k) use ($board_type_name) {
                    return preg_match('/'.$v['name'].'/i', $board_type_name);
                },ARRAY_FILTER_USE_BOTH );


        if (count($array) > 0) {
            return array_values($array)[0];
        }
    }

    public static function temperature_commend($address, $board_type, $board_number, $temperature) {
        $_txt = $address.'/'.$board_type.'/'.$board_number;
        $commend = 'mosquitto_pub -h 13.209.31.152 -t LORA/GATE/CONTROL/'.$_txt.' -u ewave -P andante -m "{\"temp\":'.$temperature.'}"';

        $output=null;
        $retval=null;
        exec($commend, $output, $retval);
    }

    public static function ch4_commend($address, $board_type, $board_number, $ch1, $ch2, $ch3, $ch4) {
        $commend = 'mosquitto_pub -h 13.209.31.152 -p 1883 -t "safe1/control/'.$address.'" -u "ewave" -P "andante" -m "{\"bt\":'.$board_type.',\"bn\":'.$board_number.',\"cv\":4,\"c1\":'.$ch1.',\"c2\":'.$ch2.',\"c3\":'.$ch3.',\"c4\":'.$ch4.'}"';

        $output=null;
        $retval=null;

        exec($commend, $output, $retval);
    }

    public static function getMembersDevice($member_idx) {
        $arr  = array();

        $result = EntityMmeber::getMembersDevice($member_idx);
        $_i = 0;
        while ($obj = $result->fetchObject(EntityMmeber::class)) {
            $arr[$_i] = (array) $obj;
            $arr[$_i]['board_name'] =  BoardTypeRef::getBoardTypeName($obj->board_type);
            $_i++;
        }

        return $arr;
    }

    public static function getMembersWidget($member_idx) {
        $arr  = array();

        $result = EntityWidget::getWidgetByMemberIdx($member_idx);
        $_i = 0;
        while ($obj = $result->fetchObject(EntityWidget::class)) {
            $arr[$_i] = (array) $obj;
            $arr[$_i]['board_name'] =  Common::getbordTypeNameByWidgetNameArray($obj->device_idx, $obj->board_type);

            $_i++;
        }

        return $arr;
    }

    public static function getbordTypeNameByWidgetNameArray($device_idx, $board_type) {

        $array = array();
        $objBoardTypeRefOrg = EntityBoardTypeRef::getBoardTypeRefByBoardType($board_type);
        $objBoardTypeRef = EntityWidgetBoardType::getWidgetWithWidgetBoardTypeByIdx($device_idx);

        if ($objBoardTypeRefOrg) {
            $i = 0;
            foreach($objBoardTypeRefOrg as $column_name=>$column_value){

                if (preg_match('/data/',$column_name, $match) && $column_value) {
                    $type_name = $column_name."_name";
                    $type_display = $column_name."_display";
                    $type_symbol = $column_name."_symbol";

                    $array[$i]['field'] = $column_name;
                    $array[$i]['org_name'] = $column_value;
                    $array[$i]['name'] = $objBoardTypeRef->{$type_name} ?? $column_value;
                    $array[$i]['display'] = $objBoardTypeRef->{$type_display} ?? 'Y';

                    $symbol = '';
                    if (!isset($objBoardTypeRef->{$type_symbol})) {
                        $_symbol = Common::findSymbol($column_value);
                        $symbol = !isset($_symbol['symbol']) ? '' : $_symbol['symbol'];
                    } else {
                        $symbol = $objBoardTypeRef->{$type_symbol};
                    }

                    $array[$i]['symbol'] =  $symbol;
                    $i++;
                }
            }
        }

        return $array;
    }

    public static function getMembersControlDevice($user_idx) {
        $arr  = array();

        $result = EntityMmeber::getMembersControlDevice($user_idx);
        $_i = 0;
        while ($obj = $result->fetchObject(EntityMmeber::class)) {
            $arr[$_i] = (array) $obj;
            $_i++;
        }

        return $arr;
    }


    public static function getBoardTypeNameArray($board_type) {
        $array = array();

        $objBoardTypeRef = EntityBoardTypeRef::getBoardTypeRefByBoardType($board_type);

        if ($objBoardTypeRef) {
            $i = 0;
            foreach($objBoardTypeRef as $column_name=>$column_value){
                if (preg_match('/data/',$column_name, $match) && $column_value) {
                    $array[$i]['field'] = $column_name;
                    $array[$i]['name'] = $column_value;

                    $i++;
                }
            }
        }

        return $array;
    }

    public static function sendPush($push_title, $push_content, $individual, $link_url = '') {
        $push_target = array('');
        $individual_arr = array();

        $individual_arr[] = $individual;

        $push_title_arr['en'] = $push_title;
        $push_content_arr['en'] = $push_content;
        $data['custom_url'] = $link_url;

        $url = "https://onesignal.com/api/v1/notifications";
        $body = array(
            "app_id" => ONESIGNAL_APP,
            "included_segments" => $push_target,
            "include_player_ids" => $individual_arr,
            "headings" => $push_title_arr,
            "contents" => $push_content_arr,
            "data" => $data,
            "small_icon" => "icon_48",
            "big_picture" => "",
            "ios_attachments" => "",
            "ios_badgeType" => "Increase",
            "ios_badgeCount" => "1"
        ); # type1
        $body = json_encode($body);

        $ch = curl_init();
        curl_setopt_array($ch, array(
            CURLOPT_URL			    => $url, //URL 지정하기
            CURLOPT_POST			=> true, //true시 post 전송
            CURLOPT_RETURNTRANSFER	=> true, //요청 결과를 문자열로 반환
            CURLOPT_HTTPHEADER		=> array(// header data
                "Content-Type:application/json",
                "Authorization: Basic ".ONESIGNAL_API_KEY
            ),
            CURLOPT_SSL_VERIFYPEER	=> 0,    //원격 서버의 인증서가 유효한지 검사 안함
            CURLOPT_POSTFIELDS		=> $body //POST data
        ));

        $response = curl_exec($ch);
        $status_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);


        $obj = new EntityPushSendLog;
        $obj->push_title = $push_title;
        $obj->push_content = $push_content;
        $obj->individual = $individual;
        $obj->link_url = $link_url;
        $obj->status_code = $status_code;
        $obj->created();


        return $status_code;
    }

    public static function sendSms($to, $body)
    {
        $twilioClient = new TwilioRestClient(getenv('TWILIO_SID'), getenv('TWILIO_TOKEN'));
        $message = $twilioClient->messages->create("+82".$to, // to
            array(
              "from" => "+19015992266",
              "body" => $body
            )
          );

        $obj = new EntitySmsSendLog;
        $obj->body = $message->body;
        $obj->numSegments = $message->numSegments;
        $obj->direction = $message->direction;
        $obj->smsFrom = $message->from;
        $obj->smsTo = $message->to;
        $obj->errorMessage = $message->errorMessage ?? '';
        $obj->status = $message->status;
        $obj->sid = $message->sid;
        $obj->created();

        return $message->status;
    }

    public static function aligoSendSms($title, $msg, $receiver)
    {
        $sms_url = getenv('ALIGO_SMS_URL');
        $sms['user_id'] = getenv('ALIGO_USER_ID');
        $sms['key'] = getenv('ALIGO_KEY');


        $sms['msg'] = stripslashes($msg);
        $sms['receiver'] = $receiver;
        $sms['sender'] = '01063423999';
        $sms['title'] = $title;
        $sms['msg_type'] = 'LMS';

        $oCurl = curl_init();
        curl_setopt($oCurl, CURLOPT_PORT, 443);
        curl_setopt($oCurl, CURLOPT_URL, $sms_url);
        curl_setopt($oCurl, CURLOPT_POST, 1);
        curl_setopt($oCurl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($oCurl, CURLOPT_POSTFIELDS, $sms);
        curl_setopt($oCurl, CURLOPT_SSL_VERIFYPEER, FALSE);
        $ret = curl_exec($oCurl);
        curl_close($oCurl);


        $retArr = json_decode($ret);

        $obj = new EntityAligoSmsSendLog();
        $obj->title = $sms['title'];
        $obj->msg = $sms['msg'];
        $obj->receiver = $sms['receiver'];
        $obj->result_code = $retArr->result_code;
        $obj->message = $retArr->message;
        $obj->msg_id = $retArr->msg_id ?? 0;
        $obj->success_cnt = $retArr->success_cnt ?? 0;
        $obj->error_cnt = $retArr->error_cnt ?? 0;
        $obj->msg_type = $retArr->msg_type ?? '';
        $obj->created();

    }

    public static function widgetConnectionCheck($address, $board_type, $number, $check_time = 0) {
        $result = true;

        $raw_result = EntityRawData::LastLimitOne($address, $board_type, $number);
        $rew_obj = $raw_result->fetchObject(EntityRawData::class);
        if (!empty($rew_obj)) {
            $diff = Common::date_diff_minutes($rew_obj->created_at,  date("Y-m-d H:i:s"));

            if ($diff >= $check_time) {
                $result = false;
            }

        } else {
            $result = false;
        }

        return $result;
    }

    public static function alarm_validity_check($obj) {

        $check = array();
        $check['reslut'] = false;

        if (!empty($obj)) {
            $device_info = EntityDevice::getDevicesByIdx($obj->device_idx);

            $raw_data_info = (new Database('raw_data'))->execute(
                "select idx, created_at, {$obj->board_type_field} from raw_data
                        where address='{$device_info->address}'
                          and board_type='{$device_info->board_type}'
                          and board_number='{$device_info->board_number}'
                        order by idx desc limit 0, 1 ")->fetchObject();

            if ($obj->alarm_range == "between") {

                if ($obj->min > $raw_data_info->{$obj->board_type_field} || $obj->max < $raw_data_info->{$obj->board_type_field}  ) {
                    $check['reslut'] = true;
                    $check['range_value'] = "(".$obj->min."~".$obj->max.")";
                    $check['raw_data_idx'] = $raw_data_info->idx;
                    $check['raw_data_value'] = $raw_data_info->{$obj->board_type_field};
                    $check['raw_data_created_at'] = $raw_data_info->created_at;
                }

            } else if ($obj->alarm_range == "up") {
                if ($obj->max < $raw_data_info->{$obj->board_type_field} ) {
                    $check['reslut'] = true;
                    $check['range_value'] = "(".$obj->max.")";
                    $check['raw_data_idx'] = $raw_data_info->idx;
                    $check['raw_data_value'] = $raw_data_info->{$obj->board_type_field};
                    $check['raw_data_created_at'] = $raw_data_info->created_at;
                }
            } else if ($obj->alarm_range == "down") {
                if ($obj->min > $raw_data_info->{$obj->board_type_field}) {
                    $check['reslut'] = true;
                    $check['range_value'] = "(".$obj->min.")";
                    $check['raw_data_idx'] = $raw_data_info->idx;
                    $check['raw_data_value'] = $raw_data_info->{$obj->board_type_field};
                    $check['raw_data_created_at'] = $raw_data_info->created_at;
                }
            }

        }

        return $check;
    }

    public static function water_alarm_validity_check($obj) {

        $check = array();
        $check['reslut'] = false;

        if (!empty($obj)) {
            $device_info = EntityDevice::getDevicesByIdx($obj->device_idx);

            $raw_data_info = (new Database('raw_data'))->execute(
        "select sum(L) as 'water' from (
                    select
                           (max({$obj->board_type_field}) - ifnull(LAG(max({$obj->board_type_field})) OVER (ORDER BY created_at), {$obj->board_type_field})) * 1 as 'L'
                    from raw_data
                    where address = '{$device_info->address}'
                      and board_type = '{$device_info->board_type}'
                      and board_number = '{$device_info->board_number}'
                      and created_at > (now() - INTERVAL 1 HOUR ) and created_at < now()
                    group by DAY(date_format(created_at, '%Y-%m-%d %H:%i:00')), FLOOR(HOUR(date_format(created_at, '%Y-%m-%d %H:%i:00')) / 1) * 10
                    order BY idx asc
                ) as Temp")->fetchObject();

            if ($obj->alarm_range == "between") {

                if ($obj->min > $raw_data_info->water || $obj->max < $raw_data_info->water  ) {
                    $check['reslut'] = true;
                    $check['range_value'] = "(".$obj->min."~".$obj->max.")";
                    $check['raw_data_value'] = $raw_data_info->water;
                }

            } else if ($obj->alarm_range == "up") {
                if ($obj->max < $raw_data_info->water ) {
                    $check['reslut'] = true;
                    $check['range_value'] = "(".$obj->max.")";
                    $check['raw_data_value'] = $raw_data_info->water;
                }
            } else if ($obj->alarm_range == "down") {
                if ($obj->min > $raw_data_info->water) {
                    $check['reslut'] = true;
                    $check['range_value'] = "(".$obj->min.")";
                    $check['raw_data_value'] = $raw_data_info->water;
                }
            }
        }

        return $check;
    }

    public static function alarmHistoryInsert($member_idx, $alarm_contents, $alarm_history_array) {
    $member_info = EntityMmeber::getMemberByIdx($member_idx);

        foreach ($alarm_history_array as $k => $v) {
            $alarmHistoryDatabases = new Database('alarm_history');
            $alarmHistoryDatabases->insert([
                'member_idx' => $member_info->idx,
                'member_name' => $member_info->member_name,
                'push_subscription_id' => $member_info->push_subscription_id,

                'device_idx' => $v['device_idx'],
                'board_type_field' => $v['board_type_field'],
                'board_type_name' => $v['board_type_name'],

                'alarm_idx' => $v['alarm_idx'],
                'alarm_contents' => $alarm_contents,
                'min' => $v['min'],
                'max' => $v['max'],

                'raw_data_idx' => $v['raw_data_idx'],
                'raw_data_value' => $v['raw_data_value'],
                'raw_data_created_at' => $v['raw_data_created_at'],

                'created_at' => date("Y-m-d H:i:s"),
            ]);
        }
    }

    public static function wateralarmHistoryInsert($member_idx, $alarm_contents, $alarm_history_array) {
    $member_info = EntityMmeber::getMemberByIdx($member_idx);

        foreach ($alarm_history_array as $k => $v) {
            $alarmHistoryDatabases = new Database('water_alarm_history');
            $alarmHistoryDatabases->insert([
                'member_idx' => $member_info->idx,
                'member_name' => $member_info->member_name,
                'push_subscription_id' => $member_info->push_subscription_id,

                'device_idx' => $v['device_idx'],
                'board_type_field' => $v['board_type_field'],
                'board_type_name' => $v['board_type_name'],

                'water_alarm_idx' => $v['alarm_idx'],
                'alarm_contents' => $alarm_contents,
                'min' => $v['min'],
                'max' => $v['max'],

                'created_at' => date("Y-m-d H:i:s"),
            ]);
        }
    }

    public static function getAlarmcontrolActivation($member_group) {
        $result = "";
        $results_obj = EntityAlarmControl::getAlarmcontrolActivation($member_group)->fetchObject();

        if (empty($results_obj)) {
            $result = "N";
        } else {
            $result = $results_obj->activation;
        }

        return $result;
    }
}