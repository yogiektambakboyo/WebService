<?php

include "setting/include.php";
$cabang="01";
$operatorcode=$_GET["operatorcode"];

$db=new DB();
$resultccabang=array();
$koneksi=$db->connectDB($cabang);

if($koneksi["status"]){
    $sql="SELECT mt.TransactionCode as TransactionCode,mt.ERPCode as ERPCode,
                (case when mt.ProjectCode='BPB' then
                (select s.perusahaan from supplier s,masterbeli m where m.supplier=s.kode and m.kodenota=mt.ERPCode)
                else
                (select keterangan from gudang where kode=(select max(d.AsalGudang) from mastertransfer m,detailtransfer d where m.kodenota=d.kodenota and m.kodenota=mt.ERPCode)) end) as Perusahaan
                ,mt.TransactionDate as TransactionDate,mt.Note,isnull(o.Assigned,2) as Assigned
                FROM wms.MasterTaskRcv mt
                left join wms.DetailTaskOpr o
                on mt.TransactionCode=o.TransactionCode and o.OprRole='10/WHR/002' and o.OperatorCode='".$operatorcode."'
                WHERE
                mt.isFinish=0 AND mt.isCancel=0 AND mt.isFinishMove=0 AND (mt.ProjectCode='BPB' OR mt.ProjectCode='RTS')
                ORDER BY mt.TransactionDate DESC";
    $result=$db->queryDB($sql);

    if($result["jumdata"]>0){
        while ($row = mssql_fetch_assoc($result["result"])) {
            $resultcabang[]=$row;
        }
    }else{
        $resultcabang[]=array("TransactionCode"=>"0","ERPCode"=>"0","Perusahaan"=>"Data Kosong","TransactionDate"=>"0","Note"=>"0","Assigned"=>"0");
    }
}
else{
    $resultcabang[]=array("TransactionCode"=>"0","ERPCode"=>"0","Perusahaan"=>"Koneksi Server Terputus","TransactionDate"=>"0","Note"=>"0","Assigned"=>"0");
}
echo json_encode(array('operatortaskmoverbpb1'=>$resultcabang));