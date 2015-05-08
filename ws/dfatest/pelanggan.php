<?php
include "../setting/include.php";$cabang="01";

$db=new DB();$resultccabang=array();$koneksi=$db->connectDB($cabang);

if($koneksi["status"]){
    $sql="select top 500 Kode,Perusahaan,ISNULL(Alamat,'') as Alamat from pelanggan where aktif=1 and kode like '01/01/%'";
    $result=$db->queryDB($sql);
    if($result["jumdata"]>0)
    {
        while ($row = mssql_fetch_assoc($result["result"])){
            $resultcabang[]=$row;
        }
    }else{
        $resultcabang[]=array("Kode"=>"0","Perusahaan"=>"Data Kosong","Alamat"=>"0");
    }
}
else{
    $resultcabang[]=array("Kode"=>"0","Perusahaan"=>"Koneksi DB Terputus","Alamat"=>"0");
}
echo json_encode(array('pelanggandata'=>$resultcabang));