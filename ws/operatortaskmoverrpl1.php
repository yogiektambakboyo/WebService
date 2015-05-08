<?php
/**
 * Created by IntelliJ IDEA.
 * User: IT-SOFT
 * Date: 6/19/14
 * Time: 10:04 AM
 * To change this template use File | Settings | File Templates.
 */


include "setting/include.php";
$cabang="01";
$operatorcode=$_GET["operatorcode"];

$db=new DB();
$resultccabang=array();
$koneksi=$db->connectDB($cabang);

if($koneksi["status"]){
    $sql="select mt.TransactionCode, ISNULL(mt.ERPCode,'0') as ERPCode, mt.TransactionDate,(case when o.Assigned is null then 2 else cast(o.Assigned as int) end) as Assigned
                from wms.MasterTaskRpl mt
                left join wms.DetailTaskOpr o
                on mt.TransactionCode=o.TransactionCode and o.OprRole='10/WHR/002' and o.OperatorCode='".$operatorcode."'
                where isFinish = '0' and isFinishMove = '0' and isCancel = '0'";
    $result=$db->queryDB($sql);

    if($result["jumdata"]>0){
        while ($row = mssql_fetch_assoc($result["result"])) {
            $resultcabang[]=$row;
        }
    }else{
        $resultcabang[]=array("TransactionCode"=>"0","ERPCode"=>"Data Kosong","TransactionDate"=>"0","Assigned"=>"0");
    }
}
else{
    $resultcabang[]=array("TransactionCode"=>"0","ERPCode"=>"Koneksi Server Terputus","TransactionDate"=>"0","Assigned"=>"0");
}
echo json_encode(array('operatortaskmoverrpl1'=>$resultcabang));