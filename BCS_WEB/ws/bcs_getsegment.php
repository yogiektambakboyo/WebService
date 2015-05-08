<?php
include "../setting/include.php";$cabang=$_GET["cabang"];

$db=new DB();$resultccabang=array();$koneksi=$db->connectDB($cabang);$divisi=$_GET["divisi"];

if($koneksi["status"]){
    $sql="select Kode,Description as Keterangan from segmentCust where aktif=1 order by Description";
    $result=$db->queryDB($sql);
    if($result["jumdata"]>0){while ($row = mssql_fetch_assoc($result["result"])) {$resultcabang[]=$row;}}
    else{$resultcabang[]=array("Kode"=>"0","Keterangan"=>"Data Kosong");}
}
else{$resultcabang[]=array("Kode"=>"0","Keterangan"=>"Data Kosong");}
echo json_encode($resultcabang);