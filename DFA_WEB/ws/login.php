<?php
include "../setting/include.php";$cabang="00";$db=new DB();$resultccabang=array();$koneksi=$db->connectDB($cabang);
$sopir=$_GET["sopir"];$code=$_GET["securecode"];
if($koneksi["status"]){
    $sql="select c.Kode,c.Nama,b.DeviceID from collector c join BCP_DFASecureCode b on b.Sopir=c.Kode where c.Aktif=1 and c.Kode='".$sopir."' and b.DeviceID='".$code."'";$result=$db->queryDB($sql);
    if($result["jumdata"]>0)
    {while ($row = mssql_fetch_assoc($result["result"])){$resultcabang[]=$row;}
    }else{$resultcabang[]=array("Kode"=>"0","Nama"=>"Username/Password Salah");}
}
else{$resultcabang[]=array("Kode"=>"0","Nama"=>"Koneksi DB Terputus");}
echo json_encode(array('logindata'=>$resultcabang));