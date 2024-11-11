<?php

namespace App\Controller\Manager;

use App\Model\Entity\Device as EntityDevice;
use App\Model\Entity\Member as EntityMmeber;
use App\Model\Entity\RawData as EntityRawData;
use App\Model\Entity\Alarm as EntityAlarm;
use App\Model\Entity\AlarmControl as EntityAlarmControl;
use App\Model\Entity\WaterAlarm;
use app\Utils\Common;
use \App\Utils\View;
use WilliamCosta\DatabaseManager\Database;

class Etc extends Page {

    public static function getEtc($request) {
        $content = View::render('manager/modules/etc/index', []);

        return parent::getPanel('Home > DASHBOARD', $content, 'etc');
    }

    public static function Password_Change($request) {
        $content = View::render('manager/modules/etc/password_change', []);

        return parent::getPanel('Home > DASHBOARD', $content, 'etc');
    }

    public static function Password_Change_Post($request) {
        $postVars = $request->getPostVars();

        $_user = Common::get_manager();
        $_userInfo = EntityMmeber::getMemberById($_user);

        $password = password_hash($postVars['password'], PASSWORD_DEFAULT);

        EntityMmeber::PasswordChange($_userInfo->member_id, $password);

        $request->getRouter()->redirect('/manager');


    }

    /* 제주농협 공동처리장 모니터링 시스템 로딩페이지 처리 */
    public static function jejunonghyeob($request) {

        $_user = Common::get_manager();
        $_userInfo = EntityMmeber::getMemberById($_user);

        $obj = Common::getMembersWidget($_userInfo->idx);

        $_val = [0,0,0,0,0,0,0,0];
        foreach ($obj as $k => $v) {

            if ($v['device_idx'] == "208" || $v['device_idx'] == "169" || $v['device_idx'] == "175" || $v['device_idx'] == "196") {

                foreach ($v['board_name'] as $kk => $vv) {
                    if ($vv['display'] == 'Y') {

                        $device_obj = EntityDevice::getDevicesByIdx($v['device_idx']);
                        $result = EntityRawData::LastLimitOne($device_obj->address, $device_obj->board_type, $device_obj->board_number);
                        $rew_obj = $result->fetchObject(EntityRawData::class);

                        if ($vv['symbol'] == 'L') {
                            if ($rew_obj->board_type == 3) {
                                $water_row = EntityRawData::LastLimitWaterDataSum($rew_obj->address, $rew_obj->board_type, $rew_obj->board_number, $vv['field'], $vv['field'], 1)->fetchObject(EntityRawData::class);
                                $value = ($water_row->{$vv['field']});
                            } else if ($rew_obj->board_type == 6 || $rew_obj->board_type == 35 || $rew_obj->board_type == 40) {
                                $water_row = EntityRawData::LastLimitWaterDataSumExcept_1($rew_obj->address, $rew_obj->board_type, $rew_obj->board_number, $vv['field'], $vv['field'], 1)->fetchObject(EntityRawData::class);
                                $value = ($water_row->{$vv['field']});
                            } else if ($rew_obj->board_type == 4 ) {
                                $water_row = EntityRawData::LastLimitWaterDataSumExcept_2($rew_obj->address, $rew_obj->board_type, $rew_obj->board_number, $vv['field'], $vv['field'], 1)->fetchObject(EntityRawData::class);
                                $value = ($water_row->{$vv['field']});
                            } else {
                                $water_row = EntityRawData::LastLimitWaterDataSum($rew_obj->address, $rew_obj->board_type, $rew_obj->board_number, $vv['field'], $vv['field'], 1)->fetchObject(EntityRawData::class);
                                $value = ($water_row->{$vv['field']});
                            }
                        } else {
                            if (isset($rew_obj->{$vv['field']})) {
                                $value = round($rew_obj->{$vv['field']}, 1);
                            } else {
                                $value = 0;
                            }
                        }
                        $_val[] = $value;

                    }

                }
            }
        }

        $content = View::render('manager/modules/etc/jejunonghyeob', [
            'v1' => $_val[0],
            'v2' => $_val[1],
            'v3' => $_val[2],
            'v4' => $_val[3],
            'v5' => $_val[4] == 0 ? '꺼짐' : '켜짐' ,
            'v6' => $_val[5] == 0 ? '꺼짐' : '켜짐' ,
            'v7' => $_val[6] == 0 ? '꺼짐' : '켜짐' ,
            'v8' => $_val[7] == 0 ? '꺼짐' : '켜짐' ,
            'update_at' => date("Y-m-d H:i:s"),
        ]);

        return parent::getPanel('Home > DASHBOARD', $content, 'etc');


    }

    /* 제주농협 공동처리장 모니터링 시스템 로딩페이지  ajax 처리 */
    public static function ajax_jejunonghyeob($request) {
        $_user = Common::get_manager();
        $_userInfo = EntityMmeber::getMemberById($_user);

        $obj = Common::getMembersWidget($_userInfo->idx);

        $_data = array();
        foreach ($obj as $k => $v) {

            if ($v['device_idx'] == "208" || $v['device_idx'] == "169" || $v['device_idx'] == "175" || $v['device_idx'] == "196") {

                foreach ($v['board_name'] as $kk => $vv) {
                    if ($vv['display'] == 'Y') {

                        $device_obj = EntityDevice::getDevicesByIdx($v['device_idx']);
                        $result = EntityRawData::LastLimitOne($device_obj->address, $device_obj->board_type, $device_obj->board_number);
                        $rew_obj = $result->fetchObject(EntityRawData::class);

                        if ($vv['symbol'] == 'L') {
                            if ($rew_obj->board_type == 3) {
                                $water_row = EntityRawData::LastLimitWaterDataSum($rew_obj->address, $rew_obj->board_type, $rew_obj->board_number, $vv['field'], $vv['field'], 1)->fetchObject(EntityRawData::class);
                                $value = ($water_row->{$vv['field']});
                            } else if ($rew_obj->board_type == 6 || $rew_obj->board_type == 35 || $rew_obj->board_type == 40 ) {
                                $water_row = EntityRawData::LastLimitWaterDataSumExcept_1($rew_obj->address, $rew_obj->board_type, $rew_obj->board_number, $vv['field'], $vv['field'], 1)->fetchObject(EntityRawData::class);
                                $value = ($water_row->{$vv['field']});
                            } else if ($rew_obj->board_type == 4 ) {
                                $water_row = EntityRawData::LastLimitWaterDataSumExcept_2($rew_obj->address, $rew_obj->board_type, $rew_obj->board_number, $vv['field'], $vv['field'], 1)->fetchObject(EntityRawData::class);
                                $value = ($water_row->{$vv['field']});
                            } else {
                                $water_row = EntityRawData::LastLimitWaterDataSum($rew_obj->address, $rew_obj->board_type, $rew_obj->board_number, $vv['field'], $vv['field'], 1)->fetchObject(EntityRawData::class);
                                $value = ($water_row->{$vv['field']});
                            }
                        } else {
                            if (isset($rew_obj->{$vv['field']})) {
                                $value = round($rew_obj->{$vv['field']}, 1);
                            } else {
                                $value = 0;
                            }
                        }
                        $_data[] = $value;
                    }
                }
            }
        }

        return [
            'success' => true,
            'data' => $_data,
            'update_at' => date("Y-m-d H:i:s"),
        ];
    }

    public static function alarmcontrol() {
        $_user = Common::get_manager();
        $_userInfo = EntityMmeber::getMemberById($_user);

        $obj = Common::getMembersWidget($_userInfo->idx);

        $array = array();

        $_i = 0;

        if (is_array((array)$obj)) {
            foreach ($obj as $k => $v) {
                $array[$_i]['path'] = $v['widget_name'];
                $array[$_i]['target'] = "데이터 미수신 상태";
                $array[$_i]['status'] = $v['check_yn'];
                $_i++;
            }
        }

        $alarm_results = (new Database('alarm'))->execute("
            select * from alarm where member_idx=".$_userInfo->idx." order by group_idx=0 asc, group_idx asc;
        ");

        while ($alarm_obj = $alarm_results->fetchObject(EntityAlarm::class)) {
            $device_obj = EntityDevice::getDevicesByIdx($alarm_obj->device_idx);

            $array[$_i]['path'] = $device_obj->device_name." ".$alarm_obj->board_type_name;
            $array[$_i]['target'] = $alarm_obj->group_idx != 0 ? "그룹환경알람" : "환경알람";
            $array[$_i]['status'] = $alarm_obj->activation;
            $_i++;
        };

        $water_alarm_results = (new Database('water_alarm'))->execute("
            select * from water_alarm where member_idx=".$_userInfo->idx.";
        ");

        while ($water_alarm_obj = $water_alarm_results->fetchObject(WaterAlarm::class)) {
            $device_obj = EntityDevice::getDevicesByIdx($water_alarm_obj->device_idx);

            $array[$_i]['path'] = $device_obj->device_name." ".$water_alarm_obj->board_type_name;
            $array[$_i]['target'] = $water_alarm_obj->group_idx != 0 ? "그룹환경알람" : "환경알람";
            $array[$_i]['status'] = $water_alarm_obj->activation;
            $_i++;
        }

        $results_activation = Common::getAlarmcontrolActivation($_userInfo->member_group);
        $checked = "";
        if ($results_activation == 'Y') {
            $checked = "checked";
        }

        $item = "";
        $checkbox = "<div class='btn_switch'><label for=''><input type='checkbox' name='activation' data-idx='' value='Y' ".$checked."></label>";
        foreach ($array as $k => $v) {
            $item .= View::render('manager/modules/etc/alarmcontrol_row',[
                'path' => $array[$k]['path'],
                'target' => $array[$k]['target'],
                'status' => ($array[$k]['status'] == 'Y') ? "<span style='color:#0b956c'>알람켜짐</span>" : "<span style='color:red'>알람꺼짐</span>" ,
                'td' => ($k == 0) ? '<td rowspan='.$_i.' style="vertical-align:top;  padding-top:50px">'.$checkbox.'</td>' : '',
            ]);
        }

        $content = View::render('manager/modules/etc/alarmcontrol', [
                'item' => $item,
        ]);

        return parent::getPanel('Home > DASHBOARD', $content, 'etc');
    }

    public static function setAlarmcontrolChange($request) {
        $postVars = $request->getPostVars();
        $active = $postVars['active'];

        $_user = Common::get_manager();
        $_userInfo = EntityMmeber::getMemberById($_user);

        $member_group = $_userInfo->member_group;
        $member_id = $_userInfo->member_id;

        $obj = new EntityAlarmControl;
        $obj->member_group = $member_group;
        $obj->member_id = $member_id;
        $obj->activation = $active;
        $obj->created();

        return [
            'success' => true,
        ];
    }

    public static function Graphic_view($request) {
        $_user = Common::get_manager();
        $_userInfo = EntityMmeber::getMemberById($_user);

        $obj = Common::getMembersWidget($_userInfo->idx);
        $_str = [];
        $_val = [];
        foreach ($obj as $k => $v) {

            if ($v['device_idx'] == "327" || $v['device_idx'] == "328") {
                foreach ($v['board_name'] as $kk => $vv) {
                    if ($vv['display'] == 'Y') {
                        $device_obj = EntityDevice::getDevicesByIdx($v['device_idx']);
                        $result = EntityRawData::LastLimitOne($device_obj->address, $device_obj->board_type, $device_obj->board_number);
                        $rew_obj = $result->fetchObject(EntityRawData::class);

                        if ($vv['symbol'] == 'L') {
                            if ($rew_obj->board_type == 3) {
                                $water_row = EntityRawData::LastLimitWaterDataSum($rew_obj->address, $rew_obj->board_type, $rew_obj->board_number, $vv['field'], $vv['field'], 1)->fetchObject(EntityRawData::class);
                                $value = ($water_row->{$vv['field']});
                            } else if ($rew_obj->board_type == 6 || $rew_obj->board_type == 35 || $rew_obj->board_type == 40) {
                                $water_row = EntityRawData::LastLimitWaterDataSumExcept_1($rew_obj->address, $rew_obj->board_type, $rew_obj->board_number, $vv['field'], $vv['field'], 1)->fetchObject(EntityRawData::class);
                                $value = ($water_row->{$vv['field']});
                            } else if ($rew_obj->board_type == 4 ) {
                                $water_row = EntityRawData::LastLimitWaterDataSumExcept_2($rew_obj->address, $rew_obj->board_type, $rew_obj->board_number, $vv['field'], $vv['field'], 1)->fetchObject(EntityRawData::class);
                                $value = ($water_row->{$vv['field']});
                            } else {
                                $water_row = EntityRawData::LastLimitWaterDataSum($rew_obj->address, $rew_obj->board_type, $rew_obj->board_number, $vv['field'], $vv['field'], 1)->fetchObject(EntityRawData::class);
                                $value = ($water_row->{$vv['field']});
                            }
                        } else {
                            if (isset($rew_obj->{$vv['field']})) {
                                $value = round($rew_obj->{$vv['field']}, 1);
                            } else {
                                $value = 0;
                            }
                        }
                        $_str[] = $vv['name'];
                        $_val[] = $value;

                    }
                }
            }
        }

        $content = View::render('blank/modules/graphicview', [
            's1' => $_str[0],
            's2' => $_str[1],
            's3' => $_str[2],
            's4' => $_str[3],
            'v1' => $_val[0],
            'v2' => $_val[1],
            'v3' => $_val[2],
            'v4' => $_val[3],
            'update_at' => date("Y-m-d H:i:s"),
        ]);
        return parent::getBlankPanel('Home > DASHBOARD', $content, 'etc');
    }

    public static function ajax_graphicview($request) {
        $_user = Common::get_manager();
        $_userInfo = EntityMmeber::getMemberById($_user);

        $obj = Common::getMembersWidget($_userInfo->idx);
        $_data = [];
        foreach ($obj as $k => $v) {

            if ($v['device_idx'] == "327" || $v['device_idx'] == "328") {
                foreach ($v['board_name'] as $kk => $vv) {
                    if ($vv['display'] == 'Y') {
                        $device_obj = EntityDevice::getDevicesByIdx($v['device_idx']);
                        $result = EntityRawData::LastLimitOne($device_obj->address, $device_obj->board_type, $device_obj->board_number);
                        $rew_obj = $result->fetchObject(EntityRawData::class);

                        if ($vv['symbol'] == 'L') {
                            if ($rew_obj->board_type == 3) {
                                $water_row = EntityRawData::LastLimitWaterDataSum($rew_obj->address, $rew_obj->board_type, $rew_obj->board_number, $vv['field'], $vv['field'], 1)->fetchObject(EntityRawData::class);
                                $value = ($water_row->{$vv['field']});
                            } else if ($rew_obj->board_type == 6 || $rew_obj->board_type == 35 || $rew_obj->board_type == 40) {
                                $water_row = EntityRawData::LastLimitWaterDataSumExcept_1($rew_obj->address, $rew_obj->board_type, $rew_obj->board_number, $vv['field'], $vv['field'], 1)->fetchObject(EntityRawData::class);
                                $value = ($water_row->{$vv['field']});
                            } else if ($rew_obj->board_type == 4 ) {
                                $water_row = EntityRawData::LastLimitWaterDataSumExcept_2($rew_obj->address, $rew_obj->board_type, $rew_obj->board_number, $vv['field'], $vv['field'], 1)->fetchObject(EntityRawData::class);
                                $value = ($water_row->{$vv['field']});
                            } else {
                                $water_row = EntityRawData::LastLimitWaterDataSum($rew_obj->address, $rew_obj->board_type, $rew_obj->board_number, $vv['field'], $vv['field'], 1)->fetchObject(EntityRawData::class);
                                $value = ($water_row->{$vv['field']});
                            }
                        } else {
                            if (isset($rew_obj->{$vv['field']})) {
                                $value = round($rew_obj->{$vv['field']}, 1);
                            } else {
                                $value = 0;
                            }
                        }
                        $_data[] = $value;
                    }
                }
            }
        }

        return [
            'success' => true,
            'data' => $_data,
            'update_at' => date("Y-m-d H:i:s"),
        ];
    }
}