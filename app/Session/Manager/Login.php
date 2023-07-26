<?php

namespace App\Session\Manager;

class Login {

    private static function init() {
        if (session_status() != PHP_SESSION_ACTIVE) {
            session_start();
        }
    }

    public static function login($obUser) {

        self::init();;

        $_SESSION['manager']['user'] = [
            'id' => $obUser->member_id,
            'name' => $obUser->member_name,
            'type' => $obUser->member_type
        ];

        return true;
    }

    public static function isLogged() {

        self::init();

        return isset($_SESSION['manager']['user']['id']);
    }

    public static function logout() {
        self::init();

        unset($_SESSION['manager']['user']);

        return true;
    }
}