<?php
include "../setting/include.php";$cabang="00";$db=new DB();$resultccabang=array();$koneksi=$db->connectDB($cabang);
$sopir=$_GET["sopir"];$code=$_GET["securecode"];$password=$_GET["pass"];

if ($koneksi["status"]) {
    $sql = "select * from master..syslogins where name='".$sopir."' and pwdCompare ('".$password."',password,0) = 1" ;
    $result = $db->queryDB($sql);
    if($result["jumdata"]>0){
        $sql = "select s.Kode,s.Nama from staff s join BCP_DFASecureCode b on b.Sopir=s.Nama where s.nama='".$sopir."' and s.jabatan in ('CHECKER DFA') and b.DeviceID='".$code."'" ;
        $result = $db->queryDB($sql);
        if($result["jumdata"]>0){
            while ($row = mssql_fetch_assoc($result["result"])) {
                $resultcabang[]=$row;
            }}
        else{
            $resultcabang[]=array("Kode"=>"0","Nama"=>"Username/Password Tidak Aktif");
        }
    }
    else{
        $resultcabang[]=array("Kode"=>"0","Nama"=>"Username/Password Salah");
    }
}
else{
    $resultcabang[]=array("Kode"=>"0","Nama"=>"Koneksi DB Terputus");
}

echo json_encode(array('logindata'=>$resultcabang));