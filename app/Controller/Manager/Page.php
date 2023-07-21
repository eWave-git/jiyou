<?php

namespace App\Controller\Manager;

use App\Utils\Common;
use App\Utils\View;

class Page {


    public static function getPage($title, $content) {
        return View::render('manager/page', [
            'title' => $title,
            'content' => $content
        ]);
    }

    private static $menus = [
        [
            'label' => 'dashboard',
            'title' => '대쉬보드',
        ],
        ['label' => 'data_chk',
            'title' => '데이터조회',
            'submenu'=>[
                ['label' => 'data_chk', 'title' => 'Horizontal', 'link' => ''],
                ['label' => 'data_chk', 'title' => 'Boxed', 'link' => ''],
            ],
        ],
        ['label' => 'data_mnt',
            'title' => '데이터관리',
            'submenu'=>[
                ['label' => 'data_mnt', 'title' => '회원1', 'link' => ''],
                ['label' => 'data_mnt', 'title' => '회원2', 'link' => ''],
            ],
        ],
    ];


    public static function getDepth_1($currentModule) {

        $menus = '';

        foreach (self::$menus as $k => $v) {
            if (!array_key_exists('submenu', $v)) {
                $menus .= View::render('manager/menu/li', [
                    'depth_1' => $v['title'],
                    'active' => $v['label'] == $currentModule ? 'active' : '',
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