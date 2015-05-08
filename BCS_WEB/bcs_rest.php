<?php
session_start();
if ((!isset($_SESSION["usernamebcs"]))||(!isset($_SESSION["jabatanbcs"]))) {
    header("location:login.php");
}
include "setting/include.php";$cabang="30";$db=new DB();$resultcabang=array();$koneksi=$db->connectDB($cabang);$cbg=$_GET["cabang"];$orderby=$_GET["jtSorting"];$recordCount=0;$startidx=(int)$_GET["jtStartIndex"];$pagesize=(int)$_GET["jtPageSize"];

try{
    if($_GET["act"] == "list"){
        if($koneksi["status"]){
            $sql="SELECT * FROM (SELECT ROW_NUMBER() OVER (ORDER BY ".$orderby.") AS Row, * FROM BCS_PelangganCabang WHERE KodeCabang like '".$_SESSION["cabangbcs"]."/%') AS BCS_PelangganCabang WHERE Perusahaan like '%".$_POST["Perusahaan"]."%' AND (CreateDate between '".$_POST["CreateDateAwal"]."' AND '".$_POST["CreateDateAkhir"]."') AND Row>".$startidx." AND Row <=".($pagesize+$startidx);
            $result=$db->queryDB($sql);
            if($result["jumdata"]>0){while ($row = mssql_fetch_assoc($result["result"])) {$resultcabang[]=$row;$recordCount=$row["Row"];}}
            else{$resultcabang[]=array();}
        }
        else{$resultcabang[]=array();}
        $jTableResult = array();$jTableResult['Result'] = "OK";$jTableResult['Records'] = $resultcabang;$jTableResult['TotalRecordCount'] = $recordCount;
        print json_encode($jTableResult);
    }
    if($_GET["act"] == "gawejejak" ){
        $_SESSION["cabangbcs"]= $cbg;
    }

    if($_GET["act"] == "listpelangganlama"){
        $db2= new DB();
        $koneksi=$db2->connectDB($_SESSION["cabangbcs"]);
        if($koneksi["status"]){
            $sql="SELECT Kode,Perusahaan,Penghubung,Segment,Alamat,Longitude,Latitude FROM PELANGGAN WHERE KODE='".$_GET["kode"]."'";
            $result=$db->queryDB($sql);
            if($result["jumdata"]>0){while ($row = mssql_fetch_assoc($result["result"])) {$resultcabang[]=$row;$recordCount=$row["Row"];}}
            else{$resultcabang[]=array();}
        }
        else{$resultcabang[]=array();}
        $jTableResult = array();$jTableResult['Result'] = "OK";$jTableResult['Records'] = $resultcabang;$jTableResult['TotalRecordCount'] = $recordCount;
        print json_encode($jTableResult);
    }
}catch(Exception $ex)
{
    //Return error message
    $jTableResult = array();
    $jTableResult['Result'] = "ERROR";
    $jTableResult['Message'] = $ex->getMessage();
    print json_encode($jTableResult);
}