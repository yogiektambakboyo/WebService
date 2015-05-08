<?php
include "setting/include.php";$cabang="01";$username=$_GET["username"];$password=$_GET["password"];$db=new DB();$resultcabang=array();$koneksi=$db->connectDB($cabang);
if($koneksi["status"]){
    $sql="select TOP 1 COUNT(1) AS jumlah,OperatorCode,Name,o.SiteId,b.ReceiveSource,b.ReceiveProblem,b.ShippingProblem,b.ReplenishProblem from wms.Operator o JOIN wms.BinImaginer b ON b.SiteID=o.SiteID WHERE OperatorCode='".$username."' and Password='".$password."' GROUP BY OperatorCode,Name,o.SiteID,b.ReceiveSource,b.ReceiveProblem,b.ShippingProblem,b.ReplenishProblem";
    $result=$db->queryDB($sql);
    if($result["jumdata"]>0){while ($row = mssql_fetch_assoc($result["result"])) {$resultcabang[]=$row;}
    }else{$resultcabang[]=array("jumlah"=>"0","OperatorCode"=>"Username/Password Salah","Name"=>"0","SiteId"=>"0","ReceiveSource"=>"0","ReceiveProblem"=>"0","ShippingProblem"=>"0","ReplenishProblem"=>"0");}
}
else{$resultcabang[]=array("jumlah"=>"0","OperatorCode"=>"Koneksi Server Terputus","Name"=>"0","SiteId"=>"0","ReceiveSource"=>"0","ReceiveProblem"=>"0","ShippingProblem"=>"0","ReplenishProblem"=>"0");}
echo json_encode(array('login'=>$resultcabang));
