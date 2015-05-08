<?php
include '../include/include.php';
$cabang="01";
$db=new DB();
$koneksi=$db->connectDB($cabang);

if($koneksi["status"]){
    $sql="select Version,LinkAddress,ISNULL(VersionDB,0) as VersionDB from SFA_VersionSFA where CreateDate=(select max(CreateDate) from SFA_VersionSFA)";

    $result=$db->queryDB($sql);
    $arr=array();
    $version=0;
    $versiondb=0;
    $link=null;

    if($result["jumdata"]>0){
        while ($row = mssql_fetch_assoc($result["result"])) {
            $version=$row["Version"];
            $link=$row["LinkAddress"];
            $versiondb=$row["VersionDB"];
        }
        $arr=array("status"=>1,"versi"=>$version,"link"=>$link,"versidb"=>$versiondb);
    }else{
        $arr=array("status"=>0,"versi"=>0,"link"=>" ","versidb"=>0);
    }
}
else{
    $arr=array("status"=>0,"versi"=>0,"link"=>" ","versidb"=>0);
}
echo $_GET['jsoncallback'] . '(' . json_encode($arr) . ');';
?>