<?php
$OperatorCode=$_GET["operatorcode"];

include "setting/include.php";
$cabang="01";
$db=new DB();
$resultccabang=array();
$koneksi=$db->connectDB($cabang);
$jumlah=0;

if($koneksi["status"]){
    //Cek Picking
    $sql = "select count(*) as Jumlah from wms.DetailTaskPck where User_1st='" . $OperatorCode . "'
            and User_2nd is null";
    $result = $db->queryDB($sql);

    if($result["jumdata"]>0){
        while ($row = mssql_fetch_assoc($result["result"])) {
            $jumlah=$row["Jumlah"];
        }
        if($jumlah>0){
            $resultcabang[0]["StatusPCK"]="1";
        }else{
            $resultcabang[0]["StatusPCK"]="0";
        }
    }else{
        $resultcabang[0]["StatusPCK"]="0";
    }

    //Cek Receiving
    $sql = "select count(*) as Jumlah from wms.DetailTaskRcvHistory where User_1st='" . $OperatorCode . "' and User_2nd is null";
    $result = $db->queryDB($sql);

    if($result["jumdata"]>0){
        while ($row = mssql_fetch_assoc($result["result"])) {
            $jumlah=$row["Jumlah"];
        }
        if($jumlah>0){
            $resultcabang[0]["StatusRCV"]="1";
        }else{
            $resultcabang[0]["StatusRCV"]="0";
        }
    }else{
        $resultcabang[0]["StatusRCV"]="0";
    }

    //Cek Replenish
    $sql = "select count(*) as Jumlah
                        from wms.DetailTaskRplHistory d
                        inner join wms.MasterTaskRpl m
                        on m.TransactionCode=d.TransactionCode
                        inner join wms.OperatorWHRole o
                        on m.CreateUserId=o.OperatorCode
                        where User_1st='" . $OperatorCode . "' and User_2nd is null and o.WHRoleCode='10/WHR/000'";
    $result = $db->queryDB($sql);

    if($result["jumdata"]>0){
        while ($row = mssql_fetch_assoc($result["result"])) {
            $jumlah=$row["Jumlah"];
        }
        if($jumlah>0){
            $resultcabang[0]["StatusRPL"]="1";
        }else{
            $resultcabang[0]["StatusRPL"]="0";
        }
    }else{
        $resultcabang[0]["StatusRPL"]="0";
    }

    // Cek Picking SPG
    $sql = "select count(*) as Jumlah
                        from wms.MasterTaskRpl
                        where CreateUserId='" . $OperatorCode . "' and isFinish=0 and isFinishMove=0";
    if($result["jumdata"]>0){
        while ($row = mssql_fetch_assoc($result["result"])) {
            $jumlah=$row["Jumlah"];
        }
        if($jumlah>0){
            $resultcabang[0]["StatusRPLManual"]="1";
        }else{
            $resultcabang[0]["StatusRPLManual"]="0";
        }
    }else{
        $resultcabang[0]["StatusRPLManual"]="0";
    }

    //Cek SPG
    $sql = "select count(*) as Jumlah from wms.DetailTaskPckS where User_1st='" . $OperatorCode . "' and DestBin is null";
    if($result["jumdata"]>0){
        while ($row = mssql_fetch_assoc($result["result"])) {
            $jumlah=$row["Jumlah"];
        }
        if($jumlah>0){
            $resultcabang[0]["StatusSPG"]="1";
        }else{
            $resultcabang[0]["StatusSPG"]="0";
        }
    }else{
        $resultcabang[0]["StatusSPG"]="0";
    }
}
else{
   $resultcabang[]=array("Status"=>"Koneksi DB Terputus");
}
echo json_encode(array('operatoroutstanding'=>$resultcabang));
