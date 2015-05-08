<?php
include "setting/include.php";$cabang="01";$cari=$_GET["koderak"];
$db=new DB();$resultccabang=array();$koneksi=$db->connectDB($cabang);
if($koneksi["status"]){
    $sql="select Name from wms.RackSlot where RackSlotCode='".$cari."'";
    $result=$db->queryDB($sql);

    if($result["jumdata"]>0){
        while ($row = mssql_fetch_assoc($result["result"])) {$resultcabang[]=$row;}
    }else{$resultcabang[]=array("Name"=>"Salah");}
}
else{$resultcabang[]=array("Name"=>"Koneksi DB Terputus");}
echo json_encode(array('operatortaskpickingpck4cekrak'=>$resultcabang));