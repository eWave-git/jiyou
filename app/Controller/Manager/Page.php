<?php

namespace App\Controller\Manager;

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

        return View::render('manager/page', [
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
                ['label' => 'alarm_list', 'title' => '알람 설정', 'link' => URL.'/manager/alarm_list'],
                ['label' => 'alarm_log_list', 'title' => '알람 기록', 'link' => URL.'/manager/alarm_log_list'],
            ],
        ],
        [
            'label' => 'control',
            'title' => '제어',
            'submenu'=>[
                ['label' => 'switch', 'title' => '스위치 제어', 'link' => URL.'/manager/control/switch'],
                ['label' => 'command', 'title' => '명령어 제어', 'link' => URL.'/manager/control/command'],
                ['label' => 'command_4ch', 'title' => '명령어2 제어', 'link' => URL.'/manager/control/command_4ch'],
                ['label' => 'control', 'title' => '인버터 제어(예정)', 'link' =>"javascript:alert('준비중')"],
            ],
        ],
        [
            'label' => 'etc',
            'title' => '기타',
            'submenu'=>[
                
                ['label' => 'group', 'title' => '그룹 관리', 'link' => '/manager/etc/group'],
                ['label' => 'autovalve', 'title' => '자동밸브제어', 'link' => '/manager/etc/autovalve'],
                ['label' => 'etc', 'title' => '알람 수신변경(예정)', 'link' => "javascript:alert('준비중')"],
//                ['label' => 'report_form', 'title' => '레포팅', 'link' => "/manager/etc/report_form"],
                ['label' => 'etc', 'title' => '데이터 분석(예정)', 'link' => "javascript:alert('준비중')"],
            ],
        ],
    ];


    public static function getDepth_1($currentModule) {

        $menus = '';

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