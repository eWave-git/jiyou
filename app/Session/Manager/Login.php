<?php

namespace App\Session\Manager;

use App\Model\Entity\Member;
use App\Session\Manager\Login as SessionManagerLogin;
use app\Utils\Common;

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

        if (isset($_COOKIE['cookie_id']) && !isset(['manager']['user']['id'])) {
            $obUser = Member::getManagerMemberById($_COOKIE['cookie_id']);
            SessionManagerLogin::login($obUser);
        }

        return isset($_SESSION['manager']['user']['id']);
    }

    public static function logout() {
        self::init();

        unset($_SESSION['manager']['user']);

        return true;
    }
}