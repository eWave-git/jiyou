<?php

namespace App\Model\Entity;

use \WilliamCosta\DatabaseManager\Database;

class Report {
    public $idx;

    public $member_idx;

    public $report_title;
    public $report_sender;

    public $report_recipient;

    public $report_attachment;

    public $report_content;

    public $created_at;


    public static function getReport($where = null, $order = null, $limit = null, $fields = '*') {
        return (new Database('report'))->select($where, $order, $limit, $fields);
    }

    public function created() {
        $this->created_at = date('Y-m-d H:i:s');

        $this->idx = (new Database('report'))->insert([
            'member_idx' => $this->member_idx,
            'report_title' => $this->report_title,
            'report_sender' => $this->report_sender,
            'report_recipient' => $this->report_recipient,
            'report_attachment' => $this->report_attachment,
            'report_content' => $this->report_content,
            'created_at' => $this->created_at,
        ]);

        return $this->idx;
    }
}