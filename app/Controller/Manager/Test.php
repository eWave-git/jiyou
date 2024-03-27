<?php

namespace App\Controller\Manager;

use App\Controller\Manager\Page;
use App\Model\Entity\BoardTypeSymbol;
use App\Model\Entity\Member as EntityMmeber;
use App\Model\Entity\Widget as EntityWidget;
use App\Model\Entity\WidgetBoardType as EntityWidgetBoardType;
use app\Utils\Common;
use app\Utils\View;

class Test extends Page {


    public static function index($request) {
        return View::render('manager/modules/test/index', [

        ]);
    }

    public static function index_back($request) {

        $postVars = $request->getPostVars();
        $data = $postVars['data'];
        $data = $data + 1;

        return [
            'success' => true,
            'data' => $data,
        ];
    }

}