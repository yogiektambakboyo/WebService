<?php
$sales=strrev($_POST["sales"]);
$tgl=$_POST["tgl"];
session_start();
if (!isset($_SESSION["username"])) {
    header("location:index.php");
}

include 'include/include.php';

$db = new DB();
$koneksi = $db->connectDB("01");
$arr = array();
if ($koneksi["status"]) {
    $sql = "select d.Keterangan as divisi,m.sales,s.nama,m.noorder,m.shipto+' ('+p.Perusahaan+')' as shipto,CASE WHEN f.orderno IS NULL THEN '0' ELSE '1' END as stat from sfa_masterorder m
            join sfa_divisi d on d.Kode=substring(m.sales,4,2)
            left join sfa_salesperson s on s.kode=m.sales
            left join pelanggan p on p.kode=m.shipto
            left join (select distinct OrderNo,SalesCode,RetailerCode from BCP_SFAOrder where REPLACE(SalesCode,'/','')='".$sales."') f on f.OrderNo=m.NoOrder and f.SalesCode=m.sales
            where REPLACE(m.sales,'/','')='".$sales."' and tgl='".$tgl."' group by d.keterangan,m.sales,s.Nama,m.NoOrder,m.ShipTo,p.Perusahaan,f.orderno";
    $result = $db->queryDB($sql);
    if ($result["jumdata"] == 0) {
        $arr = array("status" => 0, "data" => "Data Kosong");
    }else{
        $hasil=array();
        while ($row = mssql_fetch_assoc($result["result"])) {
            $hasil[]=array("divisi"=>$row["divisi"],"sales"=>$row["sales"],"nama"=>$row["nama"],"noorder"=>$row["noorder"],"shipto"=>$row["shipto"],"stat"=>$row["stat"]);
        }
        $arr = array("status" => 1, "data" => $hasil);
    }
}else{
    $arr[] = array("status" => 0,"data" => "Koneksi Putus");
}
echo $_GET['jsoncallback'] . '(' . json_encode($arr) . ');';
?>
