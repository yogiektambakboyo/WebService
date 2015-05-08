<?php
include "setting/include.php";$cabang="01";$cari=$_GET["kodebarang"];
$db=new DB();$resultccabang=array();$koneksi=$db->connectDB($cabang);

if($koneksi["status"]){
    $sql="select Satuan,Rasio from Satuan where Brg='".$cari."' and SatuanAktif=1";
    $result=$db->queryDB($sql);
    if($result["jumdata"]>0){
        while ($row = mssql_fetch_assoc($result["result"])) {$resultcabang[]=$row;}
    }else{$resultcabang[]=array("Satuan"=>"0","Rasio"=>"0");}
}
else{$resultcabang[]=array("Satuan"=>"Koneksi DB Terputus","Rasio"=>"0");}
echo json_encode(array('operatortaskreceivebpb2getsatuan'=>$resultcabang));
