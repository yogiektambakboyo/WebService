<?php
include "setting/include.php";$cabang="01";$bincode=$_GET["bincode"];$OperatorCode=$_GET["operatorcode"];$koderak=$_GET["rackcode"];$SKUCode=$_GET["skucode"];
$db=new DB();$resultcabang=array();$koneksi=$db->connectDB($cabang);$jumlah=0;
if($koneksi["status"]){
    $sql = "select COUNT(*) as Jumlah from wms.bin where BinCode='" .$bincode. "'";$result=$db->queryDB($sql);
    if($result["jumdata"]>0){
        while ($row = mssql_fetch_assoc($result["result"])) {$jumlah=$row["Jumlah"];}
        if($jumlah==0){$resultcabang[]=array("Jumlah"=>"Bin Tidak Ada!");}else{
            $sql = "SELECT COUNT(*) as Jumlah FROM wms.BinSKU WHERE BinCode=" . $bincode . " AND Qty>0 AND SKUCode='" . $SKUCode . "'";$result=$db->queryDB($sql);
            if($result["jumdata"]>0){
                while($row = mssql_fetch_assoc($result["result"])){$jumlah=$row["Jumlah"];}
                if($jumlah==0){$resultcabang[]=array("Jumlah"=>"Bin Kosong!");}else{
                    $sql = "SELECT COUNT(*) as Jumlah FROM wms.Bin WHERE BinCode=" . $bincode . " AND RackSlotCode='" . $koderak . "'";$result=$db->queryDB($sql);
                    if($result["jumdata"]>0){
                        while($row = mssql_fetch_assoc($result["result"])){$jumlah=$row["Jumlah"];}
                        if($jumlah==0){$resultcabang[]=array("Jumlah"=>"Bin di Rak yang Salah!");}else{$resultcabang[]=array("Jumlah"=>"1");}
                    }else{$resultcabang[]=array("Jumlah"=>"Bin di Rak yang Salah!");}
                }
            }else{$resultcabang[]=array("Jumlah"=>"Bin Tidak Ada!");}
        }
    }else{$resultcabang[]=array("Jumlah"=>"Bin Tidak Ada!");}
}
else{$resultcabang[]=array("Jumlah"=>"Koneksi DB Terputus");}
echo json_encode(array('operatortaskpickingpckambilcekbin'=>$resultcabang));