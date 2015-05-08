<?php
include "setting/include.php";$cabang="01";$TransactionCode=$_GET["transactioncode"];
$db=new DB();$resultccabang=array();$koneksi=$db->connectDB($cabang);
if($koneksi["status"]){
    $sql = "exec wms.PickingTask '" . $TransactionCode . "'";
    $result=$db->queryDB($sql);
    if($result["jumdata"]>0){
        while ($row = mssql_fetch_assoc($result["result"])) {$resultcabang[]=$row;}
    }else{$resultcabang[]=array("TransactionCode"=>"0","ERPCode"=>"Data Kosong","SKUCode"=>"0","NoUrut"=>"0","Keterangan"=>"0","Qty"=>"0","Satuan"=>"0","Needed"=>"0","Picked"=>"0","Konversi"=>"0","AddTask"=>"0");}
}
else{$resultcabang[]=array("TransactionCode"=>"0","ERPCode"=>"Koneksi Server Terputus","SKUCode"=>"0","NoUrut"=>"0","Keterangan"=>"0","Qty"=>"0","Satuan"=>"0","Needed"=>"0","Picked"=>"0","Konversi"=>"0","AddTask"=>"0");}
echo json_encode(array('operatortaskpickingpck2'=>$resultcabang));
