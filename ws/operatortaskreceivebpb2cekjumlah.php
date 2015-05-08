<?php
include "setting/include.php";$cabang="01";$cari=$_GET["barcode"];$bincode=$_GET["bincode"];$jmlhin=$_GET["jumlahin"];$trcode=$_GET["transactioncode"];
$db=new DB();$jmlhDERP=0;$ratio=0;$jmlhRcv=0;$resultccabang=array();$koneksi=$db->connectDB($cabang);

if($koneksi["status"]){
    $sql="select Ratio*Qty as Jumlah,Ratio from wms.DetailTaskDerp where SKUCode='".$cari."' and TransactionCode='".$trcode."'";
    $result=$db->queryDB($sql);

    if($result["jumdata"]>0){
        while ($row = mssql_fetch_assoc($result["result"])) {$jmlhDERP=$row["Jumlah"];$ratio=$row["Ratio"];}

        $sql = "select sum(Qty) as Jumlah from wms.DetailTaskRcv where BinCode='".$bincode."' and SKUCode='".$cari."' and TransactionCode='".$trcode."'";
        $result=$db->queryDB($sql);

        while ($row = mssql_fetch_assoc($result["result"])) {$jmlhRcv=$row["Jumlah"];}

        if(($jmlhRcv+$jmlhin)>$jmlhDERP){
            $resultcabang[]=array("Status"=>"0","Jumlah"=>"Barang Kelebihan Sebanyak ".abs(($jmlhDERP-($jmlhRcv+$jmlhin))/$ratio)." CRT");
        }else{
            $resultcabang[]=array("Status"=>"1","Jumlah"=>"Berhasil menambahkan ".($jmlhin/$ratio)." CRT");
        }
    }else{$resultcabang[]=array("Status"=>"0","Jumlah"=>"0");}
}
else{
    $resultcabang[]=array("Status"=>"0","Jumlah"=>"Koneksi DB Terputus");
}
echo json_encode(array('operatortaskreceivebpb2cekjumlah'=>$resultcabang));