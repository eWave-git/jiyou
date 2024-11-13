<?php

namespace App\Controller\Manager;

use App\Model\Entity\BoardTypeSymbol;
use App\Model\Entity\Device as EntityDevice;
use App\Model\Entity\Member as EntityMmeber;
use App\Model\Entity\RawData as EntityRawData;
use App\Model\Entity\Widget as EntityWidget;
use App\Model\Entity\WidgetBoardType as EntityWidgetBoardType;
use App\Model\Entity\WidgetConnectionTime as EntityWidgetConnectionTime;
use app\Utils\Common;
use App\Utils\View;


class Dashboard extends Page {
    const DEFAULT_CHECK_TIME = 5;

    public static function getCardItem($rew_obj, $board_name) {
        $item = "";

        if (is_array((array)$rew_obj)) {
            $_cnt = 0;

            foreach ($board_name as $k => $v) {

                if ($v['display'] == 'Y') {

                    if (!$v['symbol']) {
                        $v['symbol'] = "&nbsp;&nbsp;";
                    }

                    if ($v['symbol'] == 'L') {
                        if ($rew_obj->board_type == 3) {
                            $water_row = EntityRawData::LastLimitWaterDataSum($rew_obj->address, $rew_obj->board_type, $rew_obj->board_number, $v['field'], $v['field'], 1)->fetchObject(EntityRawData::class);
                            $value = ($water_row->{$v['field']});
                        } else if ($rew_obj->board_type == 6 || $rew_obj->board_type == 35 || $rew_obj->board_type == 60 || $rew_obj->board_type == 40) {
                            $water_row = EntityRawData::LastLimitWaterDataSumExcept_1($rew_obj->address, $rew_obj->board_type, $rew_obj->board_number, $v['field'], $v['field'], 1)->fetchObject(EntityRawData::class);
                            $value = ($water_row->{$v['field']});
                        } else if ($rew_obj->board_type == 4 ) {
                            $water_row = EntityRawData::LastLimitWaterDataSumExcept_2($rew_obj->address, $rew_obj->board_type, $rew_obj->board_number, $v['field'], $v['field'], 1)->fetchObject(EntityRawData::class);
                            $value = ($water_row->{$v['field']});
                        } else {
                            $water_row = EntityRawData::LastLimitWaterDataSum($rew_obj->address, $rew_obj->board_type, $rew_obj->board_number, $v['field'], $v['field'], 1)->fetchObject(EntityRawData::class);
                            $value = ($water_row->{$v['field']});
                        }
                    } else {
                        if (isset($rew_obj->{$v['field']})) {
                            $value = round($rew_obj->{$v['field']}, 1);
                        } else {
                            $value = 0;
                        }
                    }

                    $symbol = $v['symbol'];

                    $item .= View::render('manager/modules/dashboard/widget_card_item', [
                        'name' => $v['name'],
                        'value' => $value,
                        'symbol' => $symbol,
                    ]);

                    $_cnt++;

                }
            }

//            for ($i = $_cnt; $i < 8; $i++) {
//                $item .= View::render('manager/modules/dashboard/widget_card_item', [
//                    'name' => '&nbsp;',
//                    'value' => '&nbsp',
//                ]);
//            }
        }
        return $item;
    }


    public static function getWidgetCard($user_idx, $display) {
        $obj = Common::getMembersWidget($user_idx);

        $card = "";

        if (is_array((array)$obj)) {
            foreach ($obj as $k => $v) {

                $device_obj = EntityDevice::getDevicesByIdx($v['device_idx']);

                $result = EntityRawData::LastLimitOne($device_obj->address, $device_obj->board_type, $device_obj->board_number);
                $rew_obj = $result->fetchObject(EntityRawData::class);

                $obj[$k]['check_time'] = empty($obj[$k]['check_time']) ? self::DEFAULT_CHECK_TIME : $obj[$k]['check_time'];

                $check_result = Common::widgetConnectionCheck($device_obj->address, $device_obj->board_type, $device_obj->board_number, $obj[$k]['check_time']);

                $check_class = "";
                $check_text = "정상 운영 중";
                if ($check_result == false) {
                    $check_class = "warning";
                    $check_text = "경보 발생 중";
                }

                $card .= View::render('manager/modules/dashboard/widget_card', [
                    'subject' => $obj[$k]['widget_name'],
                    'idx' => $obj[$k]['idx'],
                    'item' => self::getCardItem($rew_obj, $v['board_name']),
                    'display' => $display,
                    'check_class' => $check_class,
                    'check_text' => $check_text,
                    'update_at' => isset($rew_obj->created_at) ? substr($rew_obj->created_at, 5, 14) : "00-00 00:00:00" ,
                ]);

            }
        }

        return $card;
    }

    public static function getDashboard($request) {
        $_user = Common::get_manager();
        $_userInfo = EntityMmeber::getMemberById($_user);

        if (empty($_userInfo)) {
            Common::error_loc_msg('/manager/logout','로그인 정보가 없습니다.');
        }

        if ($_userInfo->member_type == "viewer") {
            $_member = Common::get_member_info($_userInfo->member_group);
            $_userInfo->idx = $_member['idx'];
            $_userInfo->name = $_member['member_name'];

            if (empty($_userInfo->idx)) {
                Common::error_loc_msg('/manager/logout','농장 정보가 없습니다. 또는 manager 정보가 없습니다.');
            }
        }

        $_farm_Info = EntityMmeber::getMembersFarm($_userInfo->idx)->fetchObject(EntityMmeber::class);

        if (empty($_farm_Info->idx)) {
            Common::error_loc_msg('/manager/logout','농장 정보가 없습니다.');
        }

        $display = "blcok";
        if ($_SESSION['manager']['user']['type'] == 'viewer') $display = "none";

        $content = View::render('manager/modules/dashboard/index', [
            'farm_name' => $_farm_Info->farm_name ?? '',
            'widget_card' => self::getWidgetCard($_userInfo->idx, $display),
            'display' => $display,
        ]);


        return parent::getPanel('Home > DASHBOARD', $content, 'dashboard');
    }

    public static function getTableInWidgetData($widget_obj, $board_type_array) {
        $array = array();

        $data = "";

        foreach ($board_type_array as $k => $v) {
            if ($v['display'] == 'Y' && $v['symbol'] != 'L') {
                $result_1 = EntityRawData::NowLastLimitDataOne($widget_obj->address, $widget_obj->board_type, $widget_obj->board_number, $v['field'], $v['name']);
                $obj_1 = $result_1->fetchObject(EntityRawData::class);

                $array[$k]['name'] = $v['name'];
                $array[$k]['now'] = $obj_1->{$v['name']} ?? 0;

                $result_2 = EntityRawData::LastTotal($widget_obj->address, $widget_obj->board_type, $widget_obj->board_number, $v['field'], 24);
                $obj_2 = $result_2->fetchObject(EntityRawData::class);

                $array[$k]['min'] = $obj_2->min ? $obj_2->min : 0;
                $array[$k]['max'] = $obj_2->max ? $obj_2->max : 0;
                $array[$k]['avg'] = $obj_2->avg ? $obj_2->avg : 0;
            }
        }

        foreach ($array as $k => $v) {
            $data .=  View::render('manager/modules/dashboard/widget_table_td', [
                'name' => $v['name'],
                'now' => round($v['now'],1),
                'min' => round($v['min'],1),
                'max' => round($v['max'],1),
                'avg' => round($v['avg'],1),
            ]);
        }

        return $data;
    }

    public static function getTableInWidgetDataWater($widget_obj, $board_type_array) {
        $array = array();

        $data = "";

        $array = array();
        $fields = array();

        foreach ($board_type_array as $k => $v) {
            if ($v['display'] == 'Y' && $v['symbol'] == 'L') {
                $result_1 = EntityRawData::WaterDatesDay($widget_obj->address, $widget_obj->board_type, $widget_obj->board_number, $v['field'], $v['field'], 7, 1);
                $kk = 1;

                $array[$k][] = $v['name'];

                while ($obj_1 = $result_1->fetchObject(EntityRawData::class)) {

                    $fields[$kk] = $obj_1->created;
                    $array[$k][] = $obj_1->{$v['field']};
                    $kk++;
                }

            }
        }

        $data = View::render('manager/modules/dashboard/table_in_widget_water_fields', [
            'fields_1' => $fields[1] ?? '',
            'fields_2' => $fields[2] ?? '',
            'fields_3' => $fields[3] ?? '',
            'fields_4' => $fields[4] ?? '',
            'fields_5' => $fields[5] ?? '',
            'fields_6' => $fields[6] ?? '',
            'fields_7' => $fields[7] ?? '',
            'row_datas' => self::getTableInWidgetDataWaterRows($array),
        ]);

        return $data;
    }

    public static function getTableInWidgetDataWaterRows($array) {

        $rows = "";

        foreach ($array as $k => $v) {
            $rows .= View::render('manager/modules/dashboard/table_in_widget_water_rows', [
                'row_1' => $v[0] ?? "",
                'row_2' => $v[1] ?? "",
                'row_3' => $v[2] ?? "",
                'row_4' => $v[3] ?? "",
                'row_5' => $v[4] ?? "",
                'row_6' => $v[5] ?? "",
                'row_7' => $v[6] ?? "",
                'row_8' => $v[7] ?? "",
            ]);
        }

        return $rows;
    }

    public static function getDashboardTable($request, $idx) {
        $_user = Common::get_manager();
        $_userInfo = EntityMmeber::getMemberById($_user);

        $_farm_Info = EntityMmeber::getMembersFarm($_userInfo->idx)->fetchObject(EntityMmeber::class);

        $widget_obj = EntityWidget::getWidgetByIdx($idx)->fetchObject(EntityWidget::class);
        $board_type_array = Common::getbordTypeNameByWidgetNameArray($widget_obj->device_idx, $widget_obj->board_type);

        $data_arr = array_filter($board_type_array, function ($v, $k) {
            return $v['display'] == "Y" && $v['symbol'] != 'L';
        }, ARRAY_FILTER_USE_BOTH );

        $data_water_arr = array_filter($board_type_array, function ($v, $k) {
            return $v['display'] == "Y" && $v['symbol'] == 'L';
        }, ARRAY_FILTER_USE_BOTH );

        $data_display = count($data_arr) > 0 ? 'block' : 'none';
        $data_water_display = count($data_water_arr) > 0 ? 'block' : 'none';

        $content = View::render('manager/modules/dashboard/table_in_widget', [
            'farm_name' => $_farm_Info->farm_name,
            'widget_name' => $widget_obj->widget_name,
            'data'  => self::getTableInWidgetData($widget_obj, $board_type_array),
            'data_water' => self::getTableInWidgetDataWater($widget_obj, $board_type_array),
            'data_display' => $data_display,
            'data_water_display' => $data_water_display,
        ]);

        return parent::getPanel('Home > DASHBOARD', $content, 'dashboard');
    }

    public static function getDashboardChart($request, $idx) {
        $_user = Common::get_manager();
        $_userInfo = EntityMmeber::getMemberById($_user);

        $_farm_Info = EntityMmeber::getMembersFarm($_userInfo->idx)->fetchObject(EntityMmeber::class);

        $widget_obj = EntityWidget::getWidgetByIdx($idx)->fetchObject(EntityWidget::class);
        $content = View::render('manager/modules/dashboard/chart_in_widget', [
            'farm_name' => $_farm_Info->farm_name,
            'widget_name' => $widget_obj->widget_name,
            'idx' => $idx,
        ]);

        return parent::getPanel('Home > DASHBOARD', $content, 'dashboard');
    }

    public static function getChart($request) {
        $postVars = $request->getPostVars();

        $widget_obj = EntityWidget::getWidgetByIdx($postVars['widget_idx'])->fetchObject(EntityWidget::class);

        $board_type_array = Common::getbordTypeNameByWidgetNameArray($widget_obj->device_idx,$widget_obj->board_type);
        $array = array();
        $fields = array();
        foreach($board_type_array as $k => $v) {
            if ($v['display'] == 'Y') {

                if ($v['symbol'] == 'L') {
                    $row = EntityRawData::WaterDates24HourAgo($widget_obj->address, $widget_obj->board_type, $widget_obj->board_number, $v['field'], $v['name']);
                    $kk = 0;
                    while ($row_obj = $row->fetchObject(EntityRawData::class)) {
                        $array[$kk]['dates'] = $row_obj->created;
                        $array[$kk][$v['field']] = (int) $row_obj->{$v['name']};
                        $kk++;
                    }

                    $fields[$k]['field'] = $v['field'];
                    $fields[$k]['name'] = $v['name'];
                    $fields[$k]['series'] = 'series'.$k;
                    $fields[$k]['yAxis'] = 'yAxis'.$k;
                } else {
                    $row = EntityRawData::AvgDatas($widget_obj->address, $widget_obj->board_type, $widget_obj->board_number, $v['field'], $v['name'], 24, 0);
                    $kk = 0;
                    while ($row_obj = $row->fetchObject(EntityRawData::class)) {
                        $array[$kk]['dates'] = $row_obj->created;
                        $array[$kk][$v['field']] = (int) $row_obj->{$v['name']};
                        $kk++;
                    }

                    $fields[$k]['field'] = $v['field'];
                    $fields[$k]['name'] = $v['name'];
                    $fields[$k]['series'] = 'series'.$k;
                    $fields[$k]['yAxis'] = 'yAxis'.$k;
                }

            }
        }

        return [
            'success' => true,
            'obj' => $array,
            'fields' => $fields,
        ];
        return $array;
    }

    public static function getWidgetItems($request) {
        $postVars = $request->getPostVars();
        $postVars['widget_idx'];

        $obj = EntityWidget::getWidgetByIdx($postVars['widget_idx'])->fetchObject(EntityWidget::class);

        $check_yn = empty($obj->check_yn) ? 'N' : $obj->check_yn;
        $check_time = empty($obj->check_time) ? self::DEFAULT_CHECK_TIME : $obj->check_time;

        $board_type = Common::getbordTypeNameByWidgetNameArray($obj->device_idx, $obj->board_type);

        $symbols = Common::getBoardTypeSymbol();

        return [
            'success' => true,
            'board_type' => $board_type,
            'symbols' => $symbols,
            'check_yn' => $check_yn,
            'check_time' => $check_time,
        ];
    }

    public static function widgetNameChange($request) {
        $postVars = $request->getPostVars();

        EntityWidget::UpdateWidgetName($postVars['idx'], $postVars['widget_name']);

        $widget_obj = EntityWidget::getWidgetByIdx($postVars['idx'])->fetchObject(EntityWidget::class);
        $board_type_obj = Common::getBoardTypeNameArray($widget_obj->board_type);
        $widgetBoardType = EntityWidgetBoardType::getWidgetBoardTypeByWidgetIdx($postVars['idx'])->fetchObject(EntityWidgetBoardType::class);

        if (isset($widgetBoardType->idx)) {
            foreach ($board_type_obj as $k => $v) {
                $name = $v['field']."_name";
                $display = $v['field']."_display";
                $symbol = $v['field']."_symbol";

                if (!isset($postVars[$display])) {
                    $postVars[$display] = 'N';
                }
                $postVars[$symbol] = BoardTypeSymbol::getSymbolByIdx($postVars[$symbol])->symbol;

                $widgetBoardType->{$display} = $postVars[$display];
                $widgetBoardType->{$name} = $postVars[$name];
                $widgetBoardType->{$symbol} = $postVars[$symbol];
            }

            $widgetBoardType->updated();

        } else {
            $widget_board_type_obj = new EntityWidgetBoardType();
            $widget_board_type_obj->widget_idx = $postVars['idx'];
            foreach ($board_type_obj as $k=>$v) {
                $name = $v['field']."_name";
                $display = $v['field']."_display";
                $symbol = $v['field']."_symbol";

                if (!isset($postVars[$display])) {
                    $postVars[$display] = 'N';
                }
                $postVars[$symbol] = BoardTypeSymbol::getSymbolByIdx($postVars[$symbol])->symbol;

                $widget_board_type_obj->{$display} = $postVars[$display];
                $widget_board_type_obj->{$name} = $postVars[$name];
                $widget_board_type_obj->{$symbol} = $postVars[$symbol];
            }
            $widget_board_type_obj->created();
        }

        $check_yn = isset($postVars['check_yn']) ? $postVars['check_yn'] : 'N';
        $check_time = isset($postVars['check_time']) ? $postVars['check_time'] : self::DEFAULT_CHECK_TIME;

        $widget_connection_obj = EntityWidgetConnectionTime::getWidgetConnectionByWidgetIdx($postVars['idx'])->fetchObject(EntityWidgetConnectionTime::class);
        if (isset($widget_connection_obj->idx)) {
            $widget_connection_obj->check_yn = $check_yn;
            $widget_connection_obj->check_time = $check_time;
            $widget_connection_obj->updated();
        } else {
            $widget_connection_obj = new EntityWidgetConnectionTime();
            $widget_connection_obj->widget_idx = $postVars['idx'];
            $widget_connection_obj->check_yn = $check_yn;
            $widget_connection_obj->check_time = $check_time;
            $widget_connection_obj->created();
        }

        return [
            'success' => true,
        ];
    }

    public static function setPushId($request) {
        $postVars = $request->getPostVars();

        $_user = Common::get_manager();
        $_userInfo = EntityMmeber::getMemberById($_user);

        EntityMmeber::UpdateSubscriptionId($_userInfo->member_id, $postVars['subscription_id']);

        return [
            'success' => true,
        ];
    }

    public static function getTestChart($request) {
        $postVars = $request->getPostVars();

        $request = EntityRawData::TwoAvgDataTest($postVars['data_1'], $postVars['data_2'], $postVars['interval'], $postVars['minute_interval']);

        $data1_arr =  array(
            'label'=> 'data_1',
            'borderColor' => 'rgb(0, 0, 255)',
            'backgroundColor' => 'rgb(0, 0, 255)',
            'data' => array()
        );

        $data2_arr =  array(
            'label'=> 'data_2',
            'borderColor' => 'rgb(255, 0, 0)',
            'backgroundColor' => 'rgb(255, 0, 0)',
            'data' => array()
        );

        $create_arr = array();

        while ($ob = $request->fetchObject(EntityRawData::class)) {
            array_push($create_arr, substr( $ob->created, 11, 5) );
            array_push($data1_arr['data'], floor($ob->data1));
            array_push($data2_arr['data'], floor($ob->data2));

        }
        $datasets = array($data1_arr, $data2_arr);

        $lables = $create_arr;

        return [
            'success' => true,
            'labels' => $lables,
            'datasets' => $datasets,
        ];
    }

}