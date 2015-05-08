<?php
$a=$_POST["a"];
$b=$_POST["b"];
$c=$_POST["c"];
$kode=strrev($a)."/".$b."/".strrev($c);
session_start();
if (!isset($_SESSION["username"])) {
    header("location:index.php");
}

include 'include/include.php';

$db = new DB();
$koneksi = $db->connectDB("01");
$arr = array();
if ($koneksi["status"]) {
    $sql = "select o.brg,o.QtyPcs as pcs,o.QtyCrt as crt,b.keterangan
        from sfa_masterorder o
        inner join barang b on
        o.brg=b.kode
        where o.noorder='".$kode."'";
    $result = $db->queryDB($sql);
    if ($result["jumdata"] == 0) {
        $arr = array("status" => 0, "data" => "Data Kosong");
    }else{
        $hasil=array();
        while ($row = mssql_fetch_assoc($result["result"])) {
            $hasil[]=array("brg"=>$row["brg"],"keterangan"=>$row["keterangan"],"crt"=>$row["crt"],"pcs"=>$row["pcs"]);
        }
        $arr = array("status" => 1, "data" => $hasil);
    }
}else{
    $arr[] = array("status" => 0,"data" => "Koneksi Putus");
}
echo $_GET['jsoncallback'] . '(' . json_encode($arr) . ');';
?>
