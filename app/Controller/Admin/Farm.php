<?php

namespace App\Controller\Admin;

use \App\Model\Entity\Farm as EntityFarm;
use \App\Model\Entity\Member as EntityMmeber;
use \App\Model\Entity\FarmAddress as EntityFarmAddress;
use \app\Utils\Common;
use \WilliamCosta\DatabaseManager\Pagination;
use \App\Utils\View;

class Farm extends Page {

    private static function getManagerMemberList($member_idx = '') {
        $options = '';

        $results = EntityMmeber::getMembers("member_type='manager'", 'idx DESC', null);

        while ($obFarm = $results->fetchObject(EntityMmeber::class)) {
            $options .= View::render('admin/modules/farm/farm_form_options', [
                'value' => $obFarm->idx,
                'text'  => $obFarm->member_name,
                'selected' => $obFarm->idx == $member_idx ? 'selected' : '',
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

        $results = EntityFarm::getFarms(null, 'idx DESC', null);

        while ($obFarm = $results->fetchObject(EntityFarm::class)) {
            $items .= View::render('admin/modules/farm/farm_item', [
                'idx'           => $obFarm->idx,
                'farm_name'     => $obFarm->farm_name,
                'farm_ceo'     => $obFarm->farm_ceo,
                'farm_address'  => $obFarm->farm_address,
                'address'   => $obFarm->address,
            ]);
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
                'farm_ceo' => $objFarm->farm_ceo,
                'farm_address' => $objFarm->farm_address,
                'address' => $objFarm->address,
                'member_idx' => self::getManagerMemberList($objFarm->member_idx),
            ]);
        } else {
            $content = View::render('admin/modules/farm/farm_form', [
                'action' => '/admin/farm_form/create',
                'farm_name' => '',
                'farm_ceo' => '',
                'farm_address' => '',
                'address' => '',
                'member_idx' => self::getManagerMemberList(),
            ]);
        }

        return parent::getPanel('Home > DASHBOARD', $content, 'farm_mant');
    }


    public static function Farm_Create($request) {
        $postVars = $request->getPostVars();

        $obj = new EntityFarm;
        $obj->farm_name = Common::str_chekc($postVars['farm_name'], "농장이름을 입력 하세요.");
        $obj->farm_ceo = Common::str_chekc($postVars['farm_ceo'], "대표자 이름을 입력 하세요.");
        $obj->farm_address = $postVars['farm_address'];
        $obj->address = Common::int_check($postVars['address'], "농장고유주소을 입력 하세요.");
        $obj->member_idx = $postVars['member_idx'];

        $obj->created();

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


        $request->getRouter()->redirect('/admin/farm_list');
    }

    public static function Farm_Delete($request, $idx) {
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