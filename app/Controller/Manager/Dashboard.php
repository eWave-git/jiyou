<?php

namespace App\Controller\Manager;


use \App\Model\Entity\Device as EntityDevice;
use App\Model\Entity\Member as EntityMmeber;
use App\Model\Entity\RawData as EntityRawData;
use App\Model\Entity\Widget as EntityWidget;
use App\Model\Entity\WidgetData as EntityWidgetData;
use app\Utils\Common;
use \App\Utils\View;



class Dashboard extends Page {

    public static function getWidgetBoard($request) {
        $postVars = $request->getPostVars();
        $obj = EntityDevice::getDevicesByIdx($postVars['device_idx']);

        $results = Management::getBoardTypeName($obj->board_type);

        $arr = array();

        if ($results) {
            foreach ($results as $k => $v) {
                $arr['idx'][] =  $v['field'];
                $arr['text'][] =  $v['name'];
            }
        } else {
            $arr['idx'][] =  '';
            $arr['text'][] =  '';
        }

        return [
            'success' => true,
            'idx'=>$arr['idx'],
            'text' => $arr['text'],
        ];
    }

    private static function getIntervalOption() {
        $option = "";

        $interval = Common::getInterval();

        foreach ($interval as $k => $v) {
            $option .= View::render('manager/modules/dashboard/widget_add_form_options', [
                'value' => $k,
                'text'  => $v,
                'selected' => '',
            ]);
        }

        return $option;
    }

    private static function getMemberDevice($user_idx) {
        $option = "";

        $member_devices = Member::getMembersDevice($user_idx);

        if ($member_devices[0]['idx']) {
            if (is_array($member_devices[0])) {
                foreach ($member_devices as $k => $v) {
                    $option .= View::render('manager/modules/dashboard/widget_add_form_options', [
                        'value' => $v['idx'],
                        'text'  => $v['address']."-".$v['board_type']."-".$v['board_number'],
                        'selected' => '',
                    ]);
                }
            }
        }

        return $option;
    }

    private static function widget_add($user_idx) {
        $item = "";
        $item = View::render('manager/modules/dashboard/widget_add_form', [
            'device_options' => self::getMemberDevice($user_idx),
            'interval_options' => self::getIntervalOption(),
        ]);

        return $item;
    }

    private static function wigget_table($user_idx) {

        $item = "";

        $result_1 = EntityWidget::getWidgeTablebyMemberIdx($user_idx);

        $array = array();
        $i = 0;
        while ($obj_1 = $result_1->fetchObject(EntityWidget::class)) {
            $array[$i]['info'] = (array)$obj_1;

            $result_2 = EntityWidgetData::getWidgetDatesByWidgetIdx($obj_1->idx);
            $j = 0;

            while ($obj_2 = $result_2->fetchObject(EntityWidgetData::class)) {
                $array[$i]['datas'][$j] = (array)$obj_2;
                $symbol = Common::findSymbol($obj_2->board_type_name);

                if (isset($symbol['idx'])) {
                    $array[$i]['datas'][$j]['symbol'] = $symbol['symbol'];
                    $array[$i]['datas'][$j]['standard'] = $symbol['standard'];
                }
                $j++;
            }
            $i++;
        }

        $table_arr = array();

        foreach ($array as $k_1 => $v_1) {
            $result_3 = EntityRawData::LastLimitDataOne($v_1['datas'][0]['address'], $v_1['datas'][0]['board_type'], $v_1['datas'][0]['board_number'], $v_1['datas'][0]['board_type_field'], $v_1['datas'][0]['board_type_name']);
            $obj_3 = $result_3->fetchObject(EntityRawData::class);
            $table_arr[$k_1]['title'] = $v_1['info']['widget_name'];
            $table_arr[$k_1]['idx'] = $v_1['info']['idx'];
            $table_arr[$k_1]['text'] = $v_1['datas'][0]['board_type_name'];
            $table_arr[$k_1]['value'] = $obj_3->{$v_1['datas'][0]['board_type_name']} ?? 0;
            $table_arr[$k_1]['symbol'] = $v_1['datas'][0]['symbol'] ?? '';
            $table_arr[$k_1]['standard'] = $v_1['datas'][0]['standard'] ?? '';
        }

        foreach ($table_arr as $k => $v) {

            if ($v['standard']) {
                if ($v['symbol'] == "PPM") {
                    $v['value'] =  ($v['value']*100) / 5000 ;
                } else if ($v['symbol'] == "ug/㎥") {
                    $v['value'] =  ($v['value']*100) / 1000 ;
                } else if ($v['symbol'] == "lux") {
                    $v['value'] =  ($v['value']*100) / 65000 ;
                }
            }

            $item .= View::render('manager/modules/dashboard/widget_table', [
                'title' => $v['title'],
                'idx' => $v['idx'],
                'text' => $v['text'],
                'symbol' => $v['symbol'],
                'value' => round($v['value'],1),
                'update_date' => date("m-d H:i"),
            ]);
        }

        return $item;
    }

    private static function widget_chart($user_idx) {

        $item = "";

        $result = EntityWidget::getWidgeChartbyMemberIdx($user_idx);

        while($obj = $result->fetchObject(EntityWidget::class)) {
            $item .= View::render('manager/modules/dashboard/widget_chart', [
                'title' => $obj->widget_name,
                'idx' => $obj->idx,
                'chart_idx' => "myChart_".$obj->idx,
            ]);
        }

        return $item;
    }

    public static function getDashboard($request) {
        $_user = Common::get_manager();
        $_userInfo = EntityMmeber::getMemberById($_user);

        $_farm_Info = EntityMmeber::getMembersFarm($_userInfo->idx)->fetchObject(EntityMmeber::class);

        $content = View::render('manager/modules/dashboard/index', [
            'farm_name' => $_farm_Info->farm_name,
            'widget_add_form' => self::widget_add($_userInfo->idx),
            'widget_table'  => self::wigget_table($_userInfo->idx),
            'widget_chart'  => self::widget_chart($_userInfo->idx),
        ]);


        return parent::getPanel('Home > DASHBOARD', $content, 'dashboard');
    }


    public static function setWidgetAdd($request) {

        $_user = Common::get_manager();
        $_userInfo = EntityMmeber::getMemberById($_user);

        $postVars = $request->getPostVars();

        $device_info = EntityDevice::getDevicesByIdx($postVars['widget_device']);

        $board_type_info = Management::getBoardTypeName($device_info->board_type);

        $obj = new EntityWidget;
        $obj->member_idx = $_userInfo->idx;
        $obj->widget_name = $postVars['widget_name'];
        $obj->widget_type = $postVars['widget_type'];
        $obj->graph_interval = $postVars['widget_graph_interval'];

        $_idx = $obj->created();

        $obj_data = new EntityWidgetData();
        $obj_data->widget_idx = $_idx;
        $obj_data->device_idx = $device_info->idx;
        $obj_data->address = $device_info->address;
        $obj_data->board_type = $device_info->board_type;
        $obj_data->board_number = $device_info->board_number;

        foreach($board_type_info as $k => $v) {
            if ($postVars['widget_board'] == $v['field']) {
                $obj_data->board_type_field = $board_type_info[$k]['field'];
                $obj_data->board_type_name = $board_type_info[$k]['name'];
            }
        }

        $obj_data->created();

        $request->getRouter()->redirect('/manager/dashboard');
    }

    public static function setWidgetRemove($request, $idx) {

        $obj = EntityWidget::getWidgets("idx='".$idx."'",'','','*')->fetchObject(EntityWidget::class);
        $obj->deleted();

        $obj = EntityWidgetData::getWidgetDatesByWidgetIdx($idx)->fetchObject(EntityWidgetData::class);
        $obj->deleted();

        $request->getRouter()->redirect('/manager/dashboard');
    }



    public static function getMyChart($request) {
        $postVars = $request->getPostVars();

        $_user = Common::get_manager();
        $_userInfo = EntityMmeber::getMemberById($_user);

        $result_1 = EntityWidget::getWidgeChartbyMemberIdx($_userInfo->idx);

        $array = array();
        $i = 0;
        while ($obj_1 = $result_1->fetchObject(EntityWidget::class)) {
            $array[$i]['info'] = (array)$obj_1;

            $result_2 = EntityWidgetData::getWidgetDatesByWidgetIdx($obj_1->idx);
            $j = 0;

            while ($obj_2 = $result_2->fetchObject(EntityWidgetData::class)) {
                $array[$i]['datas'][$j] = (array)$obj_2;
                $symbol = Common::findSymbol($obj_2->board_type_name);

                if (isset($symbol['idx'])) {
                    $array[$i]['datas'][$j]['symbol'] = $symbol['symbol'];
                    $array[$i]['datas'][$j]['standard'] = $symbol['standard'];
                }
                $j++;
            }

            $i++;
        }

        $chart_arr = array();

        foreach ($array as $k_1 => $v_1) {
            $chart_arr[$k_1]['tag_name'] = "myChart_".$v_1['info']['idx'];
            $chart_arr[$k_1]['config'] = array(
                'type'      => 'line',
                'data'      =>  array('labels'=> array(), 'datasets'=>array()),
                'options'    =>  array(
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

                if ($v_2['symbol'] == 'L') {
                    $result_3 = EntityRawData::AccumulateDatas($v_2['address'], $v_2['board_type'], $v_2['board_type_field'], $v_2['board_type_name'],'0', '10');
                } else {
                    $result_3 = EntityRawData::AvgDatas($v_2['address'], $v_2['board_type'], $v_2['board_type_field'], $v_2['board_type_name'],'0', '10');
                }
                while ($obj = $result_3->fetchObject(EntityRawData::class)) {
//                    if ($k_2 == 0) {
//                        array_push($chart_arr[$k_1]['config']['data']['labels'], substr( $obj->created, 11, 5) );
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