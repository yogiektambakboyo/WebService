<?php
include "setting/include.php";$cabang="01";$TransactionCode=$_GET["transactioncode"];$SKUCode=$_GET["skucode"];$BinCode=$_GET["bincode"];$BinTemp=$_GET["bintemp"];$RackCode=$_GET["rackcode"];$QtyRasio=$_GET["qtyrasio"];$OperatorCode=$_GET["operatorcode"];$Needed=$_GET["needed"];$Qty=$_GET["qty"];$AddTask=$_GET["addtask"];$NoUrut=$_GET["nourut"];
$db=new DB();$resultccabang=array();$koneksi=$db->connectDB($cabang);
if($koneksi["status"]){
    $ExpDate="0";$sql = "SELECT ExpDate FROM wms.BinSKU WHERE BinCode='".$BinCode."' AND SKUCode='".$SKUCode."'";
    $result=$db->queryDB($sql);
    if($result["jumdata"]>0){while ($row = mssql_fetch_assoc($result["result"])) {$ExpDate=$row["ExpDate"];}}else{$resultcabang[]=array("Status"=>"Exp Date Tidak Ada");}
    $sql = "SELECT max(NoUrut) as Jumlah FROM wms.DetailTaskPck where TransactionCode='" . $TransactionCode . "'";$result=$db->queryDB($sql);$NoUrutBaru;
    if($result["jumdata"]>0){
        while($row = mssql_fetch_assoc($result["result"])){$NoUrutBaru=$row["Jumlah"];}
    }else{$resultcabang[]=array("Status"=>"No Urut Salah");}
    if($AddTask == 1){$sql = "UPDATE wms.DetailTaskPck set ExpDate='".$ExpDate."',Qty='" . $QtyRasio . "',SrcRackSlot='" . $RackCode . "',Srcbin='" . $BinCode . "',Destbin='" . $BinTemp . "',User_1st='" . $OperatorCode . "',Time_1st=getdate() WHERE TransactionCode='".$TransactionCode."' and NoUrut='".$NoUrut."'";}else{$sql = "INSERT INTO wms.DetailTaskPck(ExpDate,TransactionCode,NoUrut,SKUCode,QtyNeedStart,QtyNeedNow,Qty,SrcRackSlot,SrcBin,DestBin,User_1st,Time_1st) values('".$ExpDate."','" . $TransactionCode . "','" . ($NoUrutBaru+1) . "','" . $SKUCode . "','" . $QtyRasio . "','" . $QtyRasio . "','" . $QtyRasio . "','" . $RackCode . "','" . $BinCode . "','" . $BinTemp. "','" . $OperatorCode . "',getdate())";}
    $resultcabang[]=array("Status"=>"1");
    if($db->executeDB($sql)){$resultcabang[]=array("Status"=>"1");}else{$resultcabang[]=array("Status"=>"Proses Gagal");}
}
else{$resultcabang[]=array("Status"=>"0");}
echo json_encode(array('operatortaskpickingpcksetambilbarang'=>$resultcabang));