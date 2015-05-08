<?php
include "../setting/include.php";$cabang="01";

$db=new DB();$resultccabang=array();$koneksi=$db->connectDB($cabang);
$sopir=$_GET["sopir"];
if($koneksi["status"]){
    $sql="select Kode,Nama from collector where Aktif=1 and Kode='".$sopir."'";
    $result=$db->queryDB($sql);
    if($result["jumdata"]>0)
    {
        while ($row = mssql_fetch_assoc($result["result"])){
            $resultcabang[]=$row;
        }
    }else{
        $resultcabang[]=array("Kode"=>"0","Nama"=>"Username/Password Salah");
    }
}
else{
    $resultcabang[]=array("Kode"=>"0","Nama"=>"Koneksi DB Terputus");
}
echo json_encode(array('logindata'=>$resultcabang));