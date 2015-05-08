<?php
session_start();
include_once "../script/connect.php";

$sql = "SELECT TOP 1 Cabang,CONVERT(CHAR(10), TglTransaksi, 120) as TglTransaksi,Convert(CHAR(10), DateAdd(day, -7, Convert(datetime, TglTransaksi)),120) as TglTransaksiAwal,Convert(CHAR(10), DateAdd(day, +1, Convert(datetime, TglTransaksi)),120) as TglTransaksiAkhir FROM kategori WHERE Cabang=:cabang";
$stg = $dbh->prepare($sql);
$stg->bindParam(':cabang',$_SESSION["divisi"],PDO::PARAM_STR);
$result = $stg->execute();
$result = $stg->fetchAll();
foreach ($result as $rows){
    $tglTransaksi = $rows["TglTransaksi"];
    $tglTransaksiAwal = $rows["TglTransaksiAwal"];
    $tglTransaksiAkhir = $rows["TglTransaksiAkhir"];
}
$_SESSION["TglTransaksi"]=$tglTransaksi;
$_SESSION["TglTransaksiAwal"]=$tglTransaksiAwal;
$_SESSION["TglTransaksiAkhir"]=$tglTransaksiAkhir;