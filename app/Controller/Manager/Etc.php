<?php

namespace App\Controller\Manager;

use App\Model\Entity\Member as EntityMmeber;
use app\Utils\Common;
use \App\Utils\View;

class Etc extends Page {

    public static function getEtc($request) {
        $content = View::render('manager/modules/etc/index', []);

        return parent::getPanel('Home > DASHBOARD', $content, 'etc');
    }

    public static function Password_Change($request) {
        $content = View::render('manager/modules/etc/password_change', []);

        return parent::getPanel('Home > DASHBOARD', $content, 'etc');
    }

    public static function Password_Change_Post($request) {
        $postVars = $request->getPostVars();

        $_user = Common::get_manager();
        $_userInfo = EntityMmeber::getMemberById($_user);

        $password = password_hash($postVars['password'], PASSWORD_DEFAULT);

        EntityMmeber::PasswordChange($_userInfo->member_id, $password);

        $request->getRouter()->redirect('/manager');


    }

}