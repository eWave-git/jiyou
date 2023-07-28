<?php

namespace App\Controller\Manager;

use \App\Utils\View;

class All extends Page {

    public static function getAll($request) {
        $content = View::render('manager/modules/all/index', []);

        return parent::getPanel('Home > DASHBOARD', $content, 'all');
    }

}