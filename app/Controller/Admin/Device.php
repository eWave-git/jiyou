<?php

namespace App\Controller\Admin;

use \App\Model\Entity\Device as EntityDevice;
use \App\Model\Entity\Farm as EntityFarm;
use \App\Model\Entity\BoardTypeRef as EntityBoardTypeRef;

use \App\Model\Entity\Widget as EntityWidget;
use \App\Model\Entity\WidgetBoardType as EntityWidgetBoardType;

use App\Model\Entity\FarmAddress as EntityFarmAddress;

use \app\Utils\Common;
use \App\Utils\View;

class Device extends Page {

    private static function getFarmsByIdx($idx) {
        $obj = EntityFarm::getFarmsByIdx($idx);

        return $obj->farm_name ?? '';
    }

    private static function getBoardTypeList($board_idx = '') {
        $options = '';

        $results = EntityBoardTypeRef::getBoardTypeRef(null, 'idx ASC', null);

        while ($obBoardTypeRef = $results->fetchObject(EntityBoardTypeRef::class)) {
            $options .= View::render('admin/modules/device/device_form_options', [
                'value' => $obBoardTypeRef->board_type,
                'text'  => $obBoardTypeRef->board_type."  ".$obBoardTypeRef->model_name,
                'selected' => $obBoardTypeRef->board_type == $board_idx ? 'selected' : '',
            ]);
        }

        return $options;
    }

    private static function getAddressList($farm_idx = '', $address = '') {
        $options = '';

        $results = EntityFarm::getFarmsByIdx($farm_idx);

        $options = View::render('admin/modules/device/device_form_options', [
            'value' => $results->address,
            'text'  => $results->address,
            'selected' => $results->address == $address ? 'selected' : '',
        ]);


        return $options;
    }

    private static function getFarmList($farm_idx = '') {
        $options = '';

        $results = EntityFarm::getFarms(null, 'idx ASC', null);

        while ($obFarm = $results->fetchObject(EntityFarm::class)) {
            $options .= View::render('admin/modules/device/device_form_options', [
                'value' => $obFarm->idx,
                'text'  => $obFarm->farm_name,
                'selected' => $obFarm->idx == $farm_idx ? 'selected' : '',
            ]);
        }

        return $options;
    }

    private static function getDevieListItems($request) {
        $items = '';

        $results = EntityDevice::getDevicesJoinWidget();

        while ($obDevice = $results->fetchObject(EntityDevice::class)) {
            $items .= View::render('admin/modules/device/device_item', [
                'idx' => $obDevice->idx,
                'farm_name' => self::getFarmsByIdx($obDevice->farm_idx),
                'device_name' => $obDevice->device_name,
                'address' => $obDevice->address,
                'board_type' => $obDevice->board_type,
                'board_number' => $obDevice->board_number,
            ]);
        }

        return $items;
    }

    public static function Device_List($request) {
        $content = View::render('admin/modules/device/device_list', [
            'items' => self::getDevieListItems($request),
        ]);

        return parent::getPanel('Home > DASHBOARD', $content, 'device_mant');
    }

    public static function Device_Form($request, $idx = null) {
        $objDevice = is_null($idx) ? '' : EntityDevice::getDevicesByIdx($idx);

        if ($objDevice instanceof EntityDevice) {
            $content = View::render('admin/modules/device/device_form', [
                'action' =>  '/admin/device_form/'.$idx.'/edit',
                'farm_idx' => self::getFarmList($objDevice->farm_idx),
                'device_name'   => $objDevice->device_name,
                'address'   => self::getAddressList($objDevice->farm_idx, $objDevice->address),
                'board_type' => self::getBoardTypeList($objDevice->board_type),
                'board_number' => $objDevice->board_number,
            ]);
        } else {
            $content = View::render('admin/modules/device/device_form', [
                'action' => '/admin/device_form/create',
                'farm_idx' => self::getFarmList(),
                'device_name'   => '',
                'address'   => '',
                'board_type' => self::getBoardTypeList(),
                'board_number' => '',
            ]);
        }

        return parent::getPanel('Home > DASHBOARD', $content, 'device_mant');
    }

    public static function Device_Create($request) {
        $postVars = $request->getPostVars();;

        $obj = new EntityDevice();
        $obj->farm_idx = $postVars['farm_idx'];
        $obj->address = $postVars['address'];
        $obj->board_type = $postVars['board_type'];
        $obj->board_number = $postVars['board_number'];
        $device_idx = $obj->created();

        $farm_info = EntityFarm::getFarmsByIdx($postVars['farm_idx']);

        $widget_obj = new EntityWidget();
        $widget_obj->member_idx = $farm_info->member_idx;
        $widget_obj->widget_name = $postVars['device_name'];
        $widget_obj->device_idx = $device_idx;
        $widget_obj->address = $postVars['address'];
        $widget_obj->address = $postVars['address'];
        $widget_obj->board_type = $postVars['board_type'];
        $widget_obj->board_number = $postVars['board_number'];
        $widget_idx = $widget_obj->created();


        $board_type_obj = Common::getBoardTypeNameArray($postVars['board_type']);
        $widget_board_type_obj = new EntityWidgetBoardType();
        $widget_board_type_obj->widget_idx = $widget_idx;
        foreach ($board_type_obj as $k=>$v) {
            $name = $v['field']."_name";
            $display = $v['field']."_display";
            $symbol = $v['field']."_symbol";

            $widget_board_type_obj->{$name} = $v['name'];
            $widget_board_type_obj->{$display} = 'Y';
            $widget_board_type_obj->{$symbol} = Common::findSymbol($v['name'])['symbol'] ?? "";
        }
        $widget_board_type_obj->created();


        $request->getRouter()->redirect('/admin/device_list');
    }

    public static function Device_Edit($request, $idx) {
        $obj = EntityDevice::getDevicesByIdx($idx);

        $postVars = $request->getPostVars();

        $obj->farm_idx = $postVars['farm_idx'];
        $obj->address = $postVars['address'];
        $obj->board_type = $postVars['board_type'];
        $obj->board_number = $postVars['board_number'];
        $obj->updated();

        $request->getRouter()->redirect('/admin/device_list');
    }

    public static function Device_Delete($request, $idx) {

        $obj = EntityDevice::getDevicesByIdx($idx);
        $obj->deleted();

        $widget_obj = EntityWidget::getWidgetByDeviceIdx($idx)->fetchObject(EntityWidget::class);
        $widget_obj->deleted();


        $request->getRouter()->redirect('/admin/device_list');
    }

    public static function search_Addres($request) {
        $postVars = $request->getPostVars();

        if (empty($postVars['farm_idx'])) {
            throw new \Exception("fasdfs",400);
        }

        $results = EntityFarm::getFarmsByIdx($postVars['farm_idx']);
        $arr = array();


        if ($results) {
            $arr['idx'][] =  $results->idx;
            $arr['text'][] =  $results->address;
        }


        return [
            'success' => true,
            'idx'=>$arr['idx'],
            'address' => $arr['text'],
        ];
    }
}