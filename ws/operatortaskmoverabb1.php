<?php
/**
 * Created by IntelliJ IDEA.
 * User: IT-SOFT
 * Date: 6/18/14
 * Time: 3:49 PM
 * To change this template use File | Settings | File Templates.
 */

include "setting/include.php";
$cabang="01";

$db=new DB();
$resultccabang=array();
$koneksi=$db->connectDB($cabang);

if($koneksi["status"]){
    $sql="SELECT a.TransactionCode, a.SKUCode, a.Qty, b.ERPCode, c.BinCode, c.DestRackSlot, dbo.KonversiSatuanToText(a.SKUCode,a.Qty) as qtyKonv, c.NoUrut,c.ExpDate, a.qty-a.pick as selisih, dbo.KonversiSatuanToText(a.SKUCode,a.qty-a.pick)as selisihKonv, d.Keterangan,c.destBin
		FROM wms.DetailTaskRcvOver a
		left join wms.MasterTaskRcv b
		on a.TransactionCode = b.TransactionCode
		left join wms.detailTaskRcv c
		on a.TransactionCode = c.TransactionCode
		left join barang d
		on a.SKUCode = d.Kode
		where (a.Qty-a.Pick) > 0 and c.Status = '1' and c.Status2 ='1' and c.NoUrut = '2'";
    $result=$db->queryDB($sql);

    if($result["jumdata"]>0){
        while ($row = mssql_fetch_assoc($result["result"])) {
            $resultcabang[]=$row;
        }
    }else{
        $resultcabang[]=array("TransactionCode"=>"0","SKUCode"=>"0","Qty"=>"0","ERPCode"=>"Data Kosong","BinCode"=>"0","DestRackSlot"=>"0","qtyKonv"=>"0","NoUrut"=>"0","ExpDate"=>"0","selisih"=>"0","selisihKonv"=>"0","Keterangan"=>"0","destBin"=>"0");
    }
}
else{
    $resultcabang[]=array("TransactionCode"=>"0","SKUCode"=>"0","Qty"=>"0","ERPCode"=>"Koneksi Server Terputus","BinCode"=>"0","DestRackSlot"=>"0","qtyKonv"=>"0","NoUrut"=>"0","ExpDate"=>"0","selisih"=>"0","selisihKonv"=>"0","Keterangan"=>"0","destBin"=>"0");
}
echo json_encode(array('operatortaskmoverabb1'=>$resultcabang));