<?php
namespace app\Utils;

use Exception;

class Common{

    private static $vars = [];

    public static function init($vars = []) {
        self::$vars = $vars;
    }


     public static function print_r2($vars) {
        echo "<pre>";
        print_r($vars);
        echo "<pre>";
        exit;
    }

    public static function str_chekc($str, $msg) {

        if (!isset($str) || empty($str)) {
            self::error_msg($msg);
            exit;
        }

    }

    public static function error_msg($msg) {
        echo "<script language='javascript'>alert('$msg');history.back();</script>";
        exit;
    }

    public static function error_loc_msg($loc, $msg, $target=null)  {
        if($target) { echo "<script language='javascript'>alert('$msg');".$target.".location.href=('${loc}');</script>"; }
        else { echo "<script language='javascript'>alert('$msg');location.href=('${loc}');</script>"; }
        exit;
    }

}