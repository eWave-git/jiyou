<?php

namespace App\Controller\Manager;

use App\Model\Entity\Alarm as EntityAlarm;
use App\Model\Entity\BoardTypeRef as EntityBoardTypeRef;
use App\Model\Entity\ControlData as EntityControlData;
use App\Model\Entity\Device as EntityDevice;
use App\Model\Entity\Member as EntityMmeber;
use App\Model\Entity\RawData as EntityRawData;
use app\Utils\Common;
use \App\Utils\View;

class Control extends Page {

    private static function getMemberDevice($member_devices, $device = '', $control_type) {
        $option = "";

        if ($member_devices[0]['idx']) {
            if (is_array($member_devices[0])) {
                foreach ($member_devices as $k => $v) {
                    if ($v['control_type'] == $control_type) {
                        $option .= View::render('manager/modules/dashboard/widget_add_form_options', [
                            'value' => $v['idx'],
                            'text'  => $v['device_name'],
                            'selected' => ($v['idx'] == $device) ? 'selected' : '',
                        ]);
                    }
                }
            }
        }

        return $option;
    }

    public static function getSwitch($request) {

        $_user = Common::get_manager();
        $_userInfo = EntityMmeber::getMemberById($_user);

        $content = View::render('manager/modules/control/switch', [
            'switch_list_item' => self::getSwitchList($_userInfo->idx),
        ]);

        return parent::getPanel('Home > DASHBOARD', $content, 'control');
    }

    public static function getSwitchList($user_idx) {

        $member_constrols = EntityControlData::getControlDataByMemberIdx($user_idx);
        $item = '';
        $k = 0;

        while ($obj = $member_constrols->fetchObject(EntityControlData::class)) {
            if ($obj->control_type == 'switch') {
                $device_obj = EntityDevice::getDevicesByIdx($obj->device_idx);

                $item .= View::render('manager/modules/control/switch_list_item', [
                    'idx' => $obj->idx,
                    'number' => $k+1,
                    'name' => $obj->name,
                    'device_naem' => $device_obj->device_name,
                    'text'  => $obj->{$obj->type} == 1 ? "ON" : "OFF",
                    'checked' => $obj->{$obj->type} == 1 ? "checked" : "",
                    'field' => $obj->type,
                    'update_at' => $obj->update_at,
                    'created_at' => $obj->create_at,
                ]);

                $k++;
            }
        }

        return $item;
    }

    public static function getSwitchForm($request) {

        $_user = Common::get_manager();
        $_userInfo = EntityMmeber::getMemberById($_user);

        $member_devices = Member::getMembersControlDevice($_userInfo->idx);
        $device = $objAlarm->device_idx ?? '';

        $idx = $objAlarm->idx ?? '';

        $content = View::render('manager/modules/control/switch_form', [
            'device_options' => self::getMemberDevice($member_devices, $device, 'R'),
            'action'        => $idx == '' ? '/manager/switch_create' : '/manager/switch_create/'.$idx.'/edit',
        ]);

        return parent::getPanel('Home > DASHBOARD', $content, 'control');
    }

    public static function getSwitchCreate($request) {
        $postVars = $request->getPostVars();

        $_user = Common::get_manager();
        $_userInfo = EntityMmeber::getMemberById($_user);

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

        $obj = new EntityControlData;
        $obj->member_idx = $_userInfo->idx;
        $obj->device_idx = $device_info->idx;
        $obj->name = $postVars['name'];
        $obj->control_type = $postVars['control_type'];
        $obj->type = $relay;
        $obj->relay1 = $relay1;
        $obj->relay2 = $relay2;
        $obj->temperature = $temperature;

        $obj->created();

        $request->getRouter()->redirect('/manager/control/switch');
    }

    public static function getControlRelayChange($request) {
        $postVars = $request->getPostVars();

        EntityControlData::relayUpdate($postVars['control_idx'], $postVars['field'], $postVars['val']);

        return [
            'success' => true,
            'obj' => ''
        ];
    }

    public static function getCommand($request) {
        $_user = Common::get_manager();
        $_userInfo = EntityMmeber::getMemberById($_user);

        $content = View::render('manager/modules/control/command', [
            'command_list_item' => self::getCommandList($_userInfo->idx),
        ]);

        return parent::getPanel('Home > DASHBOARD', $content, 'control');
    }

    public static function getCommandList($user_idx) {
        $member_constrols = EntityControlData::getControlDataByMemberIdx($user_idx);
        $item = '';
        $k = 0;

        while ($obj = $member_constrols->fetchObject(EntityControlData::class)) {
            if ($obj->control_type == 'command') {
                $device_obj = EntityDevice::getDevicesByIdx($obj->device_idx);

                $result = EntityRawData::LastLimitOne($device_obj->address, $device_obj->board_type, $device_obj->board_number);
                $obj_temperature = $result->fetchObject(EntityRawData::class);

                $item .= View::render('manager/modules/control/command_list_item', [
                    'idx' => $obj->idx,
                    'number' => $k+1,
                    'name' => $obj->name,
                    'topic' => $device_obj->address."/".$device_obj->board_type."/".$device_obj->board_number,
                    'data1'  => $obj_temperature->data1,
                    'data2' => $obj_temperature->data2,
                    'field' => $obj->type,
                    'update_at' => $obj->update_at,
                    'created_at' => $obj->create_at,
                ]);

                $k++;
            }
        }

        return $item;
    }

    public static function getCommandForm($request) {
        $_user = Common::get_manager();
        $_userInfo = EntityMmeber::getMemberById($_user);

        $member_devices = Member::getMembersControlDevice($_userInfo->idx);
        $device = $objAlarm->device_idx ?? '';

        $idx = $objAlarm->idx ?? '';

        $content = View::render('manager/modules/control/command_form', [
            'device_options' => self::getMemberDevice($member_devices, $device, 'T'),
            'action'        => $idx == '' ? '/manager/command_create' : '/manager/command_create/'.$idx.'/edit',
        ]);

        return parent::getPanel('Home > DASHBOARD', $content, 'control');
    }

    public static function getCommandCreate($request) {
        $postVars = $request->getPostVars();

        $_user = Common::get_manager();
        $_userInfo = EntityMmeber::getMemberById($_user);

        $device_info = EntityDevice::getDevicesByIdx($postVars['device']);

        $obj = new EntityControlData;
        $obj->member_idx = $_userInfo->idx;
        $obj->device_idx = $device_info->idx;
        $obj->name = $postVars['name'];
        $obj->control_type = $postVars['control_type'];
        $obj->type = '';
        $obj->relay1 = '0';
        $obj->relay2 = '0';
        $obj->temperature = '0';

        $obj->created();

        $request->getRouter()->redirect('/manager/control/command');
    }


    public static function getControlTemperatureChange($request) {
        $postVars = $request->getPostVars();

        $success = false;

        if ($postVars['val'] && $postVars['val'] > 0) {
            EntityControlData::temperatureUpdate($postVars['control_idx'], $postVars['val']);
            $reslut = EntityControlData::getControlDataByIdx($postVars['control_idx']);

            $device_obj = EntityDevice::getDevicesByIdx($reslut->device_idx);
            Common::temperature_commend($device_obj->address, $device_obj->board_type, $device_obj->board_number, $postVars['val']);

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