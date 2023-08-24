<?php

namespace App\Controller\Manager;

use \App\Model\Entity\Device as EntityDevice;
use \App\Model\Entity\Member as EntityMmeber;
use \App\Model\Entity\Setting as EntitySetting;
use \App\Model\Entity\SettingMember as EntitySettingMember;
use \app\Utils\Common;
use \App\Utils\View;

class Setting extends Page {


    public static function getSettingList($user_idx) {

        $member_devices = Member::getMembersDevice($user_idx);

        $array = array();
        $_i = 0;
        foreach ($member_devices as $k_1 => $v_1) {
            if ($v_1['idx']) {
                $result_1 = EntitySetting::getSettingByDeviceIdx($v_1['idx']);
                while ($obj_1 = $result_1->fetchObject(EntitySetting::class)) {
                    $array[$_i]['idx'] = $obj_1->idx;
                    $array[$_i]['address'] = $obj_1->address;
                    $array[$_i]['board_type'] = $obj_1->board_type;
                    $array[$_i]['board_type_name'] = $obj_1->board_type_name;
                    $array[$_i]['min'] = $obj_1->min;
                    $array[$_i]['max'] = $obj_1->max;
                    $array[$_i]['activation'] = $obj_1->activation;
                    $array[$_i]['create'] = $obj_1->created_at;

                    $result_2 = EntitySettingMember::getSettingMemberByIdx($obj_1->idx);

                    $_temp = "";
                    while ($obj_2 = $result_2->fetchObject(EntitySettingMember::class)) {
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
            $item .= View::render('manager/modules/setting/setting_list_item', [
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


    public static function getSetting($request) {

        $_user = Common::get_manager();
        $_userInfo = EntityMmeber::getMemberById($_user);

        $content = View::render('manager/modules/setting/index', [
            'setting_list_item' => self::getSettingList($_userInfo->idx),
        ]);

        return parent::getPanel('Home > DASHBOARD', $content, 'setting');
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

    private static function getMemberGroup($user_idx, $setting_idx = null) {
        $setting_member_array = array();


        if (!empty($setting_idx)) {
            $setting_member = EntitySettingMember::getSettingMemberByIdx($setting_idx);
            while ($obj = $setting_member->fetchObject(EntitySettingMember::class)) {
                $setting_member_array[] = $obj->member_idx;
            }
        }

        $item = "";
        $results = EntityMmeber::getMemberByGroup($user_idx);

        while ($obj = $results->fetchObject(EntityMmeber::class)) {

            $item .= View::render('manager/modules/setting/targer_user_checkbox', [
                'idx' => $obj->idx,
                'name' => $obj->member_name,
                'checked' => in_array($obj->idx, $setting_member_array) ? 'checked' : '',
            ]);
        }

        return $item;
    }

    public static function Setting_Form($request, $idx = null) {
        $objSetting = is_null($idx) ? '': EntitySetting::getSettingByIdx($idx) ;

        $_user = Common::get_manager();
        $_userInfo = EntityMmeber::getMemberById($_user);

        $member_devices = Member::getMembersDevice($_userInfo->idx);

        $device = $objSetting->device_idx ?? '';
        $board = $objSetting->board_type_field ?? '';

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
        $idx = $objSetting->idx ?? '';
        $activation = $objSetting->activation ?? '';


        $content = View::render('manager/modules/setting/setting_form', [
            'device_options' => self::getMemberDevice($member_devices, $device),
            'board_options' => self::getMemberBoardType($obj, $board),
            'min'           => $objSetting->min ?? '',
            'max'           => $objSetting->max ?? '',
            'target_user'   => self::getMemberGroup($_userInfo->idx, $idx),
            'checked'       => $activation == 'Y'? 'checked' : '' ,
            'action'        => $idx == '' ? '/manager/setting_form_create' : '/manager/setting_form/'.$idx.'/edit',
        ]);

        return parent::getPanel('Home > DASHBOARD', $content, 'setting');
    }

    public static function Setting_Create($request) {
        $postVars = $request->getPostVars();

        $device_info = EntityDevice::getDevicesByIdx($postVars['device']);
        $board_type = Common::getBoardTypeNameSelect($device_info->board_type, $postVars['board']);

        $obj_1 = new EntitySetting;
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
                $obj_2 = new EntitySettingMember;
                $obj_2->setting_idx = $obj_1->idx;
                $obj_2->member_idx = $v;
                $obj_2->created();
            }
        }

        $request->getRouter()->redirect('/manager/setting');
    }

    public static function Setting_Edit($request, $idx) {
        $obj = EntitySetting::getSettingByIdx($idx);
        $postVars = $request->getPostVars();

        EntitySettingMember::deleted($idx);

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
                $obj_2 = new EntitySettingMember;
                $obj_2->setting_idx = $idx;
                $obj_2->member_idx = $v;
                $obj_2->created();
            }
        }

        $request->getRouter()->redirect('/manager/setting');
    }


}