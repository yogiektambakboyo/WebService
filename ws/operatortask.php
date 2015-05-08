<?php
include "setting/include.php";$cabang="01";$task=$_GET["task"];

$db=new DB();$resultccabang=array();$koneksi=$db->connectDB($cabang);

if($koneksi["status"]){
    $sql="select ProjectCode,WHRoleCode,NameLinkAddress from wms.ProjectWHRole where WHRoleCode='".$task."' and LinkAddress IS NOT NULL";$result=$db->queryDB($sql);
    if($result["jumdata"]>0){while ($row = mssql_fetch_assoc($result["result"])) {$resultcabang[]=$row;}}else{$resultcabang[]=array("ProjectCode"=>"0","WHRoleCode"=>"0","NameLinkAddress"=>"Data Kosong");}
}
else{$resultcabang[]=array("ProjectCode"=>"0","WHRoleCode"=>"0","NameLinkAddress"=>"Koneksi Server Terputus");}
echo json_encode(array('operatortask'=>$resultcabang));