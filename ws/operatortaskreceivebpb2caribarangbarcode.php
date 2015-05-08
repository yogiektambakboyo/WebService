<?php

include "setting/include.php";$cabang="01";$cari=$_GET["barcode"];$trcode=$_GET["transactioncode"];

$db=new DB();$resultccabang=array();$koneksi=$db->connectDB($cabang);

if($koneksi["status"]){
    $sql="select b.Kode,b.Keterangan
                from barang b
                inner join wms.DetailTaskDerp d
                on b.Kode=d.SKUcode
                where (b.ItemBarcode='".$cari."' or b.ShipperBarcode='".$cari."' or b.BundleBarcode='".$cari."') and d.TransactionCode='".$trcode."'";
    $result=$db->queryDB($sql);

    if($result["jumdata"]>0){
        while ($row = mssql_fetch_assoc($result["result"])) {$resultcabang[]=$row;}
    }else{$resultcabang[]=array("Kode"=>"0","Keterangan"=>"0");}
}
else{$resultcabang[]=array("Kode"=>"0","Keterangan"=>"Koneksi DB Terputus");}
echo json_encode(array('operatortaskreceivebpb2caribarangbarcode'=>$resultcabang));