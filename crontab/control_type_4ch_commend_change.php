<?php
include_once __DIR__."/crontab_init.php";

use \App\Utils\Common;
use App\Model\Entity\ControlData as EntityControlData;
use App\Model\Entity\RawData as EntityRawData;


$result = EntityControlData::getControlData("control_type='4ch'","idx asc");
while ($obj = $result->fetchObject(EntityControlData::class)) {
    $_chk = true;

    $raw_data = EntityRawData::getRawData("address=".$obj->address." and board_type=".$obj->board_type." and board_number=".$obj->board_number, "idx desc","0, 1")->fetchObject(EntityRawData::class);

    if ($raw_data) {
        if ($obj->ch1 != $raw_data->data5) {
            $_chk = false;
        }
        if ($obj->ch2 != $raw_data->data6) {
            $_chk = false;
        }
        if ($obj->ch3 != $raw_data->data7) {
            $_chk = false;
        }
        if ($obj->ch4 != $raw_data->data8) {
            $_chk = false;
        }

        if ($_chk === false) {
            Common::ch4_commend($obj->address, $obj->board_type, $obj->board_number, $obj->ch1, $obj->ch2, $obj->ch3, $obj->ch4);
        }
    }
}


