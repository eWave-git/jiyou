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
        $active = $postVars['active'] == 'true' ? 'Y' : 'N';

        EntityAlarm::UpdateActiveValue($postVars['idx'], $active);

        return [
            'success' => true,
        ];
    }

    public static function getBoardType($request) {
        $postVars = $request->getPostVars();

        $device_obj = EntityDevice::getDevicesByIdx($postVars['device_idx']);
        $board_array =  Common::getBoardTypeNameArray($device_obj->board_type);


        $arr = array();
        if ($board_array) {
            $success = true;
            foreach ($board_array as $k => $v) {
                $arr['field'][] = $v['field'];
                $arr['name'][] = $v['name'];
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

        $member_devices = Common::getMembersDevice($user_idx);

        $array = array();
        $_i = 0;
        foreach ($member_devices as $k_1 => $v_1) {
            if ($v_1['idx']) {
                $result_1 = EntityAlarm::getAlarmByDeviceIdx($v_1['idx']);
                while ($obj_1 = $result_1->fetchObject(EntityAlarm::class)) {
                    $device_obj = EntityDevice::getDevicesByIdx($obj_1->device_idx);
                    $array[$_i]['idx'] = $obj_1->idx;
                    $array[$_i]['device_name'] = $device_obj->device_name;
                    $array[$_i]['board_type_name'] = $obj_1->board_type_name;


                    $array[$_i]['min'] = $obj_1->min;
                    $array[$_i]['max'] = $obj_1->max;
                    $array[$_i]['activation'] = $obj_1->activation;
                    $array[$_i]['create'] = $obj_1->created_at;

                    $result_2 = EntityAlarmMember::getAlarmMemberByIdx($obj_1->idx);

                    $_temp = "";
                    while ($obj_2 = $result_2->fetchObject(EntityAlarmMember::class)) {
                        $member = Common::get_member_info($obj_2->member_idx);
                        $_temp .= $member['member_name'] . " ";
                    }

                    $array[$_i]['member'] = $_temp;
                    $_i++;
                }
            }
        }

        $item = "";

        foreach ($array as $k => $v) {
            $item .= View::render('manager/modules/alarm/alarm_list_item', [
                'idx'   => $v['idx'],
                'number' => $k+1,
                'device_name' => $v['device_name'],
                'field' => $v['board_type_name'],
                'MinAtMax' => $v['min']."~".$v['max'],
                'member' => $v['member'],
                'activation' => $v['activation'],
                'checked'       => $v['activation'] == 'Y'? 'checked' : '' ,
                'created_at' => $v['create'],
            ]);
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

    private static function getMemberDevice($member_devices, $device = '') {
        $option = "";

        if ($member_devices[0]['idx']) {
            if (is_array($member_devices[0])) {
                foreach ($member_devices as $k => $v) {
                    $option .= View::render('manager/modules/dashboard/widget_add_form_options', [
                        'value' => $v['idx'],
                        'text'  => $v['device_name'],
                        'selected' => ($v['idx'] == $device) ? 'selected' : '',
                    ]);
                }
            }
        }

        return $option;
    }

    private static function getMemberBoardType($obj, $board) {

        $results = Management::getBoardTypeName($obj->board_type);
        $option = "";
        foreach ($results as $k => $v) {
            $option .= View::render('manager/modules/dashboard/widget_add_form_options', [
                'value' => $v['field'],
                'text'  => $v['name'],
                'selected' => ($v['field'] == $board) ? 'selected' : '',
            ]);
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

        $member_devices = Common::getMembersDevice($_userInfo->idx);

        $device = $objAlarm->device_idx ?? '';
        $board = $objAlarm->board_type_field ?? '';

        $_idx = !$device ? $member_devices[0]['idx'] : $device;
        $obj = EntityDevice::getDevicesByIdx($_idx);

        $board_type = $obj->board_type;
        $board_type_info = Management::getBoardTypeName($board_type);

        if ($board) {
            foreach($board_type_info as $k => $v) {
                if ($board == $v['field']) {
                    $board_type_field = $board_type_info[$k]['field'];
                    $board_type_name = $board_type_info[$k]['name'];
                }
            }
        } else {
            $board_type_field = $board_type_info[0]['field'];
            $board_type_name = $board_type_info[0]['name'];

        }
        $idx = $objAlarm->idx ?? '';
        $activation = $objAlarm->activation ?? '';


        $content = View::render('manager/modules/alarm/alarm_form', [
            'device_options' => self::getMemberDevice($member_devices, $device),
            'board_options' => self::getMemberBoardType($obj, $board),
            'min'           => $objAlarm->min ?? '',
            'max'           => $objAlarm->max ?? '',
            'target_user'   => $_userInfo->idx,
            'checked'       => $activation == 'Y'? 'checked' : '' ,
            'action'        => $idx == '' ? '/manager/alarm_form_create' : '/manager/alarm_form/'.$idx.'/edit',
        ]);

        return parent::getPanel('Home > DASHBOARD', $content, 'alarm');
    }

    public static function Alarm_Create($request) {
        $postVars = $request->getPostVars();

        $_user = Common::get_manager();
        $_userInfo = EntityMmeber::getMemberById($_user);

        $device_info = EntityDevice::getDevicesByIdx($postVars['device']);

        $obj_1 = new EntityAlarm;
        $obj_1->member_idx = $_userInfo->idx;
        $obj_1->device_idx = $device_info->idx;

        $board_type = Common::getBoardTypeNameSelect($device_info->board_type, $postVars['board']);
        $obj_1->board_type_field = $board_type['field'];
        $obj_1->board_type_name = $board_type['name'];

        $obj_1->alarm_range = $postVars['alarm_range'];
        $obj_1->min = $postVars['min'];
        $obj_1->max = $postVars['max'];
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
        $k = 0;
        while ($obj = $alarm_log->fetchObject(EntityAlarmHistory::class)) {

            $device_obj = EntityDevice::getDevicesByIdx($obj->device_idx);

            $item .= View::render('manager/modules/alarm/alarm_log_list_item', [
                'number' => $k+1,
                'device_name' => $device_obj->device_name,
                'board_type_name' => $obj->board_type_name,
                'alarm_contents' => $obj->alarm_contents,
                'created_at' => $obj->created_at,
            ]);

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

}