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

    public static function var_dump2($vars) {
        echo "<pre>";
        var_dump($vars);
        echo "<pre>";
        exit;
    }


    public static function str_chekc($str, $msg) {

        if (!isset($str) || empty($str)) {
            self::error_msg($msg);
            exit;
        }

        return $str;
    }

    public static function int_check($int, $msg) {

        if (!is_numeric($int)) {
            self::error_msg($msg);
            exit;
        }

        return $int;
    }

    public static function get_manager() {
        if (!$_SESSION['manager']) return null;

        return $_SESSION['manager']['user']['id'];
    }

    // TODO : date_range
    /**
     *
     * @param $startDate
     * @param $lastDate
     * @return array|string
     *
     * getDatesStartToLast("2020-09-25", "2020-09-25")
     */
    public static function getDatesStartToLast($startDate, $lastDate) {
        $regex = "/^\d{4}-(0[1-9]|1[012])-(0[1-9]|[12][0-9]|3[0-1])$/";
        if(!(preg_match($regex, $startDate) && preg_match($regex, $lastDate))) return "Not Date Format";
        $period = new DatePeriod( new DateTime($startDate), new DateInterval('PT1M'), new DateTime($lastDate." +1 day"));
        foreach ($period as $date) $dates[] = $date->format("Y-m-d H:i:s");
        return $dates;
    }

    public static function date_range($first, $last, $step = '+1 day', $output_format = 'd/m/Y' )  {
        $dates = array();
        $current = strtotime($first);
        $last = strtotime($last);

        while( $current <= $last ) {
            //$dates[] = date($output_format, $current);
            $dates[] = date($output_format, $current);
            $current = strtotime($step, $current);
        }

        return $dates;
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