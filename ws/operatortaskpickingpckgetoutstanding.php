<?php
include "setting/include.php";$cabang="01";$OperatorCode=$_GET["operatorcode"];
$db=new DB();$resultccabang=array();$koneksi=$db->connectDB($cabang);
if($koneksi["status"]){
    $sql = "select top 1 TransactionCode,DestBin from wms.DetailTaskPck where User_1st='" . $OperatorCode . "' and User_2nd is null";
    $result=$db->queryDB($sql);$TransactionCode='';
    if($result["jumdata"]>0){
        while ($row = mssql_fetch_assoc($result["result"])) {$resultcabang[]=$row;$TransactionCode=$row["TransactionCode"];}
        $sql = "select ERPCode from wms.MasterTaskPck where TransactionCode='".$TransactionCode."'";
        $result=$db->queryDB($sql);
        if($result["jumdata"]>0){while($row = mssql_fetch_assoc($result["result"])){$resultcabang[0]["ERPCode"]=$row["ERPCode"];}
        }else{$resultcabang[0]["ERPCode"]="0";}
    }else{$resultcabang[]=array("TransactionCode"=>"0","DestBin"=>"Data Kosong","ERPCode"=>"0");}
}
else{$resultcabang[]=array("TransactionCode"=>"0","DestBin"=>"Koneksi Server Terputus","ERPCode"=>"0");}
echo json_encode(array('operatortaskpickingpckgetoutstanding'=>$resultcabang));