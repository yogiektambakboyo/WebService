

<?php

include "setting/include.php";
$cabang="01";
$kodebindest=$_GET["kodebindest"];
$kodebin=$_GET["kodebin"];
$koderack=$_GET["koderack"];
$Qty=$_GET["qty"];
$IsOnAisle=$_GET["gang"];
$OperatorCode=$_GET["operatorcode"];
$TransactionCode=$_GET["transactioncode"];
$NoUrut=$_GET["nourut"];
$QueueNumber=$_GET["queuenumber"];
$jumlah=0;
$gang=1;

$db=new DB();
$resultccabang=array();
$koneksi=$db->connectDB($cabang);

if($IsOnAisle=="Tidak"){
   $gang=0;
}else{
   $gang=1;
}

if($koneksi["status"]){
    if($IsOnAisle=="Tidak"){
        $sql = "UPDATE wms.DetailTaskRcv SET DestRackSlot='" . $koderack . "',DestOnAisle='".$gang."'
                    WHERE BinCode='" . $kodebin . "' AND TransactionCode='" . $TransactionCode . "'
                    AND NoUrut='" . $NoUrut . "'";

        if($db->executeDB($sql)){
            $resultcabang[]=array("Status"=>"1");
        }else{
            $resultcabang[]=array("Status"=>"0");
        }
    } else{
        $sql = "UPDATE wms.DetailTaskRcv SET DestRackSlot='".$koderack."',DestOnAisle='".$gang."' WHERE BinCode=".$kodebin." AND TransactionCode='".$TransactionCode."' AND NoUrut='".$NoUrut."'";
        if($db->executeDB($sql)){
            $resultcabang[]=array("Status"=>"1");
        }else{
            $resultcabang[]=array("Status"=>"0");
        }
    }
    $sql = "UPDATE wms.DetailTaskRcvHistory SET User_2nd='".$OperatorCode."',DestRackSlot=".$koderack.",
                        DestQty=".$Qty.",
                        DestOnAisle=".$gang.",
                        DestBin=".$kodebindest.",
                        Time_2nd=getdate()
                        WHERE TransactionCode='".$TransactionCode."'
                        AND QueueNumber=".$QueueNumber." AND NoUrut='".$NoUrut."'";
    if($db->executeDB($sql)){
        $resultcabang[]=array("Status"=>"1");
    }else{
        $resultcabang[]=array("Status"=>"0");
    }
}
else{
    $resultcabang[]=array("Status"=>"Koneksi DB Terputus");
}
echo json_encode(array('operatortaskmoverbpb2updatebinsku'=>$resultcabang));