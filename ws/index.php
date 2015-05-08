<?php
// SQL Koneksi
include "setting/include.php";

$db = new DB();
$koneksi = $db->connectDB("01");
$err=array();
$resultcabang=array();
if ($koneksi["status"]) {
    $sql = "use bcp";
    if($db->executeDB($sql)){
        echo "Koneksi OK";
    }else{
        echo "DB Ganguan";
    }
}
else{
    echo "Koneksi Pusat Putus";
}
