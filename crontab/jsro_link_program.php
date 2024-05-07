<?php
include_once __DIR__."/crontab_init.php";

use \WilliamCosta\DatabaseManager\Database;
use \App\Utils\Common;

$file_name = "jisutech2006_TEST1_SP10_".date('Ymd_His');

$address = 4002;
$board_type = 4;
$board_number = ['3'];

$fp = fopen("../log/".$file_name.".log", 'a');

foreach ($board_number as $k => $v) {
    $raw_data_info = (new Database('raw_data'))->execute(
        "SELECT address, board_type, board_number, data1, data2, data3, created_at
            FROM jsro.raw_data
            WHERE address = 4002 and board_type = 4 and board_number = 3 order by idx desc limit 0, 1
           ")->fetchObject();
    
    $raw_data_info2 = (new Database('raw_data'))->execute(
        "SELECT address, board_type, board_number, data1, data2, data3, data4, created_at
            FROM jsro.raw_data
            WHERE address = 4002 and board_type = 3 and board_number = 2 order by idx desc limit 0, 1
           ")->fetchObject();
    
    $raw_data_info3 = (new Database('raw_data'))->execute(
        "SELECT count(*) as run_time 
            FROM jsro.raw_data 
            WHERE address = 4002 and board_number = 3 and data5 > 0 and created_at > current_date() order by idx desc;
           ")->fetchObject();

    if (isset($raw_data_info->address)) {
        $_temp = array();
        $_temp['lsindRegistNo'] = "TEST1";
        $_temp['itemCode'] = "P00";
        $_temp['makrId'] = "jisutech2006";
        $_temp['eqpmnCode'] = "SF10";
        $_temp['eqpmnEsntlSn'] = "RO-".$raw_data_info->address;
        $_temp['eqpmnNo'] = "1";
        $_temp['stallTyCode'] = "SP10";
        $_temp['stallNo'] = "1";
        $_temp['roomNo'] = "1";
        $_temp['roomDtlNo'] = "1";
        $_temp['mesureDt'] = date_format(date_create($raw_data_info->created_at), "Y-m-d H:i:s");
        $_temp['mesureVal01'] = $raw_data_info->data1;
        $_temp['mesureVal02'] = $raw_data_info2->data4;
        $_temp['mesureVal03'] = $raw_data_info2->data3;
        $_temp['mesureVal04'] = ($raw_data_info2->data4)-($raw_data_info2->data3);
        $_temp['mesureVal05'] = $raw_data_info3->run_time;
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

//$_temp['eqpmnNo'] = $raw_data_info->address."-".$raw_data_info->board_type."-".$raw_data_info->board_number;