<?php

namespace App\Controller\Manager;

use \app\Utils\Common;
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

        $obUser = Member::getManagerMemberById($member_id);

        if (!$obUser instanceof Member) {
            return self::getLogin($request, 'id Error');
        }

        if (!password_verify($member_password, $obUser->member_password)) {
            return  self::getLogin($request, 'password Error');
        }

        SessionManagerLogin::login($obUser);

        $request->getRouter()->redirect('/manager/dashboard');
    }

    public static function setLogout($request) {
        SessionManagerLogin::logout();

        $request->getRouter()->redirect('/manager/login');
    }
}