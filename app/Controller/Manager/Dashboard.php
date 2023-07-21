<?php

namespace App\Controller\Manager;

use \App\Utils\View;

class Dashboard extends Page {

    public static function getDashboard() {
        $content = View::render('manager/modules/dashboard/index', []);

        return parent::getPanel('Home > DASHBOARD', $content, 'dashboard');
    }

}