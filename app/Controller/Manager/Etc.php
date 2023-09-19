<?php

namespace App\Controller\Manager;

use \App\Utils\View;

class Etc extends Page {

    public static function getEtc($request) {
        $content = View::render('manager/modules/etc/index', []);

        return parent::getPanel('Home > DASHBOARD', $content, 'etc');
    }

}