<?php

namespace App\Controller\Manager;

use \App\Model\Entity\Device as EntityDevice;
use \App\Model\Entity\Member as EntityMmeber;
use \App\Model\Entity\Settin as EntitySettin;
use \App\Model\Entity\SettinMember as EntitySettinMember;
use \app\Utils\Common;
use \App\Utils\View;

class Settin extends Page {


    public static function getSettinList($user_idx) {

        $member_devices = Member::getMembersDevice($user_idx);

        $array = array();
        foreach ($member_devices as $k_1 => $v_1) {
            $result_1 = EntitySettin::getSettinByDeviceIdx($v_1['idx']);

            while ($obj_1 = $result_1->fetchObject(EntitySettin::class)) {
                $array[$k_1]['idx'] = $obj_1->idx;
                $array[$k_1]['address'] = $obj_1->address;
                $array[$k_1]['board_type'] = $obj_1->board_type;
                $array[$k_1]['board_type_name'] = $obj_1->board_type_name;
                $array[$k_1]['min'] = $obj_1->min;
                $array[$k_1]['max'] = $obj_1->max;
                $array[$k_1]['activation'] = $obj_1->activation;
                $array[$k_1]['create'] = $obj_1->created_at;

                $result_2 = EntitySettinMember::getSettinMemberByIdx($obj_1->idx);

                $_temp = "";
                while ($obj_2 = $result_2->fetchObject(EntitySettinMember::class)) {
                    $member = Common::get_member_info($obj_2->member_idx);
                    $_temp .= $member['member_name']." ";
                }

                $array[$k_1]['member'] = $_temp;

            }
        }

        $item = "";

        foreach ($array as $k => $v) {
            $item .= View::render('manager/modules/settin/settin_list_item', [
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


    public static function getSettin($request) {

        $_user = Common::get_manager();
        $_userInfo = EntityMmeber::getMemberById($_user);

        $content = View::render('manager/modules/settin/index', [
            'settin_list_item' => self::getSettinList($_userInfo->idx),
        ]);

        return parent::getPanel('Home > DASHBOARD', $content, 'settin');
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

    private static function getMemberGroup($user_idx, $settin_idx = null) {
        $settin_member_array = array();


        if (!empty($settin_idx)) {
            $settin_member = EntitySettinMember::getSettinMemberByIdx($settin_idx);
            while ($obj = $settin_member->fetchObject(EntitySettinMember::class)) {
                $settin_member_array[] = $obj->member_idx;
            }
        }

        $item = "";
        $results = EntityMmeber::getMemberByGroup($user_idx);

        while ($obj = $results->fetchObject(EntityMmeber::class)) {

            $item .= View::render('manager/modules/settin/targer_user_checkbox', [
                'idx' => $obj->idx,
                'name' => $obj->member_name,
                'checked' => in_array($obj->idx, $settin_member_array) ? 'checked' : '',
            ]);
        }

        return $item;
    }

    public static function Settin_Form($request, $idx = null) {
        $objSettin = is_null($idx) ? '': EntitySettin::getSettinByIdx($idx) ;

        $_user = Common::get_manager();
        $_userInfo = EntityMmeber::getMemberById($_user);

        $member_devices = Member::getMembersDevice($_userInfo->idx);

        $device = $objSettin->device_idx ?? '';
        $board = $objSettin->board_type_field ?? '';

        $_idx = !$device ? $member_devices[0]['idx'] : $device;
        $obj = EntityDevice::getDevicesByIdx($_idx);

        $address = $obj->address;
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
        $idx = $objSettin->idx ?? '';
        $activation = $objSettin->activation ?? '';


        $content = View::render('manager/modules/settin/settin_form', [
            'device_options' => self::getMemberDevice($member_devices, $device),
            'board_options' => self::getMemberBoardType($obj, $board),
            'min'           => $objSettin->min ?? '',
            'max'           => $objSettin->max ?? '',
            'target_user'   => self::getMemberGroup($_userInfo->idx, $idx),
            'checked'       => $activation == 'Y'? 'checked' : '' ,
            'action'        => $idx == '' ? '/manager/settin_form_create' : '/manager/settin_form/'.$idx.'/edit',
        ]);

        return parent::getPanel('Home > DASHBOARD', $content, 'settin');
    }

    public static function Settin_Create($request) {
        $postVars = $request->getPostVars();

        $device_info = EntityDevice::getDevicesByIdx($postVars['device']);
        $board_type = Common::getBoardTypeNameSelect($device_info->board_type, $postVars['board']);

        $obj_1 = new EntitySettin;
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
                $obj_2 = new EntitySettinMember;
                $obj_2->settin_idx = $obj_1->idx;
                $obj_2->member_idx = $v;
                $obj_2->created();
            }
        }

        $request->getRouter()->redirect('/manager/settin');
    }

    public static function Settin_Edit($request, $idx) {
        $obj = EntitySettin::getSettinByIdx($idx);
        $postVars = $request->getPostVars();

        EntitySettinMember::deleted($idx);

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
                $obj_2 = new EntitySettinMember;
                $obj_2->settin_idx = $idx;
                $obj_2->member_idx = $v;
                $obj_2->created();
            }
        }

        $request->getRouter()->redirect('/manager/settin');
    }


}