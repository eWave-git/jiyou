<?php

namespace App\Controller\Manager;

use App\Model\Entity\BoardTypeRef;
use App\Model\Entity\Member as EntityMmeber;
use App\Model\Entity\RawData as EntityRawData;
use app\Utils\Common;
use \App\Utils\View;

class Dashboard extends Page {

    public static function getWidgetBoard($request) {
        $postVars = $request->getPostVars();

        $results = Management::getBoardTypeName($postVars['board_type']);

        $arr = array();

        if ($results) {
            foreach ($results as $k => $v) {
                $arr['idx'][] =  $k;
                $arr['text'][] =  $v;
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

    private static function getMemberDevice($user_idx) {
        $option = "";

        $member_devices = Member::getMembersDevice($user_idx);
        if (is_array($member_devices[0])) {
            foreach ($member_devices as $k => $v) {
                $option .= View::render('manager/modules/dashboard/widget_add_form_options', [
                    'value' => $v['idx'],
                    'text'  => $v['address']."-".$v['board_type']."-".$v['board_number'],
                ]);
            }
        }

        return $option;
    }

    private static function widget_add($user_idx) {
        $item = "";
        $item = View::render('manager/modules/dashboard/widget_add_form', [
            'device_options' => self::getMemberDevice($user_idx),
        ]);

        return $item;
    }

    public static function getDashboard($request) {
        $_user = Common::get_manager();
        $_userInfo = EntityMmeber::getMemberById($_user);

        $content = View::render('manager/modules/dashboard/index', [
            'widget_add_form' => self::widget_add($_userInfo->idx),

        ]);


        return parent::getPanel('Home > DASHBOARD', $content, 'dashboard');
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