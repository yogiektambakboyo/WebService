<?php
include "setting/include.php";$cabang="01";
$TransactionCode=$_GET["transactioncode"];
$BinCode=$_GET["bincode"];
$SKUCode=$_GET["skucode"];
$ExpDate=$_GET["expdate"];
$Qty=$_GET["qty"];
$CurrRackSlot=$_GET["currrackslot"];
$DestRackSlot=$_GET["destrackslot"];
$ReceiveSource=$_GET["receivesource"];
$OperatorCode=$_GET["operatorcode"];
$No=0;
$db=new DB();
$resultccabang=array();
$koneksi=$db->connectDB($cabang);

if($koneksi["status"]){
    $sql = "select count(*) as jumlah from wms.DetailTaskRcv where TransactionCode='".$TransactionCode."'";
    $result=$db->queryDB($sql);

    if($result["jumdata"]>0){
        while ($row = mssql_fetch_assoc($result["result"])) {
            $No=$row["jumlah"];
        }

        $sql = "insert into wms.DetailTaskRcv(TransactionCode,NoUrut,BinCode,SKUCode,ExpDate,Qty,CurrRackSlot,CurrOnAisle,DestOnAisle,DestBin,DestQty,CreateUserId)
                values('" . $TransactionCode . "',".($No+1).",'".$ReceiveSource."','" . $SKUCode . "','" . $ExpDate . "','" . $Qty . "','" . $CurrRackSlot . "','1','1','" . $BinCode . "','" . $Qty . "','".$OperatorCode."')";

        if($db->executeDB($sql)){
            $sql = "insert into wms.DetailTaskRcv(TransactionCode,NoUrut,BinCode,SKUCode,ExpDate,Qty,CurrRackSlot,CurrOnAisle,DestOnAisle,CreateUserId)
                values('" . $TransactionCode . "',".($No+2).",'" . $BinCode . "','" . $SKUCode . "','" . $ExpDate . "','" . $Qty . "','" . $CurrRackSlot . "','1','0','".$OperatorCode."')";
            if($db->executeDB($sql)){
                $resultcabang[]=array("Status"=>"1");
            }else{
                $resultcabang[]=array("Status"=>"0");
            }
        }else{
            $resultcabang[]=array("Status"=>"0");
        }
    }else{
        $resultcabang[]=array("Status"=>"0");
    }
}
else{
    $resultcabang[]=array("Satuan"=>"Koneksi DB Terputus");
}
echo json_encode(array('operatortaskreceivebpb2insert'=>$resultcabang));


