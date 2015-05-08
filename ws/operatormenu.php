<?php
include "setting/include.php";$cabang="01";$username=$_GET["username"];
$db=new DB();$resultccabang=array();$koneksi=$db->connectDB($cabang);
if($koneksi["status"]){
    $sql="select o.OperatorCode,p.WHRoleCode,r.Name from wms.operator o JOIN wms.OperatorWHRole p ON p.OperatorCode=o.OperatorCode JOIN wms.WHRole r ON r.WHRoleCode=p.WHRoleCode where o.OperatorCode='".$username."'";
    $result=$db->queryDB($sql);
    if($result["jumdata"]>0){
        while ($row = mssql_fetch_assoc($result["result"])) {$resultcabang[]=$row;}
    }else{$resultcabang[]=array("OperatorCode"=>"0","WHRoleCode"=>"0","Name"=>"Data Kosong");}
}
else{$resultcabang[]=array("OperatorCode"=>"0","WHRoleCode"=>"0","Name"=>"Koneksi Server Terputus");}
echo json_encode(array('operatormenu'=>$resultcabang));



