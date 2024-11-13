<?php

namespace App\Controller\Admin;

use \App\Utils\View;

class Alert {

    public static function getError($message) {
        return View::render('admin/alert/status', [
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