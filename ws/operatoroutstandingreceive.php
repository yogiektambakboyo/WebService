<?php

include "setting/include.php";
$cabang="01";
$OperatorCode=$_GET["operatorcode"];
$ProjectCode=$_GET["projectcode"];
$OperatorRole=$_GET["operatorrole"];

$db=new DB();
$resultccabang=array();
$koneksi=$db->connectDB($cabang);

if($koneksi["status"]){
    $sql = "SELECT DISTINCT h.NoUrut,h.SKUCode,m.ERPCode,h.QueueNumber,h.TransactionCode,h.BinCode,b.Keterangan,h.Qty,de.Ratio,dbo.KonversiSatuanToText(h.SKUCode,h.Qty) as QtyKonversi,de.RatioName,
                (SELECT r.Name FROM wms.RackSlot r WHERE r.RackSlotCode=d.DestRackSlot) AS DestRackSlot,
                (SELECT TOP 1 r.Name FROM wms.RackSlot r WHERE r.RackSlotCode=h.CurrRackSlot ORDER BY h.QueueNumber) AS CurrRackSlot
                FROM wms.DetailTaskRcvHistory h
                INNER JOIN
                wms.MasterTaskRcv m
                ON h.TransactionCode=m.TransactionCode
                INNER JOIN
                wms.DetailTaskRcv d
                ON h.TransactionCode=d.TransactionCode AND h.NoUrut=d.NoUrut
                LEFT JOIN
                dbo.Barang b
                ON b.Kode=h.SKUCode
                LEFT JOIN
                wms.DetailTaskDERP de
                ON de.TransactionCode=h.TransactionCode AND de.SKUCode=h.SKUCode
                WHERE h.User_1st ='" . $OperatorCode . "'
                AND h.User_2nd IS NULL ";
    if ($ProjectCode) {
        $sql.="AND m.ProjectCode='" . $ProjectCode . "'";
    }
    $sql.=" AND h.TransactionCode IN (select distinct m.TransactionCode
                from wms.MasterTaskRcv m,wms.DetailTaskOpr d
                where m.isFinish=0 AND m.isFinishMove=0 AND m.isCancel=0 and d.TransactionCode=m.TransactionCode and d.OperatorCode='" . $OperatorCode . "' and d.OprRole='" . $OperatorRole . "')
                ORDER BY h.QueueNumber DESC";
    $result=$db->queryDB($sql);

    if($result["jumdata"]>0){
        while ($row = mssql_fetch_assoc($result["result"])) {
            $resultcabang[]=$row;
        }
    }else{
        $resultcabang[]=array("NoUrut"=>"0","SKUCode"=>"0","ERPCode"=>"0","QueueNumber"=>"0","TransactionCode"=>"0","BinCode"=>"0","Keterangan"=>"0","Qty"=>"0","Ratio"=>"0","QtyKonversi"=>"0","RatioName"=>"0","DestRackSlot"=>"0","CurrRackSlot"=>"0");
    }
}
else{
    $resultcabang[]=array("NoUrut"=>"0","SKUCode"=>"0","ERPCode"=>"0","QueueNumber"=>"0","TransactionCode"=>"0","BinCode"=>"0","Keterangan"=>"Koneksi Server Terputus!","Qty"=>"0","Ratio"=>"0","QtyKonversi"=>"0","RatioName"=>"0","DestRackSlot"=>"0","CurrRackSlot"=>"0");
}
echo json_encode(array('operatoroutstandingreceive'=>$resultcabang));