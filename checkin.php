<?php
date_default_timezone_set('Asia/Seoul');

$date = date("Y-m-d H:i:s");

foreach ($_REQUEST as $k => $v) {
	$$k = $v;
}

$txt = "checkin === "."date :".$date." | "."mac :".$mac." | "."ver :".$ver." | "."model :".$model." | "."ip :".$ip." | "."splrate :".$splrate." | "."interval :".$interval." | "."tages :".$tags."\r\n";
//$txt = "aa";
$fp = fopen("checkin.txt", 'a');
fwrite($fp, $txt);
fclose($fp);


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
      'timestamp'=>strtotime($date),
      'offest-ch1'=>'',
      'offest-ch2'=>'',
      'sample-mode'=>''
];
echo array_to_xml('root',$arr);