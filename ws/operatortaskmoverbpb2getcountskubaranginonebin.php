

<?php

include "setting/include.php";
$cabang="01";
$kodebin=$_GET["kodebin"];

$db=new DB();
$resultccabang=array();
$koneksi=$db->connectDB($cabang);

if($koneksi["status"]){
    $sql="select count(*) as Jumlah from wms.BinSKU where BinCode='".$kodebin."' and Qty>0";
    $result=$db->queryDB($sql);

    if($result["jumdata"]>0){
        while ($row = mssql_fetch_assoc($result["result"])) {
            $resultcabang[]=$row;
        }
    }else{
        $resultcabang[]=array("Jumlah"=>"0");
    }
}
else{
    $resultcabang[]=array("Jumlah"=>"Koneksi DB Terputus");
}
echo json_encode(array('operatortaskmoverbpb2getcountskubaranginonebin'=>$resultcabang));