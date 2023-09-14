<?php

namespace App\Controller\Manager;

use \App\Model\Entity\Device as EntityDevice;
use \App\Model\Entity\Member as EntityMmeber;
use \App\Model\Entity\Alarm as EntityAlarm;
use \App\Model\Entity\AlarmMember as EntityAlarmMember;
use \app\Utils\Common;
use \App\Utils\View;

class Alarm extends Page {


    public static function getAlarmList($user_idx) {

        $member_devices = Common::getMembersDevice($user_idx);

        $array = array();
        $_i = 0;
        foreach ($member_devices as $k_1 => $v_1) {
            if ($v_1['idx']) {
                $result_1 = EntityAlarm::getAlarmByDeviceIdx($v_1['idx']);
                while ($obj_1 = $result_1->fetchObject(EntityAlarm::class)) {
                    $array[$_i]['idx'] = $obj_1->idx;
                    $array[$_i]['address'] = $obj_1->address;
                    $array[$_i]['board_type'] = $obj_1->board_type;
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
                'idx' => $v['idx'],
                'device' => $v['address']."-".$v['board_type']."-".$v['board_type'],
                'field' => $v['board_type_name'],
                'MinAtMax' => $v['min']."~".$v['max'],
                'member' => $v['member'],
                'created_at' => $v['create'],
            ]);
        }



        return $item;
    }


    public static function getAlarm($request) {

        $_user = Common::get_manager();
        $_userInfo = EntityMmeber::getMemberById($_user);

        $content = View::render('manager/modules/alarm/index', [
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
                        'text'  => $v['address']."-".$v['board_type']."-".$v['board_number'],
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
            'target_user'   => self::getMemberGroup($_userInfo->idx, $idx),
            'checked'       => $activation == 'Y'? 'checked' : '' ,
            'action'        => $idx == '' ? '/manager/alarm_form_create' : '/manager/alarm_form/'.$idx.'/edit',
        ]);

        return parent::getPanel('Home > DASHBOARD', $content, 'alarm');
    }

    public static function Alarm_Create($request) {
        $postVars = $request->getPostVars();

        $device_info = EntityDevice::getDevicesByIdx($postVars['device']);
        $board_type = Common::getBoardTypeNameSelect($device_info->board_type, $postVars['board']);

        $obj_1 = new EntityAlarm;
        $obj_1->device_idx = $device_info->idx;
        $obj_1->address = $device_info->address;
        $obj_1->board_type = $device_info->board_type;
        $obj_1->board_number = $device_info->board_number;
        $obj_1->board_type_field = $board_type['field'];
        $obj_1->board_type_name = $board_type['name'];
        $obj_1->min = $postVars['min'];
        $obj_1->max = $postVars['max'];
        $obj_1->activation = empty($postVars['activation']) ? 'N' : $postVars['activation'];
        $obj_1->created();

        if (!empty($postVars['target_user'])) {
            foreach ($postVars['target_user'] as $k => $v) {
                $obj_2 = new EntityAlarmMember;
                $obj_2->alarm_idx = $obj_1->idx;
                $obj_2->member_idx = $v;
                $obj_2->created();
            }
        }

        $request->getRouter()->redirect('/manager/alarm');
    }

    public static function Alarm_Edit($request, $idx) {
        $obj = EntityAlarm::getAlarmByIdx($idx);
        $postVars = $request->getPostVars();

        EntityAlarmMember::deleted($idx);

        $device_info = EntityDevice::getDevicesByIdx($postVars['device']);
        $board_type = Common::getBoardTypeNameSelect($device_info->board_type, $postVars['board']);

        $obj->device_idx = $device_info->idx ?? $obj->device_idx;
        $obj->address = $device_info->address ?? $obj->address;
        $obj->board_type = $device_info->board_type ?? $obj->board_type;
        $obj->board_number = $device_info->board_number ?? $obj->board_number;
        $obj->board_type_field = $board_type['field'] ?? $obj->board_type_field;
        $obj->board_type_name = $board_type['name'] ?? $obj->board_type_name;
        $obj->min = $postVars['min'] ?? $obj->min;
        $obj->max = $postVars['max'] ?? $obj->max;
        $obj->activation = empty($postVars['activation']) ? 'N': $postVars['activation'];
        $obj->updated();

        if (!empty($postVars['target_user'])) {
            foreach ($postVars['target_user'] as $k => $v) {
                $obj_2 = new EntityAlarmMember;
                $obj_2->alarm_idx = $idx;
                $obj_2->member_idx = $v;
                $obj_2->created();
            }
        }

        $request->getRouter()->redirect('/manager/alarm');
    }


}