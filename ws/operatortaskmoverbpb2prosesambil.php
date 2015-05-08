<?php

include "setting/include.php";
$cabang="01";
$OperatorCode=$_GET["operatorcode"];
$TransactionCode=$_GET["transactioncode"];
$NoUrut=$_GET["nourut"];
$QueueNumber=$_GET["queuenumber"];

$db=new DB();
$resultccabang=array();
$koneksi=$db->connectDB($cabang);

if($koneksi["status"]){
    $sql = "UPDATE wms.DetailTaskRcvHistory SET User_1st='" . $OperatorCode . "',Time_1st=getdate() WHERE TransactionCode='" . $TransactionCode . "' AND NoUrut='" . $NoUrut . "' AND QueueNumber='" . $QueueNumber."'";
    if($db->executeDB($sql)){
        $resultcabang[]=array("Status"=>"1");
    }else{
        $resultcabang[]=array("Status"=>"0");
    }
}
else{
    $resultcabang[]=array("Status"=>"0");
}
echo json_encode(array('operatortaskmoverbpb2prosesambil'=>$resultcabang));