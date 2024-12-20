<?php

namespace App\Controller\Manager;

use App\Model\Entity\AlarmControl as EntityAlarmControl;
use App\Model\Entity\Member as EntityMmeber;
use App\Utils\Common;
use App\Utils\View;

class Page {


    public static function getPage($title, $content) {

        $REQUEST_URI = explode('?',$_SERVER['REQUEST_URI'])[0];
        $FILE_NAME = (explode('/',$_SERVER['REQUEST_URI']))[2];

        if (file_exists("resources/dynamic/manager/".$FILE_NAME.".js")) {
            $javascript_file = "<script src='".URL."/resources/dynamic/manager/".$FILE_NAME.".js?".date('U')."' defer></script>";
        } else {
            $javascript_file = "";
        }

        $title = getenv('DB_NAME');

        $alarmtoast = "";

        $_user = Common::get_manager();
        if ($_user) {
            $_userInfo = EntityMmeber::getMemberById($_user);
            $results_activation = Common::getAlarmcontrolActivation($_userInfo->member_group);

            if ($results_activation == 'Y') {
                $alarmtoast = "<script src='".URL."/resources/dynamic/manager/alarm_toast.js?".date('U')."' defer></script>";
            }
        }

        return View::render('manager/page', [
            'title' => $title,
            'content' => $content,
            'javascript' => $javascript_file,
            'alarmtoast' => $alarmtoast,
        ]);
    }

    public static function getBlankPage($title, $content) {

        $REQUEST_URI = explode('?',$_SERVER['REQUEST_URI'])[0];
        $FILE_NAME = (explode('/',$_SERVER['REQUEST_URI']))[2];

        if (file_exists("resources/dynamic/manager/".$FILE_NAME.".js")) {
            $javascript_file = "<script src='".URL."/resources/dynamic/manager/".$FILE_NAME.".js?".date('U')."' defer></script>";
        } else {
            $javascript_file = "";
        }

        $title = getenv('DB_NAME');

        return View::render('blank/page', [
            'title' => $title,
            'content' => $content,
            'javascript' => $javascript_file,
        ]);
    }
    private static $menus = [
        [
            'label' => 'dashboard',
            'title' => '홈',
            'submenu'=>[
                ['label' => 'dashboard', 'title' => '처음으로', 'link' => URL.'/'],
            ],
        ],
        [
            'label' => 'inquiry',
            'title' => '조회',
            'submenu'=>[
                ['label' => 'table_inquiry', 'title' => '데이터 조회', 'link' => URL.'/manager/table_inquiry'],
                ['label' => 'chart_inquiry', 'title' => '그래프 조회', 'link' => URL.'/manager/chart_inquiry'],
            ],
        ],
        [
            'label' => 'alarm',
            'title' => '알람',
            'submenu'=>[
                ['label' => 'group_alarm_list', 'title' => '그룹 환경 알람 설정', 'link' => URL.'/manager/group_alarm_list'],
                ['label' => 'alarm_list', 'title' => '환경 알람 설정', 'link' => URL.'/manager/alarm_list'],
                ['label' => 'alarm_log_list', 'title' => '환경 알람 기록', 'link' => URL.'/manager/alarm_log_list'],
                ['label' => 'water_alarm_list', 'title' => '음수 알람 설정', 'link' => URL.'/manager/water_alarm_list'],
                ['label' => 'water_alarm_log_list', 'title' => '음수 알람 기록', 'link' => URL.'/manager/water_alarm_log_list'],
            ],
        ],
        [
            'label' => 'control',
            'title' => '제어',
            'submenu'=>[
                ['label' => 'switch', 'title' => '밸브 제어', 'link' => URL.'/manager/control/switch'],
                ['label' => 'command_4ch', 'title' => '스위치 제어', 'link' => URL.'/manager/control/command_4ch'],
                ['label' => 'command', 'title' => '온도 제어', 'link' => URL.'/manager/control/command'],
                // ['label' => 'control', 'title' => '인버터 제어(예정)', 'link' =>"javascript:alert('준비중')"],
            ],
        ],
        [
            'label' => 'etc',
            'title' => '기타',
            'submenu'=>[

//                ['label' => 'group', 'title' => '그룹 관리', 'link' => '/manager/etc/group'],
//                ['label' => 'group', 'title' => '그래픽 보기', 'link' => 'javascript:window.open(\'/manager/etc/graphic_view\', \'_blank\', \'location=no,menubar=no,toolbar=no,status=no,fullscreen=yes\')'],
                ['label' => 'group', 'title' => '알람일시중단', 'link' => '/manager/etc/alarmcontrol'],
                // ['label' => 'autovalve', 'title' => '자동밸브제어', 'link' => '/manager/etc/autovalve'],
                // ['label' => 'etc', 'title' => '알람 수신변경(예정)', 'link' => "javascript:alert('준비중')"],
                // ['label' => 'report_form', 'title' => '레포팅', 'link' => "/manager/etc/report_form"],
                // ['label' => 'etc', 'title' => '데이터 분석(예정)', 'link' => "javascript:alert('준비중')"],
            ],
        ],
    ];

    private static $viewer_menus = [
        [
            'label' => 'dashboard',
            'title' => '홈',
            'submenu'=>[
                ['label' => 'dashboard', 'title' => '처음으로', 'link' => URL.'/'],
            ],
        ],
    ];

    public static function getDepth_1($currentModule) {

        $menus = '';

        if ($_SESSION['manager']['user']['type'] == 'manager') {
            foreach (self::$menus as $k => $v) {
                if (!array_key_exists('submenu', $v)) {
                    $menus .= View::render('manager/menu/li', [
                        'depth_1' => $v['title'],
                        'active' => $v['label'] == $currentModule ? 'on' : '',
                        'link'    => $v['link'],
                    ]);
                } else {
                    $menus .= View::render('manager/menu/li_dropdown', [
                        'depth_1' => $v['title'],
                        'active' => $v['label'] == $currentModule ? 'on' : '',
                        'dropdown' => self::getDepth_2($v),
                    ]);
                }
            }
        } else if ($_SESSION['manager']['user']['type'] == 'viewer') {
            foreach (self::$viewer_menus as $k => $v) {
                if (!array_key_exists('submenu', $v)) {
                    $menus .= View::render('manager/menu/li', [
                        'depth_1' => $v['title'],
                        'active' => $v['label'] == $currentModule ? 'on' : '',
                        'link'    => $v['link'],
                    ]);
                } else {
                    $menus .= View::render('manager/menu/li_dropdown', [
                        'depth_1' => $v['title'],
                        'active' => $v['label'] == $currentModule ? 'on' : '',
                        'dropdown' => self::getDepth_2($v),
                    ]);
                }
            }
        }


        return View::render('manager/menu/navbar', [
            'menus' => $menus
        ]);
    }

    public static function getDepth_2($sub_menu) {
        $dropdown = '';
        $_temp =explode('/',$_SERVER['REQUEST_URI']);
        $FILE_NAME = end ($_temp);

        foreach ($sub_menu['submenu'] as $k => $v) {

            if ($v['label'] == $FILE_NAME) {
                $dropdown .= View::render('manager/menu/dropdown', [
                    'depth_2' => $v['title'],
                    'link'    => $v['link'],
                    'active' => 'on',
                ]);
            } else {
                $dropdown .= View::render('manager/menu/dropdown', [
                    'depth_2' => $v['title'],
                    'link'    => $v['link'],
                    'active' => '',
                ]);
            }

        }

        return $dropdown;
    }

    public static function getPanel($title, $content, $currentModule) {
        $contentPanel = View::render('manager/panel', [
            'menu' => self::getDepth_1($currentModule),
            'content' => $content
        ]);

        return self::getPage($title, $contentPanel);
    }

    public static function getBlankPanel($title, $content, $currentModule) {
        $contentPanel = View::render('blank/panel', [
            'content' => $content
        ]);

        return self::getBlankPage($title, $contentPanel);
    }

    public static function getPagination($request, $obPagination) {
        $pages = $obPagination->getPages();

        if (count($pages) <=1 ) return '';

        $links = '';

        $url = $request->getRouter()->getCurrentUrl();
        $queryParams = $request->getQueryParams();

        foreach ($pages as $page) {
            $queryParams['page'] = $page['page'];

            $link = $url.'?'.http_build_query($queryParams);

            $links .= View::render('manager/pagination/link', [
                'page' => $page['page'],
                'link' => $link,
                'active' => $page['current'] ? 'active' : ''
            ]);
        }

        return View::render('manager/pagination/box', [
            'links' => $links
        ]);
    }

}