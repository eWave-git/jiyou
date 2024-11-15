<?php
include_once __DIR__."/crontab_init.php";

use \WilliamCosta\DatabaseManager\Database;
use \App\Utils\Common;
use \phpseclib3\Net\SFTP;

$file_name = "jisutech2006_TEST1_SP10_".date('Ymd_His');

$address = 4002;
$board_type = 4;
$board_number = ['3'];

$fp = fopen("./log/".$file_name.".log", 'a');

foreach ($board_number as $k => $v) {
    $raw_data_info = (new Database('raw_data'))->execute(
        "SELECT address, board_type, board_number, data1, data2, data3, created_at
            FROM jsro.raw_data
            WHERE address = 4002 and board_type = 4 and board_number = 3 order by idx desc limit 0, 1
           ")->fetchObject();
    
    $raw_data_info2 = (new Database('raw_data'))->execute(
        "select
            (max(data3)-ifnull(LAG(max(data3)) OVER (ORDER BY created_at), data3))*1 as water_in,
            (max(data4)-ifnull(LAG(max(data4)) OVER (ORDER BY created_at), data4))*1 as water_out
            FROM jsro.raw_data
            WHERE address = '4002' and board_number= 2 and (created_at >= now() - INTERVAL 1 hour)
        ")->fetchObject();
   

    $raw_data_info3 = (new Database('raw_data'))->execute(
        "select count(case when data5>10 then 1 end) AS run_time
            FROM jsro.raw_data
            WHERE address = '4002' and board_number= 3 and (created_at >= now() - INTERVAL 1 hour)
            order by idx desc;
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
        $_temp['mesureVal01'] = $raw_data_info->data3;
        $_temp['mesureVal02'] = $raw_data_info2->water_in;
        $_temp['mesureVal03'] = $raw_data_info2->water_out;
        $_temp['mesureVal04'] = ($raw_data_info2->water_in)-($raw_data_info2->water_out);
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
        //Common::print_r2($raw_data_info3);


        $_json = json_encode($_temp);

        fwrite($fp, $_json.chr(13));

        $sftp = new SFTP( getenv('FTP_HOST'), getenv('FTP_POST'));
        $sftp->login( getenv('FTP_USER'), getenv('FTP_PASS'));
        $sftp->put( "/home/jstech/".$file_name,"./log/".$file_name,1);

    }
}
fclose($fp);

//$_temp['eqpmnNo'] = $raw_data_info->address."-".$raw_data_info->board_type."-".$raw_data_info->board_number;