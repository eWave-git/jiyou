<?php

namespace App\Controller\Manager;

use App\Model\Entity\Device as EntityDevice;
use App\Model\Entity\Member as EntityMember;
use App\Model\Entity\RawData as EntityRawData;

use App\Model\Entity\Widget as EntityWidget;
use app\Utils\Common;
use \App\Utils\View;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;

class Inquiry extends Page {

    private static function getMemberDevice($user_idx, $idx = '') {
        $option = "";

        if ($user_idx) {

            foreach (Common::getMembersWidget($user_idx) as $k => $v) {
                $option .= View::render('manager/modules/inquiry/options', [
                    'value' => $v['idx'],
                    'text'  => $v['widget_name'],
                    'selected' => ($v['idx'] == $idx ) ? 'selected' : '',
                ]);
            }
        }

        return $option;
    }

    public static function getChartInquiry($request) {
        $_user = Common::get_manager();
        $_userInfo = EntityMember::getMemberById($_user);

        $sdateAtedate = $postVars['sdateAtedate'] ?? date("Y-m-d")." - ".date("Y-m-d");

        $content = View::render('manager/modules/inquiry/chart_inquiry', [
            'device_options' => self::getMemberDevice($_userInfo->idx),
            'sdateAtedate'=>$sdateAtedate,
        ]);

        return parent::getPanel('Home > DASHBOARD', $content, 'inquiry');
    }

    public static function getTableInquiry($request) {

        $_user = Common::get_manager();
        $_userInfo = EntityMember::getMemberById($_user);

        $sdateAtedate = $postVars['sdateAtedate'] ?? date("Y-m-d")." - ".date("Y-m-d");

        $content = View::render('manager/modules/inquiry/table_inquiry', [
            'device_options' => self::getMemberDevice($_userInfo->idx),
            'sdateAtedate'=>$sdateAtedate,
            'data_1' => '',
            'data_2' => '',
            'data_3' => '',
            'data_4' => '',
            'data_5' => '',
            'data_6' => '',
            'data_7' => '',
            'data_8' => '',
            'table_datas' => '',
        ]);

        return parent::getPanel('Home > DASHBOARD', $content, 'inquiry');
    }

    public static function getTableSearch($request) {
        $queryParams = $request->getQueryParams();

        $_user = Common::get_manager();
        $_userInfo = EntityMember::getMemberById($_user);

        if (!empty($queryParams['device'])) {
            $idx = $queryParams['device'] ?? '';

            $widget_obj = EntityWidget::getWidgetByIdx($queryParams['device'])->fetchObject(EntityWidget::class);
            $board_type_array = Common::getbordTypeNameByWidgetNameArray($widget_obj->device_idx, $widget_obj->board_type);

            list ($start_date, $end_date) = explode(" - ", $queryParams['sdateAtedate']);

            $array = array();
            $fields = array();

            foreach ($board_type_array as $k => $v) {
                if ($v['display'] == 'Y') {
                    if ($v['symbol'] != 'L') {
                        $row = EntityRawData::DatesBetweenDate($widget_obj->address, $widget_obj->board_type, $widget_obj->board_number, $v['field'], $v['name'], $start_date, $end_date);
                        $kk = 0;
                        while ($row_obj = $row->fetchObject(EntityRawData::class)) {
                            $array[$kk]['dates'] = $row_obj->created;
                            $array[$kk][$v['field']] = $row_obj->{$v['name']};
                            $kk++;
                        }

                        $fields[$k]['field'] = $v['field'];
                        $fields[$k]['name'] = $v['name'];
                    } else {
                        $row = EntityRawData::WaterDatesBetweenDatesMinute($widget_obj->address, $widget_obj->board_type, $widget_obj->board_number, $v['field'], $v['name'], $start_date, $end_date);
                        $kk = 0;
                        while ($row_obj = $row->fetchObject(EntityRawData::class)) {
                            $array[$kk]['dates'] = $row_obj->created;
                            $array[$kk][$v['field']] = $row_obj->{$v['name']};
                            $kk++;
                        }

                        $fields[$k]['field'] = $v['field'];
                        $fields[$k]['name'] = $v['name'];
                    }
                }
            }

            // TODO : $fields[$i]['filed'] -> $fields[$i]['field'] 수정 해야함.
//            if (count($fields) < 9) {
//                for ($i = count($fields); $i < 9; $i++) {
//                    $fields[$i]['filed'] = 'data' . $i;
//                    $fields[$i]['name'] = '';
//                }
//            }

            $content = View::render('manager/modules/inquiry/table_inquiry', [
                'device_options' => self::getMemberDevice($_userInfo->idx, $idx),
                'sdateAtedate' => $queryParams['sdateAtedate'],
                'data_1' => $fields[0]['name'] ?? '',
                'data_2' => $fields[1]['name'] ?? '',
                'data_3' => $fields[2]['name'] ?? '',
                'data_4' => $fields[3]['name'] ?? '',
                'data_5' => $fields[4]['name'] ?? '',
                'data_6' => $fields[5]['name'] ?? '',
                'data_7' => $fields[6]['name'] ?? '',
                'data_8' => $fields[7]['name'] ?? '',
                'table_datas' => self::getTableSearchDetail($array, $fields),
            ]);
        } else {
            $content = View::render('manager/modules/inquiry/table_inquiry', [
                'device_options' => self::getMemberDevice($_userInfo->idx),
                'sdateAtedate' => $queryParams['sdateAtedate'],
                'data_1' => '',
                'data_2' => '',
                'data_3' => '',
                'data_4' => '',
                'data_5' => '',
                'data_6' => '',
                'data_7' => '',
                'data_8' => '',
                'table_datas' => self::getTableSearchDetail(array(), ''),
            ]);
        }

        return parent::getPanel('Home > DASHBOARD', $content, 'inquiry');
    }


    public static function getTableSearchDetail($array, $fields) {
        $data = "";
        if (count($array) >0 ) {
            foreach ($array as $k => $v) {
                $data .= View::render('manager/modules/inquiry/table_inquiry_detail',[
                    'number' => $k+1,
                    'created' => $v['dates'],
                    'data_1' => isset($fields[0]['field']) ? (isset($v[$fields[0]['field']]) ? $v[$fields[0]['field']] : '0') : '',
                    'data_2' => isset($fields[1]['field']) ? (isset($v[$fields[1]['field']]) ? $v[$fields[1]['field']] : '0') : '',
                    'data_3' => isset($fields[2]['field']) ? (isset($v[$fields[2]['field']]) ? $v[$fields[2]['field']] : '0') : '',
                    'data_4' => isset($fields[3]['field']) ? (isset($v[$fields[3]['field']]) ? $v[$fields[3]['field']] : '0') : '',
                    'data_5' => isset($fields[4]['field']) ? (isset($v[$fields[4]['field']]) ? $v[$fields[4]['field']] : '0') : '',
                    'data_6' => isset($fields[5]['field']) ? (isset($v[$fields[5]['field']]) ? $v[$fields[5]['field']] : '0') : '',
                    'data_7' => isset($fields[6]['field']) ? (isset($v[$fields[6]['field']]) ? $v[$fields[6]['field']] : '0') : '',
                    'data_8' => isset($fields[7]['field']) ? (isset($v[$fields[7]['field']]) ? $v[$fields[7]['field']] : '0') : '',
                ]);
            }
        } else {
            $data .= View::render('manager/modules/inquiry/table_inquiry_no_detail',[
            ]);
        }


        return $data;
    }

    public static function getTableExcelDownload($request) {
        $queryParams = $request->getQueryParams();

        if (!empty($queryParams['device'])) {

            $widget_obj = EntityWidget::getWidgetByIdx($queryParams['device'])->fetchObject(EntityWidget::class);
            $board_type_array = Common::getbordTypeNameByWidgetNameArray($widget_obj->device_idx, $widget_obj->board_type);

            list ($start_date, $end_date) = explode(" - ", $queryParams['sdateAtedate']);

            $array = array();
            $fields = array();

            foreach ($board_type_array as $k => $v) {
                if ($v['display'] == 'Y') {
                    if ($v['symbol'] != 'L') {
                        $row = EntityRawData::DatesBetweenDate($widget_obj->address, $widget_obj->board_type, $widget_obj->board_number, $v['field'], $v['name'], $start_date, $end_date);
                        $kk = 0;
                        while ($row_obj = $row->fetchObject(EntityRawData::class)) {
                            $array[$kk]['dates'] = $row_obj->created;
                            $array[$kk][$v['field']] = $row_obj->{$v['name']};
                            $kk++;
                        }

                        $fields[$k]['field'] = $v['field'];
                        $fields[$k]['name'] = $v['name'];
                    } else {
                        $row = EntityRawData::WaterDatesBetweenDatesMinute($widget_obj->address, $widget_obj->board_type, $widget_obj->board_number, $v['field'], $v['name'], $start_date, $end_date);
                        $kk = 0;
                        while ($row_obj = $row->fetchObject(EntityRawData::class)) {
                            $array[$kk]['dates'] = $row_obj->created;
                            $array[$kk][$v['field']] = $row_obj->{$v['name']};
                            $kk++;
                        }

                        $fields[$k]['field'] = $v['field'];
                        $fields[$k]['name'] = $v['name'];
                    }
                }
            }

            $cells = array('C', 'D', 'E', 'F', 'G', 'H', 'I', 'J');

            $spreadsheet = new Spreadsheet();
            $spreadsheet->setActiveSheetIndex(0);

            $out_put_file_full_name = $widget_obj->widget_name;

            $spreadsheet->setActiveSheetIndex(0)->setCellValue("A1", "번호");
            $spreadsheet->setActiveSheetIndex(0)->setCellValue("B1", "날짜시간");

            foreach ($fields as $k => $v) {
                $spreadsheet->setActiveSheetIndex(0)->setCellValue($cells[$k] . "1", $v['name']);
            }

            $cellsRow = 2;
            foreach ($array as $k => $v) {
                $spreadsheet->setActiveSheetIndex(0)->setCellValue("A" . $cellsRow, $k + 1);
                $spreadsheet->setActiveSheetIndex(0)->setCellValue("B" . $cellsRow, $v['dates']);

                foreach ($fields as $kk => $vv) {
                    if (isset($v[$vv['field']])) {
                        $spreadsheet->setActiveSheetIndex(0)->setCellValue($cells[$kk] . $cellsRow, $v[$vv['field']]);
                    } else {
                        $spreadsheet->setActiveSheetIndex(0)->setCellValue($cells[$kk] . $cellsRow, '0');
                    }
                }

                $cellsRow++;
            }

            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment; filename="' . $out_put_file_full_name . '.xlsx"');


            $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
            $writer->save('php://output');
            exit;
        } else {
            $request->getRouter()->redirect('/manager/table_inquiry');
        }
    }

    public static function getChartSearch($request) {
        $postVars = $request->getPostVars();

        if (!empty($postVars['widget_idx'])) {
            $widget_obj = EntityWidget::getWidgetByIdx($postVars['widget_idx'])->fetchObject(EntityWidget::class);
            $board_type_array = Common::getbordTypeNameByWidgetNameArray($widget_obj->device_idx, $widget_obj->board_type);

            list ($start_date, $end_date) = explode(" - ", $postVars['sdateAtedate']);

            $type = $postVars['type'];
            $interval = $postVars['interval'];

            $array = array();
            $fields = array();

            foreach($board_type_array as $k => $v) {
                if ($v['display'] == 'Y') {

                    if ($type == "normal") {
                        if ($v['symbol'] != 'L') {
                            $row = EntityRawData::AvgDatesBetweenDate($widget_obj->address, $widget_obj->board_type, $widget_obj->board_number,1, $v['field'], $v['name'],  $start_date, $end_date);
                            $kk = 0;
                            while ($row_obj = $row->fetchObject(EntityRawData::class)) {
                                $array[$kk]['dates'] = $row_obj->created;
                                $array[$kk][$v['field']] =  round($row_obj->{$v['name']}, 1);
                                $kk++;
                            }

                            $fields[$k]['field'] = $v['field'];
                            $fields[$k]['name'] = $v['name'];
                            $fields[$k]['series'] = 'series'.$k;
                            $fields[$k]['yAxis'] = 'yAxis'.$k;
                        }
                    } else if ($type == "water") {
                        if ($v['symbol'] == 'L') {

                            if ($interval == 'H') {
                                $intervals = 1;
                            } else if ($interval == 'D') {
                                $intervals = 24;
                            }

                            $row = EntityRawData::WaterDatesBetweenDate($widget_obj->address, $widget_obj->board_type, $widget_obj->board_number, $intervals, $v['field'], $v['name'],  $start_date, $end_date);
                            $kk = 0;
                            while ($row_obj = $row->fetchObject(EntityRawData::class)) {
                                $array[$kk]['dates'] = $row_obj->created;
                                $array[$kk][$v['field']] = round($row_obj->{$v['name']},1);
                                $kk++;
                            }

                            $fields[$k]['field'] = $v['field'];
                            $fields[$k]['name'] = $v['name'];
                            $fields[$k]['series'] = 'series'.$k;
                            $fields[$k]['yAxis'] = 'yAxis'.$k;
                        }
                    }
                }
            }

            return [
                'success' => true,
                'obj' => $array,
                'fields' => $fields,
            ];
        }

    }

//    public static function getMyChart($request) {
//        $postVars = $request->getPostVars();
//
//        $_user = Common::get_manager();
//        $_userInfo = EntityMember::getMemberById($_user);
//
//        $array = array();
//        $array[0]['info']['idx'] = "1";
//        $array[0]['info']['member_idx'] = $_userInfo->idx;
//        $array[0]['info']['graph_interval'] = Common::getInterval($postVars['graph_interval']) ;
//        $_t = explode(" - ", $postVars['sdateAtedate']);
//        $array[0]['info']['start'] = trim($_t[0]);
//        $array[0]['info']['end'] = trim($_t[1]);
//
//
//
//        $device_info = EntityDevice::getDevicesByIdx($postVars['device']);
//        $board_type_info = Management::getBoardTypeName($device_info->board_type);
//
//        $obj = array();
//        $obj['address'] = $device_info->address;
//        $obj['board_type'] = $device_info->board_type;
//        $obj['board_number'] = $device_info->board_number;
//        $obj['board_type_field'] = "";
//        $obj['board_type_name'] = "";
//
//        foreach($board_type_info as $k => $v) {
//            if ($postVars['board'] == $v['field']) {
//                $obj['board_type_field'] = $board_type_info[$k]['field'];
//                $obj['board_type_name'] = $board_type_info[$k]['name'];
//            }
//        }
//        $array[0]['datas'][] = $obj;
//
//
//        $chart_arr = array();
//
//        foreach ($array as $k_1 => $v_1) {
//            $chart_arr[$k_1]['tag_name'] = "myChart_".$v_1['info']['idx'];
//            $chart_arr[$k_1]['config'] = array(
//                'type'      => 'line',
//                'data'      =>  array('labels'=> array(), 'datasets'=>array()),
//                'options'    =>  array(
//                    'responsive' => true,
//                    'maintainAspectRatio' => false,
//                    'scales' => array(
//                        'x' => array(
//                                array('type'=>'time'),
//                                array('time'=>array('unit'=>'hour')),
//
//                            ),
//
//                        ),
//                    'plugins' => array(
//                        'legend' => array(
//                            'position' => 'bottom',
//                        ),
//                    ),
//                ),
//            );
//
//            $chart_data_array = array();
//            foreach ($v_1['datas'] as $k_2 => $v_2) {
//                $chart_data_array['label'] = $v_2['board_type_name'];
//                $chart_data_array['borderColor'] = "rgb(0, 0, 255)";
//                $chart_data_array['backgroundColor'] = "rgb(0, 0, 255)";
//                $chart_data_array['tension'] = 0.1;
//                $chart_data_array['pointStyle'] = false;
//                $chart_data_array['data'] = array();
//
//
//                $result_3 = EntityRawData::AvgDatesBetweenDate(
//                                                            $v_2['address'],
//                                                            $v_2['board_type'],
//                                                            $v_2['board_type_field'],
//                                                            $v_2['board_type_name'],
//                                                            $v_1['info']['start'],
//                                                            $v_1['info']['end'],
//                                                            $v_1['info']['graph_interval']);
//                while ($obj = $result_3->fetchObject(EntityRawData::class)) {
////                    if ($k_2 == 0) {
////                        array_push($chart_arr[$k_1]['config']['data']['labels'], substr( $obj->created, 5, 11) );
////                    }
//
//                    array_push($chart_data_array['data'],
//                        array(
//                            'y' => round($obj->{$v_2['board_type_name']},1),
//                            'x' => $obj->created,
//                        )
//                    );
////                    array_push($chart_data_array['data'], round($obj->{$v_2['board_type_name']},1));
//                }
//
//                array_push($chart_arr[$k_1]['config']['data']['datasets'], $chart_data_array);
//            }
//        }
//
//        return [
//            'success' => true,
//            'obj' => $chart_arr
//        ];
//    }
//
//    public static function getMyTable($address, $board_type, $board_type_field, $board_type_name, $sdateAtedate, $graph_interval) {
//        $_t = explode(" - ", $sdateAtedate);
//
//        $start = $_t[0];
//        $end = $_t[1];
//
//        $graph_interval = Common::getInterval($graph_interval);
//
//        $result_3 = EntityRawData::AvgDatesBetweenDate(
//                                                        $address,
//                                                        $board_type,
//                                                        $board_type_field,
//                                                        $board_type_name,
//                                                        $start,
//                                                        $end,
//                                                        $graph_interval);
//
//        $item = "";
//        $_i = 1;
//
//        while ($obj = $result_3->fetchObject(EntityRawData::class)) {
//            $item .= View::render('manager/modules/inquiry/table_tr', [
//                    'idx' => $_i,
//                    'created' => substr($obj->created, 5, 11),
//                    'data' => round( $obj->{$board_type_name},1),
//            ]);
//            $_i++;
//        }
//
//        return $item;
//    }
//    private static function getMemberDevice($member_devices, $device = '') {
//        $option = "";
//
//        if ($member_devices[0]['idx']) {
//            if (is_array($member_devices[0])) {
//                foreach ($member_devices as $k => $v) {
//                    $option .= View::render('manager/modules/dashboard/widget_add_form_options', [
//                        'value' => $v['idx'],
//                        'text'  => $v['address']."-".$v['board_type']."-".$v['board_number'],
//                        'selected' => ($v['idx'] == $device) ? 'selected' : '',
//                    ]);
//                }
//            }
//        }
//
//        return $option;
//    }
//
//    private static function getMemberBoardType($obj, $board) {
//
//        $results = Management::getBoardTypeName($obj->board_type);
//        $option = "";
//        foreach ($results as $k => $v) {
//            $option .= View::render('manager/modules/dashboard/widget_add_form_options', [
//                'value' => $v['field'],
//                'text'  => $v['name'],
//                'selected' => ($v['field'] == $board) ? 'selected' : '',
//            ]);
//        }
//
//        return $option;
//    }
//
//    private static function getIntervalOption($graph_interval) {
//        $option = "";
//
//        $interval = Common::getInterval();
//
//        foreach ($interval as $k => $v) {
//            $option .= View::render('manager/modules/dashboard/widget_add_form_options', [
//                'value' => $k,
//                'text'  => $v,
//                'selected' => ($k == $graph_interval) ? 'selected' : '',
//            ]);
//        }
//
//        return $option;
//    }
//
//    public static function getInquiry($request) {
//        $postVars = $request->getQueryParams();
//
//        $_user = Common::get_manager();
//        $_userInfo = EntityMember::getMemberById($_user);
//
//        $member_devices = Common::getMembersDevice($_userInfo->idx);
//
//        $device = $postVars['device'] ?? '';
//        $board = $postVars['board'] ?? '';
//        $sdateAtedate = $postVars['sdateAtedate'] ?? date("Y-m-d")." - ".date("Y-m-d");
//
//        $_idx = !$device ? $member_devices[0]['idx'] : $device;
//
//        if ($_idx) {
//
//            $obj = EntityDevice::getDevicesByIdx($_idx);
//
//            $graph_interval = $postVars['graph_interval'] ?? 'PT1M';
//
//            $address = $obj->address;
//            $board_type = $obj->board_type;
//            $board_type_info = Management::getBoardTypeName($board_type);
//
//            if ($board) {
//                foreach ($board_type_info as $k => $v) {
//                    if ($board == $v['field']) {
//                        $board_type_field = $board_type_info[$k]['field'];
//                        $board_type_name = $board_type_info[$k]['name'];
//                    }
//                }
//            } else {
//                $board_type_field = $board_type_info[0]['field'];
//                $board_type_name = $board_type_info[0]['name'];
//
//            }
//
//            $content = View::render('manager/modules/inquiry/index', [
//                'device_options' => self::getMemberDevice($member_devices, $device),
//                'board_options' => self::getMemberBoardType($obj, $board),
//                'sdateAtedate' => $sdateAtedate,
//                'interval_options' => self::getIntervalOption($graph_interval),
//                'table_date' => self::getMyTable($address, $board_type, $board_type_field, $board_type_name, $sdateAtedate, $graph_interval),
//                'board_type_name' => $board_type_name,
//            ]);
//        } else {
//            $content = View::render('manager/modules/inquiry/index', [
//                'table_date' => '',
//            ]);
//        }
//        return parent::getPanel('Home > DASHBOARD', $content, 'inquiry');
//    }



}