<?php
include "setting/include.php";$cabang="01";$bincode=$_GET["bincode"];$OperatorCode=$_GET["operatorcode"];$TransactionCode=$_GET["transactioncode"];
$db=new DB();$resultcabang=array();$koneksi=$db->connectDB($cabang);$jumlah=0;
if($koneksi["status"]){
    $sql = "select COUNT(*) as Jumlah from wms.bin where BinCode='" .$bincode. "'";$result=$db->queryDB($sql);
    if($result["jumdata"]>0){
        while ($row = mssql_fetch_assoc($result["result"])) {$jumlah=$row["Jumlah"];}
        if($jumlah==0){$resultcabang[]=array("Jumlah"=>"Bin Tidak Ada!");}else{
            $sql = "SELECT COUNT(*) as Jumlah FROM wms.BinSKU WHERE BinCode=" . $bincode . " AND Qty>0";$result=$db->queryDB($sql);
            if($result["jumdata"]>0){
                while($row = mssql_fetch_assoc($result["result"])){
                    $jumlah=$row["Jumlah"];
                }
                if($jumlah==0){
                    $sql = "select count(*) as Jumlah from wms.DetailTaskPck where Destbin=" . $bincode . " and TransactionCode<>'" . $TransactionCode . "' and User_1st='" . $OperatorCode . "' and User_2nd is null";$result=$db->queryDB($sql);
                    if($result["jumdata"]>0){
                        while($row = mssql_fetch_assoc($result["result"])){$jumlah=$row["Jumlah"];}
                        if($jumlah==0){$resultcabang[]=array("Jumlah"=>"1");}else{$resultcabang[]=array("Jumlah"=>"Bin Dipakai Operator Lain!");}
                    }else{$resultcabang[]=array("Jumlah"=>"Bin Dipakai Operator Lain!");}
                }else{$resultcabang[]=array("Jumlah"=>"Bin Ada Isinya!");}
            }else{$resultcabang[]=array("Jumlah"=>"Bin Tidak Ada!");}
        }
    }else{$resultcabang[]=array("Jumlah"=>"Bin Tidak Ada!");}
}
else{$resultcabang[]=array("Jumlah"=>"Koneksi DB Terputus");}
echo json_encode(array('operatortaskpickingpck1cekbin'=>$resultcabang));