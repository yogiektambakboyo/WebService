<?php
include "../setting/include.php";$cabang="00";

$db=new DB();$resultccabang=array();$koneksi=$db->connectDB($cabang);$barcode=$_GET["sku"];
if(strlen($barcode)<1){
    $barcode = "(Yogi Aditya)";
}

if($koneksi["status"]){
    $sql="select Kode,Keterangan from barang where aktif=1 and (ItemBarcode='".$barcode."' or ShipperBarcode='".$barcode."' or BundleBarcode='".$barcode."')";
    $result=$db->queryDB($sql);
    if($result["jumdata"]>0){while ($row = mssql_fetch_assoc($result["result"])) {$resultcabang[]=$row;}}
    else{$resultcabang[]=array("Kode"=>"0","Keterangan"=>"Data Kosong");}
}
else{$resultcabang[]=array("Kode"=>"0","Keterangan"=>"Data Kosong");}
echo json_encode($resultcabang);