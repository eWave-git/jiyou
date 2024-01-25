<?php

namespace App\Controller\Manager;

use App\Model\Entity\ControlData as EntityControlData;
use App\Model\Entity\Device as EntityDevice;
use App\Model\Entity\Member as EntityMmeber;
use App\Model\Entity\RawData as EntityRawData;
use App\Model\Entity\Widget as EntityWidget;
use app\Utils\Common;
use \App\Utils\View;

class Control extends Page {

    private static function getMemberDevice($member_devices,  $control_type) {
        $option = "";

        foreach ($member_devices as $k => $v) {
            if ($v['control_type'] == $control_type) {
                $option .= View::render('manager/modules/dashboard/widget_add_form_options', [
                    'value' => $v['idx'],
                    'text'  => $v['device_name'],
                    'selected' => '',
                ]);
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

        $k = 0;
        $array = array();

        while ($obj = $member_constrols->fetchObject(EntityControlData::class)) {
            if ($obj->control_type == 'switch') {
                $device_obj = EntityDevice::getDevicesByIdx($obj->device_idx);

                if ($device_obj) {
                    $array[$k]['idx'] = $obj->idx;

                    $array[$k]['name'] = $obj->name;
                    $array[$k]['device_name'] = $device_obj->device_name;
                    $array[$k]['text'] = $obj->{$obj->type} == 1 ? "ON" : "OFF";
                    $array[$k]['checked'] = $obj->{$obj->type} == 1 ? "checked" : "";
                    $array[$k]['field'] = $obj->type;

                    $array[$k]['update_at'] = $obj->update_at;
                    $array[$k]['create_at'] = $obj->create_at;

                    $k++;
                }
            }
        }

        $item = '';
        $total = count($array);
        foreach ($array as $k => $v) {
                $item .= View::render('manager/modules/control/switch_list_item', [
                    'idx' => $v['idx'],
                    'number' => $total,
                    'name' => $v['name'],
                    'device_name' => $v['device_name'],
                    'text'  => $v['text'],
                    'checked' => $v['checked'],
                    'field' => $v['field'],
                    'update_at' => $v['update_at'],
                    'create_at' => $v['create_at'],
                ]);
                $total--;
        }


        return $item;
    }

    public static function getSwitchForm($request) {

        $_user = Common::get_manager();
        $_userInfo = EntityMmeber::getMemberById($_user);

        $member_devices = Common::getMembersControlDevice($_userInfo->idx);

        $content = View::render('manager/modules/control/switch_form', [
            'device_options' => self::getMemberDevice($member_devices,  'R'),
            'action'        => '/manager/switch_create',
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
        $obj->device_idx = $device_info->device_idx;

        $obj->address = $device_info->address;
        $obj->board_type = $device_info->board_type;
        $obj->board_number = $device_info->board_number;

        $obj->name = $postVars['name'];
        $obj->control_type = $postVars['control_type'];
        $obj->type = $relay;
        $obj->relay1 = $relay1;
        $obj->relay2 = $relay2;
        $obj->ch1 = 0;
        $obj->ch2 = 0;
        $obj->ch3 = 0;
        $obj->ch4 = 0;
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

        $array = array();
        $k = 0;

        while ($obj = $member_constrols->fetchObject(EntityControlData::class)) {
            if ($obj->control_type == 'command') {
                $device_obj = EntityDevice::getDevicesByIdx($obj->device_idx);

                if ($device_obj) {
                    $result = EntityRawData::LastLimitOne($device_obj->address, $device_obj->board_type, $device_obj->board_number);
                    $obj_temperature = $result->fetchObject(EntityRawData::class);

                    $array[$k]['idx'] = $obj->idx;
                    $array[$k]['name'] = $obj->name;
                    $array[$k]['topic'] = $device_obj->address . "/" . $device_obj->board_type . "/" . $device_obj->board_number;
                    $array[$k]['data1'] = $obj_temperature->data1;
                    $array[$k]['data2'] = $obj_temperature->data2;
                    $array[$k]['field'] = $obj->type;
                    $array[$k]['update_at'] = $obj->update_at;
                    $array[$k]['create_at'] = $obj->create_at;

                    $k++;
                }
            }
        }
        $item = '';

        $total = count($array);

        foreach ($array as $k => $v) {
            $item .= View::render('manager/modules/control/command_list_item', [
                'idx' => $v['idx'],
                'number' => $total,
                'name' => $v['name'],
                'topic' => $v['topic'],
                'data1'  => $v['data1'],
                'data2' => $v['data2'],
                'field' => $v['field'],
                'update_at' => $v['update_at'],
                'create_at' => $v['create_at'],
            ]);
            $total--;
        }

        return $item;
    }

    public static function getCommandForm($request) {
        $_user = Common::get_manager();
        $_userInfo = EntityMmeber::getMemberById($_user);

        $member_devices = Common::getMembersControlDevice($_userInfo->idx);

        $content = View::render('manager/modules/control/command_form', [
            'device_options' => self::getMemberDevice($member_devices,  'T'),
            'action'        => '/manager/command_create',
        ]);

        return parent::getPanel('Home > DASHBOARD', $content, 'control');
    }

    public static function getCommandCreate($request) {
        $postVars = $request->getPostVars();

        $_user = Common::get_manager();
        $_userInfo = EntityMmeber::getMemberById($_user);

        $device_info = EntityWidget::getWidgetByDeviceIdx($postVars['device'])->fetchObject(EntityWidget::class);

        $obj = new EntityControlData;
        $obj->member_idx = $_userInfo->idx;
        $obj->device_idx = $device_info->device_idx;

        $obj->address = $device_info->address;
        $obj->board_type = $device_info->board_type;
        $obj->board_number = $device_info->board_number;

        $obj->name = $postVars['name'];
        $obj->control_type = $postVars['control_type'];
        $obj->type = '';
        $obj->relay1 = '0';
        $obj->relay2 = '0';
        $obj->temperature = '0';
        $obj->ch1 = 0;
        $obj->ch2 = 0;
        $obj->ch3 = 0;
        $obj->ch4 = 0;
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


    public static function ControlDelete($request, $idx, $mode) {

        $obj = EntityControlData::getControlDataByIdx($idx);
        $obj->deleted();

        if ($mode == "switch") {
            $request->getRouter()->redirect('/manager/control/switch');
        } else if ($mode == "command") {
            $request->getRouter()->redirect('/manager/control/command');
        } else if ($mode == "command_4ch") {
            $request->getRouter()->redirect('/manager/control/command_4ch');
        }

    }

    public static function getCommand4Ch($request) {
        $_user = Common::get_manager();
        $_userInfo = EntityMmeber::getMemberById($_user);

        $content = View::render('manager/modules/control/command_4ch', [
            '4ch_list_item' => self::getCommand4ChList($_userInfo->idx),
        ]);

        return parent::getPanel('Home > DASHBOARD', $content, 'control');
    }

    public static function getCommand4ChList($user_idx) {
        $member_constrols = EntityControlData::getControlDataByMemberIdx($user_idx);

        $array = array();
        $k = 0;

        while ($obj = $member_constrols->fetchObject(EntityControlData::class)) {
            if ($obj->control_type == '4ch') {
                $device_obj = EntityDevice::getDevicesByIdx($obj->device_idx);

                if ($device_obj) {
                    $result = EntityRawData::LastLimitOne($device_obj->address, $device_obj->board_type, $device_obj->board_number);
                    $obj_temperature = $result->fetchObject(EntityRawData::class);

                    $array[$k]['idx'] = $obj->idx;
                    $array[$k]['name'] = $obj->name;
                    $array[$k]['topic'] = $device_obj->address . "/" . $device_obj->board_type . "/" . $device_obj->board_number;
                    $array[$k]['ch1_checked'] = $obj->ch1 == 1 ? "checked" : "";
                    $array[$k]['ch2_checked'] = $obj->ch2 == 1 ? "checked" : "";
                    $array[$k]['ch3_checked'] = $obj->ch3 == 1 ? "checked" : "";
                    $array[$k]['ch4_checked'] = $obj->ch4 == 1 ? "checked" : "";


                    $array[$k]['update_at'] = $obj->update_at;
                    $array[$k]['create_at'] = $obj->create_at;

                    $k++;
                }
            }
        }
        $item = '';

        $total = count($array);

        foreach ($array as $k => $v) {
            $item .= View::render('manager/modules/control/command_4ch_list_item', [
                'idx' => $v['idx'],
                'number' => $total,
                'name' => $v['name'],
                'topic' => $v['topic'],
                'ch1_checked' => $v['ch1_checked'],
                'ch2_checked' => $v['ch2_checked'],
                'ch3_checked' => $v['ch3_checked'],
                'ch4_checked' => $v['ch4_checked'],

                'update_at' => $v['update_at'],
                'create_at' => $v['create_at'],
            ]);
            $total--;
        }

        return $item;
    }
    public static function getCommand4ChForm($request) {
        $_user = Common::get_manager();
        $_userInfo = EntityMmeber::getMemberById($_user);

        $member_devices = Common::getMembersControlDevice($_userInfo->idx);

        $content = View::render('manager/modules/control/command_4ch_form', [
            'device_options' => self::getMemberDevice($member_devices,  '4CH'),
            'action'        => '/manager/command_4ch_create',
        ]);

        return parent::getPanel('Home > DASHBOARD', $content, 'control');
    }

    public static function getCommand4ChCreate($request) {
        $postVars = $request->getPostVars();
        $postVars['ch1'] = isset($postVars['ch1']) ? $postVars['ch1'] : 0;
        $postVars['ch2'] = isset($postVars['ch2']) ? $postVars['ch2'] : 0;
        $postVars['ch3'] = isset($postVars['ch3']) ? $postVars['ch3'] : 0;
        $postVars['ch4'] = isset($postVars['ch4']) ? $postVars['ch4'] : 0;

        $_user = Common::get_manager();
        $_userInfo = EntityMmeber::getMemberById($_user);

        $device_info = EntityWidget::getWidgetByDeviceIdx($postVars['device'])->fetchObject(EntityWidget::class);

        $obj = new EntityControlData;
        $obj->member_idx = $_userInfo->idx;
        $obj->device_idx = $device_info->device_idx;

        $obj->address = $device_info->address;
        $obj->board_type = $device_info->board_type;
        $obj->board_number = $device_info->board_number;

        $obj->name = $postVars['name'];
        $obj->control_type = $postVars['control_type'];
        $obj->type = '';
        $obj->relay1 = '0';
        $obj->relay2 = '0';
        $obj->temperature = '0';
        $obj->ch1 = $postVars['ch1'];
        $obj->ch2 = $postVars['ch2'];
        $obj->ch3 = $postVars['ch3'];
        $obj->ch4 = $postVars['ch4'];
        $obj->created();

        Common::ch4_commend($device_info->address, $device_info->board_type, $device_info->board_number, $postVars['ch1'], $postVars['ch2'], $postVars['ch3'], $postVars['ch4']);

        $request->getRouter()->redirect('/manager/control/command_4ch');
    }

    public static function getControl4ChChange($request) {
        $postVars = $request->getPostVars();

        EntityControlData::ch4Update($postVars['control_idx'], $postVars['ch1'], $postVars['ch2'], $postVars['ch3'], $postVars['ch4']);
        $reslut = EntityControlData::getControlDataByIdx($postVars['control_idx']);

        $device_obj = EntityDevice::getDevicesByIdx($reslut->device_idx);
        Common::ch4_commend($device_obj->address, $device_obj->board_type, $device_obj->board_number, $postVars['ch1'], $postVars['ch2'], $postVars['ch3'], $postVars['ch4']);
        return [
            'success' => 'true',
            'obj' => ''
        ];
    }
}