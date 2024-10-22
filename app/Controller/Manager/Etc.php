<?php

namespace App\Controller\Manager;

use App\Model\Entity\Device as EntityDevice;
use App\Model\Entity\Member as EntityMmeber;
use App\Model\Entity\RawData as EntityRawData;
use App\Model\Entity\Alarm as EntityAlarm;
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

    public static function allmessagecontrol() {
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
            select * from alarm where member_idx=".$_userInfo->idx." order by group_idx=0 asc, group_idx desc;
        ");

        while ($alarm_obj = $alarm_results->fetchObject(EntityAlarm::class)) {
            $device_obj = EntityDevice::getDevicesByIdx($alarm_obj->device_idx);

            $array[$_i]['path'] = $device_obj->device_name." ".$alarm_obj->board_type_name;
            $array[$_i]['target'] = $alarm_obj->group_idx != 0 ? "그룹알람" : "개별알람";
            $array[$_i]['status'] = $alarm_obj->activation;
            $_i++;
        };

        // TODO :: 음수알람 추가
//        Common::print_r2($array);

        $content = View::render('manager/modules/etc/allmessagecontrol', [

        ]);

        return parent::getPanel('Home > DASHBOARD', $content, 'etc');
    }
}