<?php

namespace App\Controller\Manager;

use App\Controller\Admin\BoardTypeRef;

use app\Utils\Common;
use \App\Utils\View;

class Management extends Page {

    public static function getBoardTypeName($board_type) {
        $array = array();
        $array =  BoardTypeRef::getBoardTypeNameArray($board_type);

        return $array;
    }


    public static function getManagement($request) {
        $content = View::render('manager/modules/management/index', []);

        return parent::getPanel('Home > DASHBOARD', $content, 'management');
    }

}