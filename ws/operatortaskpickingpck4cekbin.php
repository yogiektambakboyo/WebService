<?php
include "setting/include.php";$cabang="01";$bincode=$_GET["bincode"];$OperatorCode=$_GET["operatorcode"];
$db=new DB();$resultcabang=array();$koneksi=$db->connectDB($cabang);$jumlah=0;
if($koneksi["status"]){
    $sql = "select COUNT(*) as Jumlah from wms.bin where BinCode='" .$bincode. "'";$result=$db->queryDB($sql);
    if($result["jumdata"]>0){
        while ($row = mssql_fetch_assoc($result["result"])) {$jumlah=$row["Jumlah"];}
        if($jumlah==0){$resultcabang[]=array("Jumlah"=>"Bin Tidak Ada!");}else{
            $sql = "SELECT COUNT(*) AS Jumlah FROM wms.DetailTaskPck WHERE User_1st='" . $OperatorCode . "' AND User_2nd IS NULL AND DestBin=" . $bincode;
            $result=$db->queryDB($sql);
            if($result["jumdata"]>0){
                while($row = mssql_fetch_assoc($result["result"])){$jumlah=$row["Jumlah"];}
                if($jumlah==0){$resultcabang[]=array("Jumlah"=>"Pembawa Bin Salah!");
                }else{
                    $sql = "SELECT COUNT(*) AS Jumlah FROM wms.DetailTaskPck
                            WHERE DestBin=" . $bincode . "
                            AND User_1st='" . $OperatorCode . "'
                            AND User_2nd IS NULL
                            AND TransactionCode IN
                            (select distinct m.TransactionCode
                            from wms.MasterTaskPck m,wms.DetailTaskOpr d
                            where m.isFinish=0 and m.isCancel=0 and m.isFinishMove=0 and d.TransactionCode=m.TransactionCode and d.OperatorCode='" . $OperatorCode . "')";
                    $result=$db->queryDB($sql);

                    if($result["jumdata"]>0){
                        while($row = mssql_fetch_assoc($result["result"])){$jumlah=$row["Jumlah"];}
                        if($jumlah==0){$resultcabang[]=array("Jumlah"=>"Bin Masih Kosong!");}else{$resultcabang[]=array("Jumlah"=>"1");}
                    }else{$resultcabang[]=array("Jumlah"=>"Bin Masih Kosong!");}
                }
            }else{$resultcabang[]=array("Jumlah"=>"Pembawa Bin Salah!");}
        }
    }else{$resultcabang[]=array("Jumlah"=>"Bin Tidak Ada!");}
}
else{$resultcabang[]=array("Jumlah"=>"Koneksi DB Terputus");}
echo json_encode(array('operatortaskpickingpck4cekbin'=>$resultcabang));