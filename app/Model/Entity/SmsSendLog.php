<?php

namespace App\Model\Entity;

use \WilliamCosta\DatabaseManager\Database;

class SmsSendLog {

    public $idx;
    public $body;
    public $numSegments;
    public $direction;
    public $smsFrom;
    public $smsTo;
    public $errorMessage;
    public $status;
    public $sid;
    public $created_at;

    public function created() {
        $this->created_at = date('Y-m-d H:i:s');

        $this->idx = (new Database('sms_send_log'))->insert([
            'body' => $this->body,
            'numSegments' => $this->numSegments,
            'direction' => $this->direction,
            'smsFrom' => $this->smsFrom,
            'smsTo' => $this->smsTo,
            'errorMessage' => $this->errorMessage,
            'status' => $this->status,
            'sid' => $this->sid,
            'created_At' => $this->created_at,
        ]);

        return $this->idx;
    }
}