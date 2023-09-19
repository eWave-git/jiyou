<?php

namespace App\Controller\Manager;

use \App\Utils\View;
use \App\Model\Entity\Member;
use \App\Session\Manager\Login as SessionManagerLogin;


class Login extends Page {
    public static function getLogin($request, $errorMessage = null) {

        $content = View::render('manager/login',[
            'status' => !is_null($errorMessage) ? Alert::getError($errorMessage) : ''
        ]);

        return parent::getPage('ewave > Login', $content);
    }

    public static function setLogin($request) {

        $postVars = $request->getPostVars();
        $member_id    = $postVars['member_id'] ?? '';
        $member_password    = $postVars['member_password'] ?? '';
        $auto_login    = isset($postVars['auto_login']) ? 'Y': 'N';

        $obUser = Member::getManagerMemberById($member_id);

        if (!$obUser instanceof Member) {
            return self::getLogin($request, 'id Error');
        }

        if (!password_verify($member_password, $obUser->member_password)) {
            return  self::getLogin($request, 'password Error');
        }

        if ($auto_login == "Y") {
            setcookie("cookie_id",$obUser->member_id,(time()+3600*24*30*365*10),"/"); // 십년간 자동로그인 유지
        }

        SessionManagerLogin::login($obUser);

        $request->getRouter()->redirect('/manager/dashboard');
    }

    public static function setLogout($request) {
        if (isset($_COOKIE['cookie_id'])) {
            setcookie('cookie_id', '', time() - 100, '/');
        }

        SessionManagerLogin::logout();

        $request->getRouter()->redirect('/manager/login');
    }
}