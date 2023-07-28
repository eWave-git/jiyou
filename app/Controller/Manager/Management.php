<?php

namespace App\Controller\Manager;

use \App\Utils\View;

class Management extends Page {

    public static function getManagement($request) {
        $content = View::render('manager/modules/management/index', []);

        return parent::getPanel('Home > DASHBOARD', $content, 'management');
    }

}