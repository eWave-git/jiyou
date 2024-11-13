<?php

namespace App\Controller\Manager;

use App\Model\Entity\Member as EntityMmeber;
use App\Model\Entity\Report as EntityReport;
use app\Utils\Common;
use \App\Utils\View;

class Report extends Page {
    public static function Report_Form($request) {
        $content = View::render('manager/modules/report/report_form', []);

        return parent::getPanel('Home > DASHBOARD', $content, 'etc');
    }

    public static function Report_Create($request) {
        $postVars = $request->getPostVars();

        $_user = Common::get_manager();
        $_userInfo = EntityMmeber::getMemberById($_user);

        $obj = new EntityReport;
        $obj->member_idx = $_userInfo->idx;
        $obj->report_title = $postVars['report_title'];
//        $obj->report_sender = $postVars['report_sender'];
        $obj->report_sender = $_userInfo->member_name;
        $obj->report_recipient = $postVars['report_recipient'];
        $obj->report_attachment = $postVars['report_attachment'];
        $obj->report_content = $postVars['report_content'];
        $obj->created();

        $request->getRouter()->redirect('/manager/etc/report_form');
    }
}