<?php

namespace App\Controller\Manager;

use App\Utils\Common;
use App\Utils\View;

class Page {


    public static function getPage($title, $content) {

        $REQUEST_URI = explode('?',$_SERVER['REQUEST_URI'])[0];

        if (file_exists("resources/dynamic/".$REQUEST_URI.".js")) {
            $javascript_file = "<script src='".URL."/resources/dynamic".$REQUEST_URI.".js' defer></script>";
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
            'link'  => URL."/manager/dashboard",
        ],
        [
            'label' => 'inquiry',
            'title' => '조회',
            'link'  => URL."/manager/inquiry",
        ],
        [
            'label' => 'management',
            'title' => '알람',
            'link'  => URL."/manager/managment",
        ],
        [
            'label' => 'setting',
            'title' => '제어',
            'link'  => URL."/manager/setting",
        ],
        [
            'label' => 'all',
            'title' => '기타',
            'link'  => "javascript:alert('준비중')",
        ],
    ];


    public static function getDepth_1($currentModule) {

        $menus = '';

        foreach (self::$menus as $k => $v) {
            if (!array_key_exists('submenu', $v)) {
                $menus .= View::render('manager/menu/li', [
                    'depth_1' => $v['title'],
                    'active' => $v['label'] == $currentModule ? 'active' : '',
                    'link'    => $v['link'],
                ]);
            } else {
                $menus .= View::render('manager/menu/li_dropdown', [
                    'depth_1' => $v['title'],
                    'active' => $v['label'] == $currentModule ? 'active' : '',
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


        foreach ($sub_menu['submenu'] as $k => $v) {
            $dropdown .= View::render('manager/menu/dropdown', [
                'depth_2' => $v['title'],
                'link'    => $v['link'],
            ]);
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