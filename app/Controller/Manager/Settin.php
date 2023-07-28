<?php

namespace App\Controller\Manager;

use \App\Utils\View;

class Settin extends Page {

    public static function getSettin($request) {
        $content = View::render('manager/modules/settin/index', []);

        return parent::getPanel('Home > DASHBOARD', $content, 'settin');
    }

}