<?php

namespace App\Controller\Manager;

use App\Controller\Admin\BoardTypeRef;
use \App\Model\Entity\Farm as EntityFarm;
use \App\Model\Entity\Member as EntityMmeber;
use app\Utils\Common;
use \App\Utils\View;

class Member extends Page {

    private static function getMemberJoinFarm() {
        $options = '';

        $results = EntityMmeber::getMemberJoinFarm();

        while ($obMember = $results->fetchObject(EntityMmeber::class)) {
            $options .= View::render('manager/modules/member/member_join_options', [
                'value' => $obMember->idx,
                'text'  => $obMember->farm_name,
            ]);
        }

        return $options;
    }

    public static function Member_Join($request) {
        $content = View::render('manager/modules/member/member_join', [
            'member_farm_options' => self::getMemberJoinFarm(),
        ]);

        return parent::getPage('Home > DASHBOARD', $content);
    }

    public static function Member_Create($request) {
        $postVars = $request->getPostVars();

        $obj = new EntityMmeber();
        $obj->member_id = $postVars['member_id'];
        $obj->member_name = $postVars['member_name'];
        $obj->member_password = password_hash($postVars['member_password'], PASSWORD_DEFAULT);
        $obj->member_email = $postVars['member_email'];
        $obj->member_phone = $postVars['member_phone'];
        $obj->member_type = $postVars['member_type'];
        $obj->member_farm_idx = $postVars['member_farm_idx'];
        $obj->created();

        $request->getRouter()->redirect('/manager/login');
    }

}