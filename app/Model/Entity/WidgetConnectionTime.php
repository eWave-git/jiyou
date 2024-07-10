<?php

namespace App\Model\Entity;

use \WilliamCosta\DatabaseManager\Database;

class WidgetConnectionTime {
    public $idx;
    public $widget_idx;
    public $check_yn;
    public $check_time;
    public $created_at;

    public static function getWidgetConnections() {
        return (new Database('widget_connection_time'))->execute("select m.*, w.*, wct.* from member as m left join farm as f on m.idx = f.member_idx left join device as d on f.idx = d.farm_idx left join widget as w on w.device_idx = d.idx left join widget_connection_time as wct on wct.widget_idx = w.idx where m.member_type = 'manager'  and f.idx is not null and wct.idx is not null and wct.check_yn = 'Y'");
    }

    public static function getWidgetConnectionByWidgetIdx($widget_idx) {
        return self::getWidgetConnection("widget_idx ='".$widget_idx."'");
    }

    public static function getWidgetConnection($where = null, $order = null, $limit = null, $fields = '*') {
        return (new Database('widget_connection_time'))->select($where, $order, $limit, $fields);
    }

    public function created() {
        $this->created_at = date('Y-m-d H:i:s');

        $this->idx = (new Database('widget_connection_time'))->insert([
            'widget_idx' => $this->widget_idx,
            'check_yn' => $this->check_yn,
            'check_time' => $this->check_time,
            'created_at' => $this->created_at,
        ]);

        return $this->idx;
    }

    public function updated() {
        $this->idx = (new Database('widget_connection_time'))->update('idx ='.$this->idx,[
            'check_yn' => $this->check_yn,
            'check_time' => $this->check_time,
        ]);
    }
}