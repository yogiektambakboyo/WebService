<?php
include "setting/include.php";$cabang="01";$cari=$_GET["koderakslot"];
$db=new DB();$resultccabang=array();$koneksi=$db->connectDB($cabang);
if($koneksi["status"]){
    $sql = "select RackSlotCode from wms.rackslot where RackSlotCode='" . $cari . "'";
    $result=$db->queryDB($sql);
    if($result["jumdata"]>0){
        while ($row = mssql_fetch_assoc($result["result"])) {$resultcabang[]=$row;}
    }else{$resultcabang[]=array("RackSlotCode"=>"0");}
}
else{$resultcabang[]=array("RackSlotCode"=>"Koneksi DB Terputus");}
echo json_encode(array('operatortaskreceivebpb2cekrakslot'=>$resultcabang));