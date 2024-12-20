<?php

namespace App\Controller\Admin;

use \App\Model\Entity\Farm as EntityFarm;
use \App\Model\Entity\Member as EntityMmeber;
use \App\Model\Entity\FarmAddress as EntityFarmAddress;
use \App\Model\Entity\Device as EntityDevice;

use \app\Utils\Common;
use \WilliamCosta\DatabaseManager\Pagination;
use \App\Utils\View;

class Farm extends Page {

    private static function getManagerMemberList($member_idx = '') {
        $options = '';

        if ($member_idx) {
            $results = EntityMmeber::getMembers('idx ='.$member_idx,'','','*');
        } else {
            $results = EntityMmeber::getMemberJoinNotFarm();
        }

        while ($obFarm = $results->fetchObject(EntityMmeber::class)) {
            $options .= View::render('admin/modules/farm/farm_form_options', [
                'value' => $obFarm->idx,
                'text'  => $obFarm->member_name,
                'selected' => $obFarm->idx == $member_idx ? 'selected' : '',
            ]);
        }
        return $options;
    }

    private static function getViewerMemberList($member_idx = '') {
        $options = '';

        $results = EntityMmeber::getMembers("member_type='viewer'", 'idx DESC', '','*');
        while ($obFarm = $results->fetchObject(EntityMmeber::class)) {
            $options .= View::render('admin/modules/farm/farm_form_viewer_options', [
                'value' => $obFarm->idx,
                'text'  => $obFarm->member_name,
                'selected' => $obFarm->member_group == $member_idx ? 'selected' : '',
            ]);
        }

        return $options;
    }

    private static function getFarmListItems($request) {
        $items = '';

//        $datetotal = EntityFarm::getFarms(null, null, null, 'COUNT(*) as cnt')->fetchObject()->cnt;
//        $queryParams = $request->getQueryParams();
//        $paging = $queryParams['page'] ?? 1;
//        $obpagin = new Pagination($datetotal, $paging, 10 );
//        $results = EntityFarm::getFarms(null, 'idx DESC', $obpagin->getLimit());

        $results = EntityFarm::getFarms('', 'idx DESC', '','*');
        $cnt = EntityFarm::getFarms('', '', '', 'COUNT(*) as cnt')->fetchObject()->cnt;

        while ($obFarm = $results->fetchObject(EntityFarm::class)) {
            $member_info = EntityMmeber::getMemberByIdx($obFarm->member_idx);
            $viewer_info = EntityMmeber::getMemberByGroup($obFarm->member_idx);
            $items .= View::render('admin/modules/farm/farm_item', [
                'num'           => $cnt,
                'idx'           => $obFarm->idx,
                'farm_name'     => $obFarm->farm_name,
                'manager_name'     => $member_info->member_name ?? '',
                'viewer_name'  => '',
                'address'   => $obFarm->address,
            ]);
            $cnt--;
        }

        return $items;
    }

    public static function Farm_List($request) {
        $content = View::render('admin/modules/farm/farm_list', [
            'items' => self::getFarmListItems($request),
        ]);

        return parent::getPanel('Home > DASHBOARD', $content, 'farm_mant');
    }

    public static function Farm_Form($request, $idx = null) {

        $objFarm = is_null($idx) ? '': EntityFarm::getFarmsByIdx($idx) ;

        if ($objFarm instanceof EntityFarm) {

            $content = View::render('admin/modules/farm/farm_form', [
                'action' => '/admin/farm_form/'.$idx.'/edit',
                'farm_name' => $objFarm->farm_name,
                'farm_address' => $objFarm->farm_address,
                'address' => $objFarm->address,
                'member_idx' => self::getManagerMemberList($objFarm->member_idx),
                'viewer_member_idx' => self::getViewerMemberList($objFarm->member_idx),
            ]);
        } else {
            $content = View::render('admin/modules/farm/farm_form', [
                'action' => '/admin/farm_form/create',
                'farm_name' => '',
                'farm_address' => '',
                'address' => '',
                'member_idx' => self::getManagerMemberList(),
                'viewer_member_idx' => self::getViewerMemberList(),
            ]);
        }

        return parent::getPanel('Home > DASHBOARD', $content, 'farm_mant');
    }


    public static function Farm_Create($request) {
        $postVars = $request->getPostVars();

        $obj = EntityFarm::getAddressCnt($postVars['address']);

        if ($obj > 0) {
            Common::error_msg("등록된 safe1 식별번호 입니다.");
        }

        $obj = new EntityFarm;
        $obj->farm_name = Common::str_chekc($postVars['farm_name'], "농장이름을 입력 하세요.");
        $obj->farm_address = $postVars['farm_address'];
        $obj->address = Common::int_check($postVars['address'], "농장고유주소을 입력 하세요.");
        $obj->member_idx = $postVars['member_idx'];

        $obj->created();

        $member_obj = EntityMmeber::getMemberByIdx($postVars['member_idx']);
        $member_obj->member_group = $postVars['member_idx'];
        $member_obj->updated();

        if (isset($postVars['viewer_member_idx'])) {
            foreach ($postVars['viewer_member_idx'] as $idx) {
                $member_obj = EntityMmeber::getMemberByIdx($idx);
                $member_obj->member_group = $postVars['member_idx'];
                $member_obj->updated();
            }
        }

        $request->getRouter()->redirect('/admin/farm_list');
    }

    public static function Farm_Edit($request, $idx) {
        $obj = EntityFarm::getFarmsByIdx($idx);

        $postVars = $request->getPostVars();

        $obj->farm_name = $postVars['farm_name'] ?? $obj->farm_name;
        $obj->farm_ceo = $postVars['farm_ceo'] ?? $obj->farm_ceo;
        $obj->farm_address = $postVars['farm_address'] ?? $obj->farm_name;
        $obj->address = $postVars['address'] ?? $obj->address;
        $obj->member_idx = $postVars['member_idx'] ?? $obj->member_idx;
        $obj->updated();

        EntityMmeber::UpdateMemberGroupReset($postVars['member_idx']);

        $member_obj = EntityMmeber::getMemberByIdx($postVars['member_idx']);
        $member_obj->member_group = $postVars['member_idx'];
        $member_obj->updated();

        if (isset($postVars['viewer_member_idx'])) {
            foreach ($postVars['viewer_member_idx'] as $idx) {
                $member_obj = EntityMmeber::getMemberByIdx($idx);
                $member_obj->member_group = $postVars['member_idx'];
                $member_obj->updated();
            }
        }

        $request->getRouter()->redirect('/admin/farm_list');
    }

    public static function Farm_Delete($request, $idx) {

        $cnt = EntityDevice::getDeviceByFarmIdxCnt($idx)->fetchObject()->cnt;;
        if ($cnt > 0) {
            Common::error_msg("디바이스를 모두 삭제 후 농장을 석제 할 수 있습니다.");
        }

        $obj = EntityFarm::getFarmsByIdx($idx);

        $obj->deleted();

        $request->getRouter()->redirect('/admin/farm_list');
    }

    public static function Farm_Address_Add($request) {
        $postVars = $request->getPostVars();
        if (empty($postVars['farm_idx'])) {
            throw new \Exception("fasdfs",400);
        }
        if (empty($postVars['address'])) {
            throw new \Exception("fasdfs",400);
        }

        $obj = EntityFarmAddress::getAddressCnt($postVars['address']);

        if ($obj > 0) {
            throw new \Exception("fasdfs",400);
        }

        $obj = new EntityFarmAddress;
        $obj->farm_idx = $postVars['farm_idx'];
        $obj->address = $postVars['address'];
        $obj->save();

        return ['success' => true];
    }
}