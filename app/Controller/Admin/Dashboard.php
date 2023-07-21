<?php

namespace App\Controller\Admin;

use \App\Utils\View;

class Dashboard extends Page {

    public static function getDashboard() {
        $content = View::render('admin/modules/dashboard/index', []);

        return parent::getPanel('Home > DASHBOARD', $content, 'dashboard');
    }

}