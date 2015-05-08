

<?php

include "setting/include.php";
$cabang="01";
$kodebindest=$_GET["kodebindest"];
$kodebin=$_GET["kodebin"];
$SKUCode=$_GET["skucode"];
$TransactionCode=$_GET["transactioncode"];
$NoUrut=$_GET["nourut"];

$db=new DB();
$resultccabang=array();
$koneksi=$db->connectDB($cabang);

if($koneksi["status"]){
    $sql = "SELECT COUNT(*) AS Jumlah
                FROM wms.BinSKU
                WHERE
                BinCode=" . $kodebindest . "
                AND SKUCode='" . $SKUcode . "'
                AND ExpDate=
                (SELECT ExpDate FROM wms.DetailTaskRcv WHERE TransactionCode='" . $TransactionCode . "' AND NoUrut='" . $NoUrut . "')";
    $result=$db->queryDB($sql);

    if($result["jumdata"]>0){
        while ($row = mssql_fetch_assoc($result["result"])) {
            $resultcabang[]=$row;
        }

        $sql = "SELECT COUNT(*) AS jumlah
                FROM wms.Bin ba,wms.Bin bb
                WHERE ba.BinCode=" . $kodebindest . "
                AND bb.BinCode=" . $kodebin . "
                AND ba.WHCode=bb.WHCode";

        $result=$db->queryDB($sql);

        if($result["jumdata"]>0){
            $resultcabang[]=array("Status"=>"1");
        }else{
            $resultcabang[]=array("Status"=>"0");
        }

    }else{
        $sql = "  select count(*) as jumlah from wms.BinSKU where BinCode=" . $kodebindest." and Qty>0";

        $result=$db->queryDB($sql);

        if($result["jumdata"]>0){
            $resultcabang[]=array("Status"=>"1");
        }else{
            $resultcabang[]=array("Status"=>"0");
        }
    }
}
else{
    $resultcabang[]=array("Status"=>"Koneksi DB Terputus");
}
echo json_encode(array('operatortaskmoverbpb2cekskubindest'=>$resultcabang));