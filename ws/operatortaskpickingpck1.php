<?php
/**
 * Created by IntelliJ IDEA.
 * User: IT-SOFT
 * Date: 6/19/14
 * Time: 11:47 AM
 * To change this template use File | Settings | File Templates.
 */


include "setting/include.php";
$cabang="01";
$operatorcode=$_GET["operatorcode"];

$db=new DB();
$resultccabang=array();
$koneksi=$db->connectDB($cabang);

if($koneksi["status"]){
    $sql="SELECT mt.TransactionCode as TransactionCode,mt.ERPCode as ERPCode,mt.TransactionDate as TransactionDate ,WHCode as WHCode,EDPanjang as EDPanjang,(case when o.Assigned is null then 2 else cast(o.Assigned as int) end) as Assigned
                FROM wms.MasterTaskPck mt
                left join wms.DetailTaskOpr o
                on mt.TransactionCode=o.TransactionCode and o.OprRole='10/WHR/003' and o.OperatorCode='".$operatorcode."'
                WHERE
                mt.isFinish=0 AND mt.isCancel=0 AND mt.isFinishMove=0 AND (mt.ProjectCode='PCK' or mt.ProjectCode='PTS')
                ORDER BY mt.TransactionDate DESC";
    $result=$db->queryDB($sql);

    if($result["jumdata"]>0){
        while ($row = mssql_fetch_assoc($result["result"])) {
            $resultcabang[]=$row;
        }
    }else{
        $resultcabang[]=array("TransactionCode"=>"0","ERPCode"=>"Data Kosong","TransactionDate"=>"0","Assigned"=>"0","WHCode"=>"0","EDPanjang"=>"0");
    }
}
else{
    $resultcabang[]=array("TransactionCode"=>"0","ERPCode"=>"Koneksi Server Terputus","TransactionDate"=>"0","Assigned"=>"0","WHCode"=>"0","EDPanjang"=>"0");
}
echo json_encode(array('operatortaskpickingpck1'=>$resultcabang));