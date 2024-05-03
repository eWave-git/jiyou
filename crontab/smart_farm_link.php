<?php
include_once __DIR__."/crontab_init.php";

use \WilliamCosta\DatabaseManager\Database;
use \App\Utils\Common;

$file_name = "ewave2332_TEST1_ES01_".date('Ymd_His');

$address = 2300;
$board_type = 35;
$board_number = ['19','18','17'];

$fp = fopen("./log/".$file_name.".log", 'a');

foreach ($board_number as $k => $v) {
    $raw_data_info = (new Database('raw_data'))->execute(
        "SELECT address, board_type, board_number, data1, data2, data3, created_at
                FROM upa.raw_data
                WHERE address={$address} and board_type = {$board_type} and board_number = {$v} order by idx desc limit 0, 1
           ")->fetchObject();

    if (isset($raw_data_info->address)) {
        $_temp = array();
        $_temp['lsindRegistNo'] = "TEST1";
        $_temp['itemCode'] = "P00";
        $_temp['makrId'] = "ewave2332";
        $_temp['eqpmnCode'] = "ES01";
        $_temp['eqpmnEsntlSn'] = "";
        $_temp['eqpmnNo'] = $raw_data_info->address."-".$raw_data_info->board_type."-".$raw_data_info->board_number;
        $_temp['stallTyCode'] = "SP07";
        $_temp['stallNo'] = $raw_data_info->board_number;
        $_temp['roomNo'] = "";
        $_temp['roomDtlNo'] = "";
        $_temp['mesureDt'] = date_format(date_create($raw_data_info->created_at), "Y-m-d H:i:s");
        $_temp['mesureVal01'] = $raw_data_info->data1;
        $_temp['mesureVal02'] = "";
        $_temp['mesureVal03'] = "";
        $_temp['mesureVal04'] = "";
        $_temp['mesureVal05'] = "";
        $_temp['mesureVal06'] = "";
        $_temp['mesureVal07'] = "";
        $_temp['mesureVal08'] = "";
        $_temp['mesureVal09'] = "";
        $_temp['mesureVal10'] = "";
        $_temp['mesureVal11'] = "";
        $_temp['mesureVal12'] = "";
        $_temp['mesureVal13'] = "";
        $_temp['mesureVal14'] = "";
        $_temp['mesureVal15'] = "";

        $_json = json_encode($_temp);

        fwrite($fp, $_json.chr(13));

    }
}
fclose($fp);