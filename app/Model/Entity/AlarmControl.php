<?php

namespace App\Model\Entity;

use \WilliamCosta\DatabaseManager\Database;

class AlarmControl {
    public $idx;
    public $member_group;
    public $member_id;
    public $activation;
    public $created_at;


    public static function getAlarmcontrolActivation($member_group) {
        return (new Database('alarm_control'))->execute(
            "select * From alarm_control where member_group = ".$member_group." order by created_at desc limit 1"
        );
    }

    public function created() {
        $this->created_at = date('Y-m-d H:i:s');

        $this->idx = (new Database('alarm_control'))->insert([
            'member_group' => $this->member_group,
            'member_id' => $this->member_id,
            'activation' => $this->activation,
            'created_at' => $this->created_at,
        ]);

        return $this->idx;
    }

    public function getAlarmStops($where = null, $order = null, $limit = null, $fields = '*') {
        return (new Database('alarm_control'))->select($where, $order, $limit, $fields);
    }
}