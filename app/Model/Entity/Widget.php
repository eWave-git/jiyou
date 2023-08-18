<?php

namespace App\Model\Entity;

use \WilliamCosta\DatabaseManager\Database;

class Widget {
    public $idx;
    public $member_idx;
    public $widget_name;
    public $widget_type;
    public $graph_interval;
    public $created_at;


    public static function getWidgeTablebyMemberIdx($user_idx) {
        return self::getWidgets("member_idx ='".$user_idx."' and widget_type='text' ");
    }

    public static function getWidgeChartbyMemberIdx($user_idx) {
        return self::getWidgets("member_idx ='".$user_idx."' and widget_type='graph' ");
    }

    public static function getWidgets($where = null, $order = null, $limit = null, $fields = '*') {
        return (new Database('widget'))->select($where, $order, $limit, $fields);
    }

    public function created() {
        $this->created_at = date('Y-m-d H:i:s');

        $this->idx = (new Database('widget'))->insert([
            'member_idx' => $this->member_idx,
            'widget_name' => $this->widget_name,
            'widget_type' => $this->widget_type,
            'graph_interval' => $this->graph_interval,
            'created_at' => $this->created_at,
        ]);

        return $this->idx;
    }

    public function updated() {
        $this->idx = (new Database('widget'))->update('idx ='.$this->idx,[
            'member_idx' => $this->member_idx,
            'widget_name' => $this->widget_name,
            'widget_type' => $this->widget_type,
            'graph_interval' => $this->graph_interval,
        ]);
    }

    public function deleted() {
        $this->idx = (new Database('widget'))->delete('idx ='.$this->idx);
    }
}