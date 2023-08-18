<?php

namespace App\Controller\Admin;

use app\Utils\Common;
use \App\Utils\View;
use \App\Model\Entity\BoardTypeRef as EntityBoardTypeRef;

class BoardTypeRef extends Page {

    public static function getBoardTypeName($board_type) {
        $array = array();
        if ($board_type) {
            $objBoardTypeRef = EntityBoardTypeRef::getBoardTypeRefByBoardType($board_type);

            if ($objBoardTypeRef) {
                foreach($objBoardTypeRef as $column_name=>$column_value){
                    if (preg_match('/data/',$column_name, $match) && $column_value) {
                        $array[] = $column_value;
                    }
                }
            }
        }

        return $array;
    }

    public static function getBoardTypeNameArray($board_type) {
        $array = array();

        $objBoardTypeRef = EntityBoardTypeRef::getBoardTypeRefByBoardType($board_type);

        if ($objBoardTypeRef) {
            $i = 0;
            foreach($objBoardTypeRef as $column_name=>$column_value){
                if (preg_match('/data/',$column_name, $match) && $column_value) {
                    $array[$i]['field'] = $column_name;
                    $array[$i]['name'] = $column_value;

                    $i++;
                }
            }
        }

        return $array;
    }



    private static function getMemberListItems($request) {
        $items = '';

        $request = EntityBoardTypeRef::getBoardTypeRef(null, 'idx DESC', null);

        while ($obBoardTypeRef = $request->fetchObject(EntityBoardTypeRef::class)) {
            $items .= View::render('admin/modules/device/board_type_ref_item', [
                'idx'            => $obBoardTypeRef->idx,
                'board_type'      => $obBoardTypeRef->board_type,
                'model_name'    => $obBoardTypeRef->model_name,
                'maker'      => $obBoardTypeRef->maker,
                'use_count'    => $obBoardTypeRef->use_count,
            ]);
        }

        return $items;
    }

    public static function BoardTypeRef_List($request) {
        $content = View::render('admin/modules/device/board_type_ref_list', [
            'items' => self::getMemberListItems($request),
        ]);

        return parent::getPanel('Home > DASHBOARD', $content, 'device_mant');
    }

    public static function BoardTypeRef_Form($request, $idx = null) {
        $objBoardTypeRef = is_null($idx) ? '' : EntityBoardTypeRef::getBoardTypeRefByIdx($idx);

        if ($objBoardTypeRef instanceof EntityBoardTypeRef) {
            $content = View::render('admin/modules/device/board_type_ref_form', [
                'action'                    =>  '/admin/board_type_ref_form/'.$idx.'/edit',
                'board_type'         => $objBoardTypeRef->board_type,
                'model_name'         => $objBoardTypeRef->model_name,
                'maker'              => $objBoardTypeRef->maker,
                'use_count'          => $objBoardTypeRef->use_count,
                'data1'              => $objBoardTypeRef->data1,
                'data2'              => $objBoardTypeRef->data2,
                'data3'              => $objBoardTypeRef->data3,
                'data4'              => $objBoardTypeRef->data4,
                'data5'              => $objBoardTypeRef->data5,
                'data6'              => $objBoardTypeRef->data6,
                'data7'              => $objBoardTypeRef->data7,
                'data8'              => $objBoardTypeRef->data8,
            ]);
        } else {
            $content = View::render('admin/modules/device/board_type_ref_form', [
                'action'             => '/admin/board_type_ref_form/create',
                'board_type'         => '',
                'model_name'         => '',
                'maker'              => '',
                'use_count'          => '',
                'data1'              => '',
                'data2'              => '',
                'data3'              => '',
                'data4'              => '',
                'data5'              => '',
                'data6'              => '',
                'data7'              => '',
                'data8'              => '',
            ]);
        }

        return parent::getPanel('Home > DASHBOARD', $content, 'device_mant');
    }

    public static function BoardTypeRef_Create($request) {
        $postVars = $request->getPostVars();

        $obj = new EntityBoardTypeRef();
        $obj->board_type = $postVars['board_type'];
        $obj->model_name = $postVars['model_name'];
        $obj->maker = $postVars['maker'];
        $obj->use_count = $postVars['use_count'];
        $obj->data1 = $postVars['data1'];
        $obj->data2 = $postVars['data2'];
        $obj->data3 = $postVars['data3'];
        $obj->data4 = $postVars['data4'];
        $obj->data5 = $postVars['data5'];
        $obj->data6 = $postVars['data6'];
        $obj->data7 = $postVars['data7'];
        $obj->data8 = $postVars['data8'];
        $obj->created();

        $request->getRouter()->redirect('/admin/board_type_ref_list');
    }

    public static function BoardTypeRef_Edit($request, $idx) {
        $obj = EntityBoardTypeRef::getBoardTypeRefByIdx($idx);

        $postVars = $request->getPostVars();

        $obj->board_type = $postVars['board_type'] ?? $obj->board_type;
        $obj->model_name = $postVars['model_name'] ?? $obj->model_name;
        $obj->maker = $postVars['maker'] ?? $obj->maker;
        $obj->use_count = $postVars['use_count'] ?? $obj->use_count;
        $obj->data1 = $postVars['data1'] ?? $obj->data1;
        $obj->data2 = $postVars['data2'] ?? $obj->data2;
        $obj->data3 = $postVars['data3'] ?? $obj->data3;
        $obj->data4 = $postVars['data4'] ?? $obj->data4;
        $obj->data5 = $postVars['data5'] ?? $obj->data5;
        $obj->data6 = $postVars['data6'] ?? $obj->data6;
        $obj->data7 = $postVars['data7'] ?? $obj->data7;
        $obj->data8 = $postVars['data8'] ?? $obj->data8;

        $obj->updated();

        $request->getRouter()->redirect('/admin/board_type_ref_list');
    }

    public static function BoardTypeRef_Delete($request, $idx) {
        $obj = EntityBoardTypeRef::getBoardTypeRefByIdx($idx);

        $obj->deleted();

        $request->getRouter()->redirect('/admin/board_type_ref_list');
    }

}