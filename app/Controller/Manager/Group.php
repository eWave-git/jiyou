<?php

namespace App\Controller\Manager;

use App\Model\Entity\Member as EntityMmeber;
use App\Model\Entity\Group as EntityGroup;
use app\Utils\Common;
use \App\Utils\View;

class Group extends Page {

    public static function getGroupList($user_idx) {

        $array = array();
        $k=0;
        $group_member = EntityGroup::getGroupByMemberIdx($user_idx);
        while ($obj = $group_member->fetchObject(EntityGroup::class)) {
            $array[$k]['idx'] = $obj->idx;
            $array[$k]['name'] = $obj->group_name;
            $array[$k]['start_date'] = substr($obj->start_date,0,11);
            $array[$k]['end_date'] = substr($obj->end_date,0,11);
            $array[$k]['created_at'] = $obj->created_at;
            $k++;
        }

        $item = '';
        $total = Count($array);
        foreach ($array as $k => $v) {
            $item .= View::render('manager/modules/group/group_list_item', [
                'idx' => $v['idx'],
                'number' => $total,
                'name' => $v['name'],
                'start_date' => $v['start_date'],
                'end_date' => $v['end_date'],
                'created_at' => $v['created_at'],
            ]);

            $total--;
        }

        return $item;
    }

    public static function getGroup($request) {
        $_user = Common::get_manager();
        $_userInfo = EntityMmeber::getMemberById($_user);

        $content = View::render('manager/modules/group/group_list', [
            'group_list_item' => self::getGroupList($_userInfo->idx),
        ]);

        return parent::getPanel('Home > DASHBOARD', $content, 'etc');
    }

    public static function Group_Form($request) {
        $content = View::render('manager/modules/group/group_form', []);

        return parent::getPanel('Home > DASHBOARD', $content, 'etc');
    }

    public static function Group_Create($request) {
        $postVars = $request->getPostVars();

        $_user = Common::get_manager();
        $_userInfo = EntityMmeber::getMemberById($_user);
        list ($start_date, $end_date) = explode(' - ',  $postVars['group_date']);

        $obj = new EntityGroup;
        $obj->member_idx = $_userInfo->idx;
        $obj->group_name = $postVars['group_name'];
        $obj->start_date = $start_date;
        $obj->end_date = $end_date;
        $obj->created();

        $request->getRouter()->redirect('/manager/etc/group');
    }


    public static function Group_Delete($request, $idx) {

        $obj = EntityGroup::getGroupByIdx($idx);
        $obj->deleted();
        $request->getRouter()->redirect('/manager/etc/group');
    }
}