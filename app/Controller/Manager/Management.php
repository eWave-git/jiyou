<?php

namespace App\Controller\Manager;

use App\Controller\Admin\BoardTypeRef;

use App\Model\Entity\BoardTypeRef as EntityBoardTypeRef;
use App\Model\Entity\ControlData as EntityControlData ;
use App\Model\Entity\Device as EntityDevice;
use \App\Model\Entity\Member as EntityMmeber;
use \App\Model\Entity\RawData as EntityRawData;
use \app\Utils\Common;
use \App\Utils\View;

class Management extends Page {

    // TODO : Common 으로 이동
    /**
     * @param $board_type
     * @return array
     */
    public static function getBoardTypeName($board_type) {
        $array = array();
        $array =  Common::getBoardTypeNameArray($board_type);

        return $array;
    }

    public static function getManagementList($user_idx) {

        $member_devices = Member::getMembersControlDevice($user_idx);

        $array = array();
        $_i = 0;
        foreach ($member_devices as $k_1 => $v_1) {
            if ($v_1) {
                $result_1 = EntityControlData::getControlDataByDeviceIdx($v_1['idx']);

                while ($obj_1 = $result_1->fetchObject(EntityControlData::class)) {
                    $array[$_i]['idx'] = $obj_1->idx;
                    $array[$_i]['address'] = $obj_1->address;
                    $array[$_i]['board_type'] = $obj_1->board_type;
                    $array[$_i]['board_number'] = $obj_1->board_number;
                    $array[$_i]['name'] = $obj_1->name;
                    $array[$_i]['type'] = $obj_1->type;
                    $array[$_i]['relay1'] = $obj_1->relay1;
                    $array[$_i]['relay2'] = $obj_1->relay2;
                    $array[$_i]['temperature'] = $obj_1->temperature;
                    $array[$_i]['control_type'] = $v_1['control_type'];
                    $array[$_i]['created_at'] = $obj_1->create_at;

                    $_i++;
                }
            }
        }

        $item = "";

        foreach ($array as $k => $v) {
            if ($v['control_type'] == "R") {
                $item .= View::render('manager/modules/management/management_list_item_relay', [
                    'idx' => $v['idx'],
                    'name' => $v['name'],
                    'device' => $v['address']."-".$v['board_type']."-".$v['board_number'],
                    'text'  => $array[$k][$array[$k]['type']] == 1 ? "운영중" : "중지",
                    'checked' => $array[$k][$array[$k]['type']] == 1 ? "checked" : "",
                    'field' => $v['type'],
                    'created_at' => $v['created_at'],
                ]);
            } else if ($v['control_type'] == "T") {
                $item .= View::render('manager/modules/management/management_list_item_temperature', [
                    'idx' => $v['idx'],
                    'name' => $v['name'],
                    'device' => $v['address']."-".$v['board_type']."-".$v['board_number'],
                    'temperature' => $v['temperature'],


                    'created_at' => $v['created_at'],
                ]);
            }
        }


        return $item;
    }


    public static function getManagement($request) {

        $_user = Common::get_manager();
        $_userInfo = EntityMmeber::getMemberById($_user);

        $content = View::render('manager/modules/management/index', [
            'management_list_item' => self::getManagementList($_userInfo->idx),
        ]);

        return parent::getPanel('Home > DASHBOARD', $content, 'management');
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

    public static function getDivRelay() {

        $display = "none";


        $div = View::render('manager/modules/management/management_form_relay', [
            'display' => $display
        ]);

        return $div;
    }

    public static function getDivTemperature() {
        $display = "none";

        $div = View::render('manager/modules/management/management_form_temperature', [
            'display' => $display
        ]);

        return $div;
    }

    public static function Management_Form($request) {
        $objAlarm = '';

        $_user = Common::get_manager();
        $_userInfo = EntityMmeber::getMemberById($_user);

        $member_devices = Member::getMembersControlDevice($_userInfo->idx);
        $device = $objAlarm->device_idx ?? '';

        $idx = $objAlarm->idx ?? '';

        $content = View::render('manager/modules/management/management_form', [
            'device_options' => self::getMemberDevice($member_devices, $device),
            'div_relay' => self::getDivRelay(),
            'div_temperature' => self::getDivTemperature(),
            'action'        => $idx == '' ? '/manager/management_form_create' : '/manager/management_form/'.$idx.'/edit',
        ]);


        return parent::getPanel('Home > DASHBOARD', $content, 'management');
    }

    public static function Management_Create($request) {
        $postVars = $request->getPostVars();

        $device_info = EntityDevice::getDevicesByIdx($postVars['device']);

        if ($postVars['relay']) {
            $relay = $postVars['relay'];

            if ($postVars['relay'] == "relay1") {
                $relay1 = 1;
                $relay2 = 0;
            } else if ($postVars['relay'] == "relay2") {
                $relay1 = 0;
                $relay2 = 1;
            }
            $temperature = 0;
        } else {
            $relay = "";
            $relay1 = 0;
            $relay2 = 0;
        }

        if ($postVars['temperature']) {
            $temperature = $postVars['temperature'];
            $relay = "";
            $relay1 = 0;
            $relay2 = 0;
        }
        $obj = new EntityControlData;
        $obj->device_idx = $device_info->idx;
        $obj->address = $device_info->address;
        $obj->board_type = $device_info->board_type;
        $obj->board_number = $device_info->board_number;
        $obj->name = $postVars['name'];
        $obj->type = $relay;
        $obj->relay1 = $relay1;
        $obj->relay2 = $relay2;
        $obj->temperature = $temperature;

        $obj->created();

        $request->getRouter()->redirect('/manager/managment');
    }


    public static function getControl($request) {
        $postVars = $request->getPostVars();

        if ($postVars['device_idx'])  {

            $device_info = EntityDevice::getDevicesByIdx($postVars['device_idx']);
            $obj = (array) EntityBoardTypeRef::getBoardTypeRefByBoardType($device_info->board_type);

            $result = EntityRawData::LastLimitDataOne($device_info->address, $device_info->board_type, $device_info->board_number, 'data1', 'data');
            $obj_temperature = $result->fetchObject(EntityRawData::class);

            $success = false;
            if ($obj_temperature->data && $obj_temperature->data > 0) {
                $success = true;

                Common::temperature_commend($device_info->address, $device_info->board_type, $device_info->board_number, $obj_temperature->data);

            } else {
                $success = false;
            }

            return [
                'success' => $success,
                'obj' => $obj,
                'temperature' => $obj_temperature->data,
            ];
        } else {
            return [
                'success' => true,
                'obj' => ''
            ];
        }
    }

    public static function getControlRelayChange($request) {
        $postVars = $request->getPostVars();

        EntityControlData::relayUpdate($postVars['control_idx'], $postVars['field'], $postVars['val']);

        return [
            'success' => true,
            'obj' => ''
        ];
    }


    public static function getControlTemperatureChange($request) {
        $postVars = $request->getPostVars();

        $success = false;

        if ($postVars['val'] && $postVars['val'] > 0) {
            EntityControlData::temperatureUpdate($postVars['control_idx'], $postVars['val']);
            $reslut = EntityControlData::getControlDataByIdx($postVars['control_idx']);
            Common::temperature_commend($reslut->address, $reslut->board_type, $reslut->board_number, $postVars['val']);

            $success = true;
        } else {
            $success = false;
        }

        return [
            'success' => $success,
            'obj' => ''
        ];
    }


}