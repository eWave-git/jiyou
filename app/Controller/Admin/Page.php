<?php

namespace App\Controller\Admin;

use App\Utils\Common;
use App\Utils\View;

class Page {

    public static function getPage($title, $content) {
        return View::render('admin/page', [
            'title' => $title,
            'content' => $content
        ]);
    }

    private static $menus = [
        [
            'label' => 'farm_mant',
            'title' => '농장관리',
            'link'  => URL."/admin/farm_list"
        ],
        [
            'label' => 'data_chk',
            'title' => '회원관리',
            'link'  => URL."/admin/member_list"
//            'submenu'=>[
//                ['label' => 'data_chk', 'title' => 'Horizontal', 'link' => ''],
//                ['label' => 'data_chk', 'title' => 'Boxed', 'link' => ''],
//            ],
        ],
        [
            'label' => 'data_mnt',
            'title' => '장비관리',
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
                $menus .= View::render('admin/menu/li', [
                    'depth_1' => $v['title'],
                    'active'  => $v['label'] == $currentModule ? 'active' : '',
                    'link'    => $v['link'],
                ]);
            } else {
                $menus .= View::render('admin/menu/li_dropdown', [
                    'depth_1' => $v['title'],
                    'active' => $v['label'] == $currentModule ? 'active' : '',
                    'dropdown' => self::getDepth_2($v),
                ]);
            }

        }

        return View::render('admin/menu/navbar', [
            'menus' => $menus
        ]);
    }

    public static function getDepth_2($sub_menu) {
        $dropdown = '';


        foreach ($sub_menu['submenu'] as $k => $v) {
            $dropdown .= View::render('admin/menu/dropdown', [
                'depth_2' => $v['title'],
            ]);
        }

        return $dropdown;
    }

    public static function getPanel($title, $content, $currentModule) {
        $contentPanel = View::render('admin/panel', [
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

            $links .= View::render('admin/pagination/link', [
                'page' => $page['page'],
                'link' => $link,
                'active' => $page['current'] ? 'active' : ''
            ]);
        }

        return View::render('admin/pagination/box', [
            'links' => $links
        ]);
    }

}