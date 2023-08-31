<?php

namespace App\Controller\Manager;

use App\Model\Entity\Device as EntityDevice;
use App\Model\Entity\Member as EntityMmeber;
use App\Model\Entity\RawData as EntityRawData;

use app\Utils\Common;
use \App\Utils\View;

class Inquiry extends Page {

    public static function getMyChart($request) {
        $postVars = $request->getPostVars();

        $_user = Common::get_manager();
        $_userInfo = EntityMmeber::getMemberById($_user);

        $array = array();
        $array[0]['info']['idx'] = "1";
        $array[0]['info']['member_idx'] = $_userInfo->idx;
        $array[0]['info']['graph_interval'] = Common::getInterval($postVars['graph_interval']) ;
        $_t = explode(" - ", $postVars['sdateAtedate']);
        $array[0]['info']['start'] = trim($_t[0]);
        $array[0]['info']['end'] = trim($_t[1]);



        $device_info = EntityDevice::getDevicesByIdx($postVars['device']);
        $board_type_info = Management::getBoardTypeName($device_info->board_type);

        $obj = array();
        $obj['address'] = $device_info->address;
        $obj['board_type'] = $device_info->board_type;
        $obj['board_number'] = $device_info->board_number;
        $obj['board_type_field'] = "";
        $obj['board_type_name'] = "";

        foreach($board_type_info as $k => $v) {
            if ($postVars['board'] == $v['field']) {
                $obj['board_type_field'] = $board_type_info[$k]['field'];
                $obj['board_type_name'] = $board_type_info[$k]['name'];
            }
        }
        $array[0]['datas'][] = $obj;


        $chart_arr = array();

        foreach ($array as $k_1 => $v_1) {
            $chart_arr[$k_1]['tag_name'] = "myChart_".$v_1['info']['idx'];
            $chart_arr[$k_1]['config'] = array(
                'type'      => 'line',
                'data'      =>  array('labels'=> array(), 'datasets'=>array()),
                'options'    =>  array(
                    'responsive' => true,
                    'maintainAspectRatio' => true,
                    'scales' => array(
                        'x' => array(

                                array('ticks'=> array( 'display'=>false)),
                            ),

                        ),
                    'plugins' => array(
                        'legend' => array(
                            'position' => 'bottom',
                        ),
                    ),
                ),
            );

            $chart_data_array = array();
            foreach ($v_1['datas'] as $k_2 => $v_2) {
                $chart_data_array['label'] = $v_2['board_type_name'];
                $chart_data_array['borderColor'] = "rgb(0, 0, 255)";
                $chart_data_array['backgroundColor'] = "rgb(0, 0, 255)";
                $chart_data_array['tension'] = 0.1;
                $chart_data_array['pointStyle'] = false;
                $chart_data_array['data'] = array();

                $group = "HOUR";


                $result_3 = EntityRawData::AvgDatesBetweenDate(
                                                            $v_2['address'],
                                                            $v_2['board_type'],
                                                            $v_2['board_type_field'],
                                                            $v_2['board_type_name'],
                                                            $v_1['info']['start'],
                                                            $v_1['info']['end'],
                                                            $group,
                                                            $v_1['info']['graph_interval']);
                while ($obj = $result_3->fetchObject(EntityRawData::class)) {
//                    if ($k_2 == 0) {
//                        array_push($chart_arr[$k_1]['config']['data']['labels'], substr( $obj->created, 5, 11) );
//                    }

                    array_push($chart_data_array['data'],
                        array(
                            'y' => round($obj->{$v_2['board_type_name']},1),
                            'x' => $obj->created,
                        )
                    );
//                    array_push($chart_data_array['data'], round($obj->{$v_2['board_type_name']},1));
                }

                array_push($chart_arr[$k_1]['config']['data']['datasets'], $chart_data_array);
            }
        }

        return [
            'success' => true,
            'obj' => $chart_arr
        ];
    }

    public static function getMyTable($address, $board_type, $board_type_field, $board_type_name, $sdateAtedate, $graph_interval) {
        $_t = explode(" - ", $sdateAtedate);

        $start = $_t[0];
        $end = $_t[1];

        $group = "HOUR";
        $graph_interval = Common::getInterval($graph_interval);

        $result_3 = EntityRawData::AvgDatesBetweenDate(
                                                        $address,
                                                        $board_type,
                                                        $board_type_field,
                                                        $board_type_name,
                                                        $start,
                                                        $end,
                                                        $group,
                                                        $graph_interval);

        $item = "";
        $_i = 1;

        while ($obj = $result_3->fetchObject(EntityRawData::class)) {
            $item .= View::render('manager/modules/inquiry/table_tr', [
                    'idx' => $_i,
                    'created' => substr($obj->created, 5, 11),
                    'data' => round( $obj->{$board_type_name},1),
            ]);
            $_i++;
        }

        return $item;
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

    private static function getIntervalOption($graph_interval) {
        $option = "";

        $interval = Common::getInterval();

        foreach ($interval as $k => $v) {
            $option .= View::render('manager/modules/dashboard/widget_add_form_options', [
                'value' => $k,
                'text'  => $v,
                'selected' => ($k == $graph_interval) ? 'selected' : '',
            ]);
        }

        return $option;
    }

    public static function getInquiry($request) {
        $postVars = $request->getQueryParams();

        $_user = Common::get_manager();
        $_userInfo = EntityMmeber::getMemberById($_user);

        $member_devices = Member::getMembersDevice($_userInfo->idx);

        $device = $postVars['device'] ?? '';
        $board = $postVars['board'] ?? '';
        $sdateAtedate = $postVars['sdateAtedate'] ?? date("Y-m-d")." - ".date("Y-m-d");

        $_idx = !$device ? $member_devices[0]['idx'] : $device;

        if ($_idx) {

            $obj = EntityDevice::getDevicesByIdx($_idx);

            $graph_interval = $postVars['graph_interval'] ?? 'PT1M';

            $address = $obj->address;
            $board_type = $obj->board_type;
            $board_type_info = Management::getBoardTypeName($board_type);

            if ($board) {
                foreach ($board_type_info as $k => $v) {
                    if ($board == $v['field']) {
                        $board_type_field = $board_type_info[$k]['field'];
                        $board_type_name = $board_type_info[$k]['name'];
                    }
                }
            } else {
                $board_type_field = $board_type_info[0]['field'];
                $board_type_name = $board_type_info[0]['name'];

            }

            $content = View::render('manager/modules/inquiry/index', [
                'device_options' => self::getMemberDevice($member_devices, $device),
                'board_options' => self::getMemberBoardType($obj, $board),
                'sdateAtedate' => $sdateAtedate,
                'interval_options' => self::getIntervalOption($graph_interval),
                'table_date' => self::getMyTable($address, $board_type, $board_type_field, $board_type_name, $sdateAtedate, $graph_interval),
                'board_type_name' => $board_type_name,
            ]);
        } else {
            $content = View::render('manager/modules/inquiry/index', [
                'table_date' => '',
            ]);
        }
        return parent::getPanel('Home > DASHBOARD', $content, 'inquiry');
    }



}