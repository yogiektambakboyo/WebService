<?php
include "setting/include.php";$cabang="01";$koderak=$_GET["koderak"];$OperatorCode=$_GET["operatorcode"];$TransactionCode=$_GET["transactioncode"];$kodebin=$_GET["kodebin"];
$db=new DB();$resultccabang=array();$koneksi=$db->connectDB($cabang);
if($koneksi["status"]){
    $sql = "SELECT NoUrut FROM wms.DetailTaskPck WHERE User_1st='" . $OperatorCode . "' AND User_2nd IS NULL AND DestBin='" . $kodebin . "' AND TransactionCode='" . $TransactionCode . "'";
    $result=$db->queryDB($sql);
    if($result["jumdata"]>0){
        while ($row = mssql_fetch_assoc($result["result"])) {
            $resultcabang[]=$row;
            $sql = "UPDATE wms.DetailTaskPck SET DestRackSlot='" . $koderak . "' , User_2nd='" . $OperatorCode . "', Time_2nd=getdate() WHERE NoUrut='".$row['NoUrut']."' AND TransactionCode='" . $TransactionCode . "'";
            if($db->executeDB($sql)){$resultcabang[]=array("Status"=>"1");}else{$resultcabang[]=array("Status"=>"0");}
        }
    }else{$resultcabang[]=array("Status"=>"0");}
}
else{$resultcabang[]=array("NoUrut"=>"Koneksi DB Terputus");}
echo json_encode(array('operatortaskpickingpck4updatetaruhbin'=>$resultcabang));