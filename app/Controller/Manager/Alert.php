<?php

namespace App\Controller\Manager;

use \App\Utils\View;

class Alert {

    public static function getError($message) {
        return View::render('admin/alert/status', [
            'type' => 'danger',
            'message' => $message
        ]);
    }

    public static function getLoginError($message) {
        return View::render('manager/alert/status', [
            'type' => 'danger',
            'message' => $message
        ]);
    }

    public static function getSuccess($message) {
        return View::render('admin/alert/status', [
            'type' => 'success',
            'message' => $message
        ]);
    }
}