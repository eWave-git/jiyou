<?php

namespace App\Controller\Manager;

use App\Model\Entity\Member as EntityMmeber;
use App\Model\Entity\RawData as EntityRawData;
use app\Utils\Common;
use \App\Utils\View;

class Dashboard extends Page {

    public static function getDashboard($request) {
        $content = View::render('manager/modules/dashboard/index', []);






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