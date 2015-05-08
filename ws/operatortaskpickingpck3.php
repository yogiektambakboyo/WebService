<?php
include "setting/include.php";$cabang="01";$OperatorCode=$_GET["operatorcode"];
$db=new DB();$resultccabang=array();$koneksi=$db->connectDB($cabang);
if($koneksi["status"]){
    $sql = "SELECT DISTINCT m.ERPCode,d.TransactionCode,d.SKUCode,
                d.NoUrut,d.QtyNeedStart,b.Keterangan,d.Qty,d.SrcRackSlot,d.SrcBin,d.DestBin,r.Name
                FROM wms.DetailTaskPck d
                INNER JOIN
                wms.MasterTaskPck m
                ON d.TransactionCode=m.TransactionCode
                LEFT JOIN
                dbo.Barang b
                ON b.Kode=d.SKUCode
                LEFT JOIN
                wms.RackSlot r
                ON d.SrcRackSlot=r.RackSlotCode
                WHERE d.User_1st ='" . $OperatorCode . "'
                AND d.User_2nd IS NULL AND
                d.TransactionCode IN (select distinct m.TransactionCode
                from wms.MasterTaskPck m,wms.DetailTaskOpr d
                where m.isFinish=0 and m.isCancel=0 and m.isFinishMove=0 and d.TransactionCode=m.TransactionCode and d.OperatorCode='" . $OperatorCode . "')";
    $result=$db->queryDB($sql);
    if($result["jumdata"]>0){
        while ($row = mssql_fetch_assoc($result["result"])) {$resultcabang[]=$row;}
    }else{$resultcabang[]=array("TransactionCode"=>"0","ERPCode"=>"Data Kosong","SKUCode"=>"0","NoUrut"=>"0","Keterangan"=>"0","QtyNeedStart"=>"0","Qty"=>"0","SrcRackSlot"=>"0","SrcBin"=>"0","DestBin"=>"0","Name"=>"0");}
}
else{$resultcabang[]=array("TransactionCode"=>"0","ERPCode"=>"Koneksi Server Terputus","SKUCode"=>"0","NoUrut"=>"0","Keterangan"=>"0","QtyNeedStart"=>"0","Qty"=>"0","SrcRackSlot"=>"0","SrcBin"=>"0","DestBin"=>"0","Name"=>"0");}
echo json_encode(array('operatortaskpickingpck2'=>$resultcabang));