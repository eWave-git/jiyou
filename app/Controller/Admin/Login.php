<?php

namespace App\Controller\Admin;

use \app\Utils\Common;
use \App\Utils\View;
use \App\Model\Entity\Member;
use \App\Session\Admin\Login as SessionAdminLogin;

class Login extends Page {

    public static function getLogin($request, $errorMessage = null) {

        $content = View::render('admin/login',[
            'status' => !is_null($errorMessage) ? Alert::getError($errorMessage) : ''
        ]);

        return parent::getPage('ewave > Login', $content);
    }

    public static function setLogin($request) {

        $postVars = $request->getPostVars();
        $member_id    = $postVars['member_id'] ?? '';
        $member_password    = $postVars['member_password'] ?? '';

        $obUser = Member::getAdminMemberById($member_id);

        if (!$obUser instanceof Member) {
            return self::getLogin($request, 'id Error');
        }

        if (!password_verify($member_password, $obUser->member_password)) {
            return  self::getLogin($request, 'password Error');
        }

        SessionAdminLogin::login($obUser);

        $request->getRouter()->redirect('/admin/member_list');
    }

    public static function setLogout($request) {
        SessionAdminLogin::logout();

        $request->getRouter()->redirect('/admin/login');
    }
}
