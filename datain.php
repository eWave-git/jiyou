<?php
date_default_timezone_set('Asia/Seoul');

$date = date("Y-m-d H:i:s");

foreach ($_REQUEST as $k => $v) {
	$$k = $v;
}

$conn = mysqli_connect("database-2.cvdze1lptugg.ap-northeast-2.rds.amazonaws.com","wave2","qkrtjs4871##KL","upa") or die ("Can't access DB");



$txt = "datain === "."date :".$date." | "."mac :".$mac." | "."sig :".$sig." | "."bat :".$bat." | "."volt :".$volt." | "."SMODEL :".$SMODEL." | "."C000 :".$C000." | "."P000 :".$P000."\r\n";
//$txt = "aa";
$fp = fopen("datain.txt", 'a');
fwrite($fp, $txt);
fclose($fp);


$c = explode('|', $C000);

$c1 = empty($c[1]) ? 0 : round($c[1], 1);
$c2 = empty($c[2]) ? 0 : round($c[2], 1);
$c3 = empty($c[3]) ? 0 : round($c[3], 1);
$c4 = empty($c[4]) ? 0 : round($c[4], 1);

$sql = "INSERT INTO raw_data (	`created_at`,`address`,`board_type`,`board_number`,`data1`,`data2`,`data3`,`data4`) VALUES ('{$date}', '8001','71' , '1' , $c1, $c2, $c3, $c4)";

$result = mysqli_query($conn, $sql);

function array_to_xml( $root, $arr ) {
        function a2x( $arr, &$xml, $pk=null ) {
                foreach( $arr as $k => $v ) {
                        if( !is_array($v) ) $xml->addChild($k,htmlspecialchars($v));
                        else if( is_numeric(key($v)) ) a2x($v, $xml, $k);
                        else a2x($v, $xml->addChild( is_null($pk)? $k: $pk));
                }
        }
        $xml = new SimpleXMLElement("<$root/>");
        a2x($arr, $xml);
        return $xml->asXML();
}

$arr = [
      'ack'=>'ok',
];
echo array_to_xml('root',$arr);