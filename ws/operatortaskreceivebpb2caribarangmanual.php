<?php

include "setting/include.php";$cabang="01";$cari=$_GET["katakunci"];$trcode=$_GET["transactioncode"];
$db=new DB();$resultccabang=array();$koneksi=$db->connectDB($cabang);

if($koneksi["status"]){
    $sql = "select b.Kode,b.Keterangan
                from wms.DetailTaskDERP dd
                inner join
                barang b
                on b.Kode=dd.SKUCode
                inner join wms.MasterTaskRcv m
                on m.TransactionCode=dd.TransactionCode
                where dd.TransactionCode='".$trcode."' and
                    (dd.Qty*dd.Ratio)>(select isnull(SUM(DestQty),0) from wms.DetailTaskRcv d
                    inner join wms.BinImaginer i
                    on d.BinCode=i.ReceiveSource where TransactionCode='".$trcode."' and SKUCode=dd.SKUCode)
                    and
                    (b.ItemBarcode='".$cari."'
                        or b.ShipperBarcode='".$cari."'
                            or b.BundleBarcode='".$cari."'
                                or b.keterangan like '%".$cari."%')";
    $result=$db->queryDB($sql);

    if($result["jumdata"]>0){
        while ($row = mssql_fetch_assoc($result["result"])) {$resultcabang[]=$row;}
    }else{$resultcabang[]=array("Kode"=>"0","Keterangan"=>"0");}
}
else{$resultcabang[]=array("Kode"=>"0","Keterangan"=>"Koneksi DB Terputus");}
echo json_encode(array('operatortaskreceivebpb2caribarangmanual'=>$resultcabang));