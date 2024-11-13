<?php

namespace App\Model\Entity;

use \WilliamCosta\DatabaseManager\Database;

class PushSendLog {

    public $idx;
    public $push_title;
    public $push_content;

    public $individual;

    public $link_url;
    public $status_code;
    public $created_At;

    public function created() {
        $this->created_at = date('Y-m-d H:i:s');

        $this->idx = (new Database('push_send_log'))->insert([
            'push_title' => $this->push_title,
            'push_content' => $this->push_content,
            'individual' => $this->individual,
            'link_url' => $this->link_url,
            'status_code' => $this->status_code,
            'created_at' => $this->created_at,
        ]);

        return $this->idx;
    }

    public function updated() {
        $this->idx = (new Database('push_send_log'))->update('idx = '.$this->idx,[
            'push_title' => $this->push_title,
            'push_content' => $this->push_content,
            'link_url' => $this->link_url,
            'status_code' => $this->status_code,
        ]);
    }

    public static function getPushSendLog($where = null, $order = null, $limit = null, $fields = '*') {
        return (new Database('push_send_log'))->select($where, $order, $limit, $fields);
    }
}