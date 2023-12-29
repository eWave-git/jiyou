<?php

namespace App\Controller\Manager;

use \App\Model\Entity\Device as EntityDevice;
use \App\Model\Entity\Member as EntityMmeber;
use \App\Model\Entity\Alarm as EntityAlarm;
use \App\Model\Entity\AlarmMember as EntityAlarmMember;
use \App\Model\Entity\AlarmHistory as EntityAlarmHistory;
use \app\Utils\Common;
use \App\Utils\View;

class Alarm extends Page {

    public static function setActiveChange($request) {
        $postVars = $request->getPostVars();
        $active = $postVars['active'];

        EntityAlarm::UpdateActiveValue($postVars['idx'], $active);

        return [
            'success' => true,
        ];
    }

    public static function getBoardType($request) {
        $postVars = $request->getPostVars();

        $device_obj = EntityDevice::getDevicesByIdx($postVars['device_idx']);
        $board_array = Common::getbordTypeNameByWidgetNameArray($device_obj->device_idx, $device_obj->board_type);

        $arr = array();
        if ($board_array) {
            $success = true;
            foreach ($board_array as $k => $v) {
                if ($v['display'] == 'Y') {
                    $arr['field'][] = $v['field'];
                    $arr['name'][] = $v['name'];
                }
            }
        } else {
            $success = false;
        }

        return [
            'success' => $success,
            'value'=>$arr['field'],
            'text' => $arr['name'],
        ];

    }

    public static function getAlarmList($user_idx) {

        $result = EntityAlarm::getAlarmByMemberIdx($user_idx);
        $array = array();
        $_i = 0;
        while ($obj = $result->fetchObject(EntityAlarm::class)) {
            $device_obj = EntityDevice::getDevicesByIdx($obj->device_idx);
            $array[$_i]['idx'] = $obj->idx;
            $array[$_i]['device_name'] = $device_obj->device_name ?? '';
            $array[$_i]['board_type_name'] = $obj->board_type_name;
            $array[$_i]['alarm_range'] = $obj->alarm_range;

            $array[$_i]['min'] = $obj->min;
            $array[$_i]['max'] = $obj->max;
            $array[$_i]['activation'] = $obj->activation;
            $array[$_i]['create'] = $obj->created_at;

            $result_2 = EntityAlarmMember::getAlarmMemberByIdx($obj->idx);
            $_temp = "";
            while ($obj_2 = $result_2->fetchObject(EntityAlarmMember::class)) {
                $member = Common::get_member_info($obj_2->member_idx);
                $_temp .= $member['member_name'] . " ";
            }

            $array[$_i]['member'] = $_temp;

            $_i++;
        }

        $item = "";

        $total = count($array);
        foreach ($array as $k => $v) {

            if ($v['alarm_range'] == "between") {
                $MinAtMax = $v['min']." 이상 ~ ".$v['max']." 이하";
            } else if ($v['alarm_range'] == "up") {
                $MinAtMax = $v['max']." 이상";
            } else if ($v['alarm_range'] == "down") {
                $MinAtMax = $v['min']." 이하";
            }

            $item .= View::render('manager/modules/alarm/alarm_list_item', [
                'idx'   => $v['idx'],
                'number' => $total,
                'device_name' => $v['device_name'],
                'field' => $v['board_type_name'],
                'MinAtMax' => $MinAtMax,
                'member' => $v['member'],
                'activation' => $v['activation'],
                'checked'       => $v['activation'] == 'Y'? 'checked' : '' ,
                'created_at' => $v['create'],
            ]);
            $total--;
        }

        return $item;
    }

    public static function getAlarm($request) {

        $_user = Common::get_manager();
        $_userInfo = EntityMmeber::getMemberById($_user);

        $content = View::render('manager/modules/alarm/alarm_list', [
            'alarm_list_item' => self::getAlarmList($_userInfo->idx),
        ]);

        return parent::getPanel('Home > DASHBOARD', $content, 'alarm');
    }

    private static function getMemberDevice($user_idx) {
        $option = "";

        if ($user_idx) {
            foreach (Common::getMembersWidget($user_idx) as $k => $v) {
                $option .= View::render('manager/modules/dashboard/widget_add_form_options', [
                    'value' => $v['device_idx'],
                    'text'  => $v['widget_name'],
                    'selected' => '',
                ]);
            }
        }

        return $option;
    }

    /**
     * TODO : 여러 사용자에게 발송 하기위해서 만들었음. 추후 여러 사용자를 선택할 수 있도록 불러옴.
     *
     * @param $user_idx
     * @param $alarm_idx
     * @return string
     */
    private static function getMemberGroup($user_idx, $alarm_idx = null) {
        $alarm_member_array = array();


        if (!empty($alarm_idx)) {
            $alarm_member = EntityAlarmMember::getAlarmMemberByIdx($alarm_idx);
            while ($obj = $alarm_member->fetchObject(EntityAlarmMember::class)) {
                $alarm_member_array[] = $obj->member_idx;
            }
        }

        $item = "";
        $results = EntityMmeber::getMemberByGroup($user_idx);

        while ($obj = $results->fetchObject(EntityMmeber::class)) {

            $item .= View::render('manager/modules/alarm/targer_user_checkbox', [
                'idx' => $obj->idx,
                'name' => $obj->member_name,
                'checked' => in_array($obj->idx, $alarm_member_array) ? 'checked' : '',
            ]);
        }

        return $item;
    }

    public static function Alarm_Form($request, $idx = null) {
        $objAlarm = is_null($idx) ? '': EntityAlarm::getAlarmByIdx($idx) ;

        $_user = Common::get_manager();
        $_userInfo = EntityMmeber::getMemberById($_user);


        $content = View::render('manager/modules/alarm/alarm_form', [
            'device_options' => self::getMemberDevice($_userInfo->idx),
            'board_options' => '',
            'min'           => '',
            'max'           => '',
            'target_user'   => $_userInfo->idx,
            'checked'       => 'Y',
            'action'        => '/manager/alarm_form_create',
        ]);

        return parent::getPanel('Home > DASHBOARD', $content, 'alarm');
    }

    public static function Alarm_Create($request) {
        $postVars = $request->getPostVars();

        $_user = Common::get_manager();
        $_userInfo = EntityMmeber::getMemberById($_user);

        $device_info = EntityDevice::getDevicesByIdx($postVars['device']);

        if ($postVars['alarm_range'] == "between") {
            $min = $postVars['between_min'];
            $max = $postVars['between_max'];
        } else if ($postVars['alarm_range'] == "up") {
            $min = 0;
            $max = $postVars['up_max'];
        } else if ($postVars['alarm_range'] == "down") {
            $min = $postVars['down_min'];
            $max = 0;
        }

        $obj_1 = new EntityAlarm;
        $obj_1->member_idx = $_userInfo->idx;
        $obj_1->device_idx = $device_info->device_idx;
        $board_type = Common::getBoardTypeNameSelect($device_info->device_idx, $device_info->board_type, $postVars['board']);

        $obj_1->board_type_field = $board_type['field'];
        $obj_1->board_type_name = $board_type['name'];

        $obj_1->alarm_range = $postVars['alarm_range'];
        $obj_1->min = $min;
        $obj_1->max = $max;
        $obj_1->activation = empty($postVars['activation']) ? 'N' : $postVars['activation'];
        $obj_1->created();

        if ($postVars['target_user']) {
            $obj_2 = new EntityAlarmMember;
            $obj_2->alarm_idx = $obj_1->idx;
            $obj_2->member_idx = $postVars['target_user'];
            $obj_2->created();
        }

        $request->getRouter()->redirect('/manager/alarm_list');
    }

    public static function Alarm_Edit($request, $idx) {
        $obj = EntityAlarm::getAlarmByIdx($idx);
        $postVars = $request->getPostVars();

        $device_info = EntityDevice::getDevicesByIdx($postVars['device']);
        $obj->member_idx = $obj->member_idx;
        $obj->device_idx = $device_info->idx ?? $obj->device_idx;

        $board_type = Common::getBoardTypeNameSelect($device_info->board_type, $postVars['board']);
        $obj->board_type_field = $board_type['field'] ?? $obj->board_type;
        $obj->board_type_name = $board_type['name'] ?? $obj->board_number;

        $obj->min = $postVars['min'] ?? $obj->min;
        $obj->max = $postVars['max'] ?? $obj->max;
        $obj->activation = empty($postVars['activation']) ? 'N': $postVars['activation'];
        $obj->updated();

        $request->getRouter()->redirect('/manager/alarm_list');
    }

    public static function getAlarmLogList($user_idx) {
        $alarm_log = EntityAlarmHistory::getAlarmHistoryByMemberIdx($user_idx);

        $item = "";
        $array = array();
        $_i = 0;
        while ($obj = $alarm_log->fetchObject(EntityAlarmHistory::class)) {

            $device_obj = EntityDevice::getDevicesByIdx($obj->device_idx);

            if ($device_obj) {
                $array[$_i]['device_name'] = $device_obj->device_name;
                $array[$_i]['board_type_name'] = $obj->board_type_name;
                $array[$_i]['alarm_contents'] = $obj->alarm_contents;
                $array[$_i]['created_at'] = $obj->created_at;
            }
            $_i++;
        }

        $_i = 1;

        foreach ($array as $k => $v) {
            $item .= View::render('manager/modules/alarm/alarm_log_list_item', [
                'number' => $_i,
                'device_name' => $v['device_name'],
                'board_type_name' => $v['board_type_name'],
                'alarm_contents' => $v['alarm_contents'],
                'created_at' => $v['created_at'],
            ]);

            $_i++;
        }

        return $item;
    }

    public static function AlarmLogList($request) {

        $_user = Common::get_manager();
        $_userInfo = EntityMmeber::getMemberById($_user);

        $content = View::render('manager/modules/alarm/alarm_log_list', [
            'alarm_log_list_item' => self::getAlarmLogList($_userInfo->idx),
        ]);

        return parent::getPanel('Home > DASHBOARD', $content, 'alarm');
    }

    public static function AlarmDelete($request, $idx) {
        $obj = EntityAlarm::getAlarmByIdx($idx);
        $obj->deleted();
        $request->getRouter()->redirect('/manager/alarm_list');
    }
}