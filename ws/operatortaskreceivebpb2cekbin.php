<?php
include "setting/include.php";$cabang="01";$cari=$_GET["kodebin"];
$db=new DB();$resultccabang=array();$koneksi=$db->connectDB($cabang);
if($koneksi["status"]){
    $sql = "select isUsed from wms.bin where BinCode='" .$cari. "'";
    $result=$db->queryDB($sql);

    if($result["jumdata"]>0){
        while ($row = mssql_fetch_assoc($result["result"])) {$resultcabang[]=$row;}
    }else{$resultcabang[]=array("isUsed"=>"0");}
}
else{$resultcabang[]=array("isUsed"=>"Koneksi DB Terputus");}
echo json_encode(array('operatortaskreceivebpb2cekbin'=>$resultcabang));