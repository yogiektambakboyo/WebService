<?php

include "setting/include.php";
$cabang="01";
$OperatorCode=$_GET["operatorcode"];
$TransactionCode=$_GET["transactioncode"];

$db=new DB();
$resultccabang=array();
$koneksi=$db->connectDB($cabang);

if($koneksi["status"]){
    $sql = "select m.ERPCode,b.Keterangan,d.DestBin as BinCode,d.ExpDate,dbo.KonversiSatuanToText(d.SKUCode,d.Qty) as Qty,r.Name
                from wms.MasterTaskRcv m
                inner join wms.DetailTaskRcv d
                on m.TransactionCode=d.TransactionCode
                inner join wms.BinImaginer i
                on d.BinCode=i.ReceiveSource
                left join barang b
                on b.Kode=d.SKUCode
                left join wms.RackSlot r
                on r.RackSlotCode=d.CurrRackSlot
                where m.TransactionCode='".$TransactionCode."' and d.CreateUserId='".$OperatorCode."'";
    $result=$db->queryDB($sql);

    if($result["jumdata"]>0){
        while ($row = mssql_fetch_assoc($result["result"])) {
            $resultcabang[]=$row;
        }
    }else{
        $resultcabang[]=array("ERPKode"=>"0","Keterangan"=>"Data Kosong","BinCode"=>"0","ExpDate"=>"0","Qty"=>"0","Name"=>"0");
    }
}
else{
    $resultcabang[]=array("Kode"=>"0","Keterangan"=>"Koneksi DB Terputus","BinCode"=>"0","ExpDate"=>"0","Qty"=>"0","Name"=>"0");
}
echo json_encode(array('operatortaskreceivebpb2listbrg'=>$resultcabang));