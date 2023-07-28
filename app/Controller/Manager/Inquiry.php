<?php

namespace App\Controller\Manager;

use \App\Utils\View;

class Inquiry extends Page {

    public static function getInquiry($request) {
        $content = View::render('manager/modules/inquiry/index', []);

        return parent::getPanel('Home > DASHBOARD', $content, 'inquiry');
    }

}