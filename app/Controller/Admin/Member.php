<?php

namespace App\Controller\Admin;

use \App\Model\Entity\Farm as EntityFarm;
use \App\Model\Entity\Member as EntityMmeber;
use app\Utils\Common;
use \App\Utils\View;

class Member extends Page {

    private static function getFarmsByIdx($idx) {
        $obj = EntityFarm::getFarmsByIdx($idx);

        return $obj->farm_name ?? '';
    }

    private static function getMemberType($type = '') {
        $_type = array( 'manager','viewer');
        $options = '';

        foreach ($_type as $item) {
            $options .= View::render('admin/modules/member/member_form_options', [
                'value' => $item,
                'text'  => $item,
                'selected' => $item == $type ? 'selected' : '',
            ]);
        }

        return $options;
    }

    private static function getFarmList($farm_idx = '') {
        $options = '';

        $results = EntityFarm::getFarms(null, 'idx DESC', null);

        while ($obFarm = $results->fetchObject(EntityFarm::class)) {
            $options .= View::render('admin/modules/member/member_form_options', [
                'value' => $obFarm->idx,
                'text'  => $obFarm->farm_name,
                'selected' => $obFarm->idx == $farm_idx ? 'selected' : '',
            ]);
        }

        return $options;
    }

    private static function getMemberListItems($request) {
        $items = '';

        $request = EntityMmeber::getMembers(null, 'idx DESC', null);

        while ($obMember = $request->fetchObject(EntityMmeber::class)) {
            $items .= View::render('admin/modules/member/member_item', [
               'idx'            => $obMember->idx,
               'member_type'    => $obMember->member_type,
               'member_id'      => $obMember->member_id,
               'member_name'    => $obMember->member_name,
               'member_email'   => $obMember->member_email,
               'member_phone'   => $obMember->member_phone,
            ]);
        }

        return $items;
    }

    public static function Member_List($request) {
        $content = View::render('admin/modules/member/member_list', [
            'items' => self::getMemberListItems($request),
        ]);

        return parent::getPanel('Home > DASHBOARD', $content, 'member_mant');
    }

    public static function Member_Form($request, $idx = null) {
        $objMember = is_null($idx) ? '' : EntityMmeber::getMemberByIdx($idx);

        if ($objMember instanceof EntityMmeber) {
            $content = View::render('admin/modules/member/member_form', [
                'action'                    =>  '/admin/member_form/'.$idx.'/edit',
                'member_id'                 => $objMember->member_id,
                'member_name'               => $objMember->member_name,
                'member_password'           => '',
                'member_email'              => $objMember->member_email,
                'member_phone'              => $objMember->member_phone,
                'member_type_options'       => self::getMemberType($objMember->member_type),
            ]);
        } else {
            $content = View::render('admin/modules/member/member_form', [
                'action'                    => '/admin/member_form/create',
                'member_id'                 => '',
                'member_name'               => '',
                'member_password'           => '',
                'member_email'              => '',
                'member_phone'              => '',
                'member_type_options'       => self::getMemberType(),
            ]);
        }

        return parent::getPanel('Home > DASHBOARD', $content, 'member_mant');
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
        $obj->created();

        $request->getRouter()->redirect('/admin/member_list');
    }

    public static function Member_Edit($request, $idx) {
        $obj = EntityMmeber::getMemberByIdx($idx);

        $postVars = $request->getPostVars();

        $obj->member_id = $postVars['member_id'] ?? $obj->member_id;
        $obj->member_name = $postVars['member_name'] ?? $obj->member_name;
        $obj->member_password = $postVars['member_password'] ? password_hash($postVars['member_password'], PASSWORD_DEFAULT) : $obj->member_password;
        $obj->member_email = $postVars['member_email'] ?? $obj->member_email;
        $obj->member_phone = $postVars['member_phone'] ?? $obj->member_phone;
        $obj->member_type = $postVars['member_type'] ?? $obj->member_type;
        $obj->updated();


        $request->getRouter()->redirect('/admin/member_list');
    }

    public static function Member_Delete($request, $idx) {
        $obj = EntityMmeber::getMemberByIdx($idx);

        $obj->deleted();

        $request->getRouter()->redirect('/admin/member_list');
    }

    public static function id_Check($request) {
        $postVars = $request->getPostVars();
        $obj = EntityMmeber::getMemberById($postVars['id']);

        if ($obj) {
            return [
                'success' => true,
            ];
        } else {
            return [
                'success' => false,
            ];
        }
    }
}