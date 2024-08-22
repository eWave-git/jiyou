<?php

namespace App\Model\Entity;

use \WilliamCosta\DatabaseManager\Database;

class AligoSmsSendLog {
    public $idx;
    public $title;
    public $msg;
    public $receiver;
    public $result_code;
    public $message;
    public $msg_id;
    public $success_cnt;
    public $error_cnt;
    public $msg_type;
    public $created_at;

    public function created() {
        $this->created_at = date('Y-m-d H:i:s');

        $this->idx = (new Database('aligo_sms_send_log'))->insert([
            'title' => $this->title,
            'msg' => $this->msg,
            'receiver' => $this->receiver,
            'result_code' => $this->result_code,
            'message' => $this->message,
            'msg_id' => $this->msg_id,
            'success_cnt' => $this->success_cnt,
            'error_cnt' => $this->error_cnt,
            'msg_type' => $this->msg_type,
            'created_at' => $this->created_at,
        ]);

        return $this->idx;
    }
}