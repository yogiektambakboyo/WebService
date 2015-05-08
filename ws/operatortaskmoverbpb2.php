<?php

include "setting/include.php";
$cabang="01";
$OperatorCode=$_GET["operatorcode"];
$TransactionCode=$_GET["transactioncode"];
$OperatorRole=$_GET["operatorrole"];
$ProjectCode=$_GET["projectcode"];

$db=new DB();
$resultccabang=array();
$koneksi=$db->connectDB($cabang);

if($koneksi["status"]){
    $sql = "SELECT DISTINCT h.NoUrut,h.QueueNumber,m.ERPCode,h.QueueNumber,h.TransactionCode,h.BinCode,h.SKUCode,b.Keterangan,h.Qty,dbo.KonversiSatuanToText(h.SKUCode,h.Qty) as QtyKonversi,de.Ratio,de.RatioName,
                (SELECT r.Name FROM wms.RackSlot r WHERE r.RackSlotCode=d.DestRackSlot) AS DestRackSlot,
                (SELECT TOP 1 r.Name FROM wms.RackSlot r WHERE r.RackSlotCode=h.CurrRackSlot ORDER BY h.QueueNumber) AS CurrRackSlot
                FROM wms.DetailTaskRcvHistory h
                INNER JOIN
                wms.MasterTaskRcv m
                ON h.TransactionCode=m.TransactionCode
                INNER JOIN
                wms.DetailTaskRcv d
                ON h.TransactionCode=d.TransactionCode AND h.NoUrut=d.NoUrut
                INNER JOIN
                (select distinct m.TransactionCode
                from wms.MasterTaskRcv m,wms.DetailTaskOpr d
                where m.isFinish=0 AND m.isFinishMove=0 AND m.isCancel=0 and d.TransactionCode=m.TransactionCode and d.OperatorCode='" . $OperatorCode . "' and d.OprRole='" . $OperatorRole . "') a
                ON a.TransactionCode= h.TransactionCode
                LEFT JOIN
                dbo.Barang b
                ON b.Kode=h.SKUCode
                LEFT JOIN
                wms.DetailTaskDERP de
                ON de.TransactionCode=h.TransactionCode AND de.SKUCode=h.SKUCode
                WHERE h.User_1st IS NULL
                AND h.User_2nd IS NULL
                AND m.ProjectCode='" . $ProjectCode . "'
                AND m.TransactionCode='". $TransactionCode ."'
                ORDER BY h.QueueNumber DESC";
    $result=$db->queryDB($sql);

    if($result["jumdata"]>0){
        while ($row = mssql_fetch_assoc($result["result"])) {
            $resultcabang[]=$row;
        }
    }else{
        $resultcabang[]=array("NoUrut"=>"0","QueueNumber"=>"0","ERPCode"=>"Data Kosong","TransactionCode"=>"0","BinCode"=>"0","SKUCode"=>"0","Keterangan"=>"0","Qty"=>"0","QtyKonversi"=>"0","Ratio"=>"0","RatioName"=>"0","DestRackSlot"=>"0","CurrRackSlot"=>"0");
    }
}
else{
    $resultcabang[]=array("NoUrut"=>"0","QueueNumber"=>"0","ERPCode"=>"Koneksi Server terputus","TransactionCode"=>"0","BinCode"=>"0","SKUCode"=>"0","Keterangan"=>"0","Qty"=>"0","QtyKonversi"=>"0","Ratio"=>"0","RatioName"=>"0","DestRackSlot"=>"0","CurrRackSlot"=>"0");
}
echo json_encode(array('operatortaskmoverbpb2'=>$resultcabang));