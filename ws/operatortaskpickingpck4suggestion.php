<?php
include "setting/include.php";$cabang="01";$SKUCode=$_GET["skucode"];$EDPanjang=$_GET["edpanjang"];$Qty=$_GET["qty"];$WHCode=$_GET["whcode"];
$db=new DB();$resultccabang=array();$koneksi=$db->connectDB($cabang);
if($koneksi["status"]){$sql = "exec wms.spPickingSuggestion '".$SKUCode."','".$EDPanjang."','".$Qty."','".$WHCode."'";$result=$db->queryDB($sql);
    if($result["jumdata"]>0){while ($row = mssql_fetch_assoc($result["result"])) {$resultcabang[]=$row;}
    }else{$resultcabang[]=array("RackSlotName"=>"0","RackSlotLvl"=>"0","Expdate"=>"0","RackSlotName"=>"0","Qty"=>"0","QtyText"=>"0","Priority"=>"0");}
}
else{$resultcabang[]=array("RackSlotName"=>"Koneksi DB Terputus","RackSlotLvl"=>"0","Expdate"=>"0","RackSlotName"=>"0","Qty"=>"0","QtyText"=>"0","Priority"=>"0");}
echo json_encode(array('operatortaskpickingpck4suggestion'=>$resultcabang));