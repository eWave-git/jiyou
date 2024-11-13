<?php

namespace App\Controller\Admin;

use \App\Utils\View;

class Home extends Page {

    public static function getHome() {
        $content = View::render('admin/modules/home/index', []);

        return parent::getPanel('Home > DASHBOARD', $content, 'dashboard');
    }

}