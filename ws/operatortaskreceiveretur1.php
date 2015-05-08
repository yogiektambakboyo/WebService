<?php
/**
 * Created by IntelliJ IDEA.
 * User: IT-SOFT
 * Date: 6/18/14
 * Time: 2:55 PM
 * To change this template use File | Settings | File Templates.
 */


include "setting/include.php";
$cabang="01";
$operatorcode=$_GET["operatorcode"];

$db=new DB();
$resultccabang=array();
$koneksi=$db->connectDB($cabang);

if($koneksi["status"]){
    $sql="SELECT mt.TransactionCode as TransactionCode,mt.ERPCode as ERPCode,mt.TransactionDate as TransactionDate,(case when o.Assigned is null then 2 else cast(o.Assigned as int) end) as Assigned
                FROM wms.MasterTaskRcv mt
                left join wms.DetailTaskOpr o
                on o.TransactionCode=mt.TransactionCode and o.OprRole='10/WHR/001' and o.OperatorCode='".$operatorcode."'
                WHERE
                mt.isFinish=0 AND mt.isCancel=0 AND mt.isFinishMove=0 AND mt.ProjectCode='RJT'
                ORDER BY mt.TransactionDate DESC";
    $result=$db->queryDB($sql);

    if($result["jumdata"]>0){
        while ($row = mssql_fetch_assoc($result["result"])) {
            $resultcabang[]=$row;
        }
    }else{
        $resultcabang[]=array("TransactionCode"=>"0","ERPCode"=>"0","Perusahaan"=>"Data Kosong","TransactionDate"=>"0","Assigned"=>"0");
    }
}
else{
    $resultcabang[]=array("TransactionCode"=>"0","ERPCode"=>"0","Perusahaan"=>"Koneksi Server Terputus","TransactionDate"=>"0","Assigned"=>"0");
}
echo json_encode(array('operatortaskreceiveretur1'=>$resultcabang));