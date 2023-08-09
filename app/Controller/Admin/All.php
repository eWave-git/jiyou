<?php

namespace App\Controller\Admin;

use \App\Model\Entity\BoardTypeRef as EntityBoardTypeRef;
use \App\Model\Entity\Member as EntityMmeber;
use \App\Model\Entity\Device as EntityDevice;
use \app\Utils\Common;
use \App\Utils\View;

class All extends Page {

    private static function getMemberDevices($farm_idx, $address) {
        $text = '';

        $results = EntityDevice::getDevicesByIdxAddress($farm_idx, $address);

        while ($obFarm = $results->fetchObject(EntityDevice::class)) {
            $text .= $obFarm->address . "-" . $obFarm->board_type . "-" . $obFarm->board_number ."<br>";
        }

        return $text;
    }

    private static function getMemberDetail($request) {
        $items = '';

        $results = EntityMmeber::getMemberDetailList();

        while ($obFarm = $results->fetchObject(EntityMmeber::class)) {

            $items .= View::render('admin/modules/all/all_item', [
                'idx'       => $obFarm->idx,
                'member_id'       => $obFarm->member_id,
                'member_name'       => $obFarm->member_name,
                'farm_name'       => $obFarm->farm_name,
                'member_devices'  => self::getMemberDevices($obFarm->farm_idx, $obFarm->address),
                'farm_idx'        => $obFarm->farm_idx,
            ]);
        }

        return $items;
    }

    public static function all_list($request) {
        $content = View::render('admin/modules/all/all_list', [
            'items' => self::getMemberDetail($request),
        ]);

        return parent::getPanel('Home > DASHBOARD', $content, 'all_mant');
    }


    private static function getBoardType($board_type) {
        $__idx = 1;
        $datas = "";

        $objBoardTypeRef = EntityBoardTypeRef::getBoardTypeRefByBoardType($board_type);

        foreach($objBoardTypeRef as $column_name=>$column_value){
            if (preg_match('/data/',$column_name, $match) && $column_value) {
                $datas .=  View::render('admin/modules/all/all_detail_accordion_boardtype', [
                    '__idx'  => $__idx,
                    'field' => $column_name,
                    'text'  => $column_value,
                ]);
                $__idx++;
            }

        }

        return $datas;
    }


    private static function getDetailDevice($farm_idx, $address) {
        $items = "";

        $results = EntityDevice::getDevicesByIdxAddress($farm_idx, $address);

        $_idx = 1;
        while ($obFarm = $results->fetchObject(EntityMmeber::class)) {
            $items .= View::render('admin/modules/all/all_detail_accordion', [
                '_idx'  => $_idx,
                'accordion_title' => "장비 :" . $obFarm->address . "-" . $obFarm->board_type . "-" . $obFarm->board_number,
                'device_list' => self::getBoardType($obFarm->board_type),
            ]);
            $_idx++;
        }

        return $items;
    }

    public static function all_detail($request, $farm_idx) {
        $objDetail = EntityMmeber::getMemberDetail($farm_idx)->fetchObject(EntityMmeber::class);

        $content = View::render('admin/modules/all/all_detail', [
            'member_id' => $objDetail->member_id,
            'member_name' => $objDetail->member_name,
            'member_email' => $objDetail->member_email,
            'member_phone' => $objDetail->member_phone,
            'member_created_at' => $objDetail->member_created_at,
            'farm_name' => $objDetail->farm_name,
            'farm_ceo' => $objDetail->farm_ceo,
            'farm_address' => $objDetail->farm_address,
            'address' => $objDetail->address,
            'all_detail_device' => self::getDetailDevice($objDetail->farm_idx, $objDetail->address),
        ]);



        return parent::getPanel('Home > DASHBOARD', $content, 'all_mant');
    }
}