<?php

namespace App\Model\Entity;

use \WilliamCosta\DatabaseManager\Database;

class WidgetBoardType {
    public $idx;
    public $widget_idx;
    public $data1_display;
    public $data1_name;
    public $data1_symbol;
    public $data2_display;
    public $data2_name;
    public $data2_symbol;
    public $data3_display;
    public $data3_name;
    public $data3_symbol;
    public $data4_display;
    public $data4_name;
    public $data4_symbol;
    public $data5_display;
    public $data5_name;
    public $data5_symbol;
    public $data6_display;
    public $data6_name;
    public $data6_symbol;
    public $data7_display;
    public $data7_name;
    public $data8_symbol;
    public $data8_display;
    public $data8_name;
    public $created_at;

    public function created() {
        $this->created_at = date('Y-m-d H:i:s');

        $this->idx = (new Database('widget_board_type'))->insert([
            'widget_idx' => $this->widget_idx,
            'data1_display' => $this->data1_display ?? '',
            'data1_name' => $this->data1_name ?? '',
            'data1_symbol' => $this->data1_symbol ?? '',
            'data2_display' => $this->data2_display ?? '',
            'data2_name' => $this->data2_name ?? '',
            'data2_symbol' => $this->data2_symbol ?? '',
            'data3_display' => $this->data3_display ?? '',
            'data3_name' => $this->data3_name ?? '',
            'data3_symbol' => $this->data3_symbol ?? '',
            'data4_display' => $this->data4_display ?? '',
            'data4_name' => $this->data4_name ?? '',
            'data4_symbol' => $this->data4_symbol ?? '',
            'data5_display' => $this->data5_display ?? '',
            'data5_name' => $this->data5_name ?? '',
            'data5_symbol' => $this->data5_symbol ?? '',
            'data6_display' => $this->data6_display ?? '',
            'data6_name' => $this->data6_name ?? '',
            'data6_symbol' => $this->data6_symbol ?? '',
            'data7_display' => $this->data7_display ?? '',
            'data7_name' => $this->data7_name ?? '',
            'data7_symbol' => $this->data7_symbol ?? '',
            'data8_display' => $this->data8_display ?? '',
            'data8_name' => $this->data8_name ?? '',
            'data8_symbol' => $this->data8_symbol ?? '',
            'created_at' => $this->created_at,
        ]);
    }

    public function updated() {
        $this->idx = (new Database('widget_board_type'))->update('idx ='.$this->idx,[
            'widget_idx' => $this->widget_idx,
            'data1_display' => $this->data1_display,
            'data1_name' => $this->data1_name,
            'data1_symbol' => $this->data1_symbol,
            'data2_display' => $this->data2_display,
            'data2_name' => $this->data2_name,
            'data2_symbol' => $this->data2_symbol,
            'data3_display' => $this->data3_display,
            'data3_name' => $this->data3_name,
            'data3_symbol' => $this->data3_symbol,
            'data4_display' => $this->data4_display,
            'data4_name' => $this->data4_name,
            'data4_symbol' => $this->data4_symbol,
            'data5_display' => $this->data5_display,
            'data5_name' => $this->data5_name,
            'data5_symbol' => $this->data5_symbol,
            'data6_display' => $this->data6_display,
            'data6_name' => $this->data6_name,
            'data6_symbol' => $this->data6_symbol,
            'data7_display' => $this->data7_display,
            'data7_name' => $this->data7_name,
            'data7_symbol' => $this->data7_symbol,
            'data8_display' => $this->data8_display,
            'data8_name' => $this->data8_name,
            'data8_symbol' => $this->data8_symbol,
        ]);
    }

    public static function getWidgetBoardTypes($where = null, $order = null, $limit = null, $fields = '*') {
        return (new Database('widget_board_type'))->select($where, $order, $limit, $fields);
    }

    public function deleted() {
        $this->idx = (new Database('widget_board_type'))->delete('idx ='.$this->idx);
    }

    public static function getWidgetBoardTypeByWidgetIdx($widget_idx) {
       return self::getWidgetBoardTypes("widget_idx=".$widget_idx);
    }

    public static function getWidgetWithWidgetBoardTypeByIdx($device_idx) {
        return (new Database('widget_board_type'))->execute("select wbt.* from widget as w left join widget_board_type as wbt on w.idx = wbt.widget_idx where w.device_idx =".$device_idx."")->fetchObject(self::class);
    }
}

