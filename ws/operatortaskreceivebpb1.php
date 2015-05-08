<?php

include "setting/include.php";$cabang="01";$operatorcode=$_GET["operatorcode"];

$db=new DB();$resultccabang=array();$koneksi=$db->connectDB($cabang);

if($koneksi["status"]){
    $sql="select t.TransactionCode,t.ERPCode as KodeNota,(case when t.ProjectCode='BPB' then (select s.perusahaan from supplier s,masterbeli m where m.supplier=s.kode and m.kodenota=t.ERPCode ) else
       (select keterangan from gudang where kode=(select max(d.AsalGudang) from mastertransfer m,detailtransfer d where m.kodenota=d.kodenota and m.kodenota=t.ERPCode)) end) as Perusahaan,
       t.Note,(case when o.Assigned is null then 2 else cast(o.Assigned as int) end) as Assigned
from wms.masterTaskRcv t
left join wms.DetailTaskOpr o on o.TransactionCode=t.TransactionCode and o.OprRole='10/WHR/001' and o.OperatorCode='".$operatorcode."'
where t.isFinish='0' and t.isFinishMove='0' and t.isCancel='0' and (t.ProjectCode='BPB' or t.ProjectCode='RTS')";
    $result=$db->queryDB($sql);
    if($result["jumdata"]>0){
        while ($row = mssql_fetch_assoc($result["result"])) {$resultcabang[]=$row;}
    }else{$resultcabang[]=array("TransactionCode"=>"0","KodeNota"=>"0","Perusahaan"=>"Data Kosong","Note"=>"0","Assigned"=>"0");}
}
else{$resultcabang[]=array("TransactionCode"=>"0","KodeNota"=>"0","Perusahaan"=>"Koneksi Server Terputus","Note"=>"0","Assigned"=>"0");}
echo json_encode(array('operatortaskreceivebpb1'=>$resultcabang));