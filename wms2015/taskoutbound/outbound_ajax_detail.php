<?php
include_once "../script/connectbon.php";

$kodenota=$_GET['id'];

$sql="select LineNumber,ProductCode,b.Keterangan,QtyOrder,StockStatusCode
      from outbound_Send s JOIN ".$_SESSION['dbase'].".dbo.Barang b on b.kode=s.ProductCode
      WHERE s.Reference=:kodenota";
$sth = $dbh->prepare($sql);
$sth->bindParam(':kodenota',$kodenota,PDO::PARAM_STR);
$result = $sth->execute();
$result=$sth->fetchAll();

$arrlistrcpt=null;$arrlistsend=null;

$i=0;
foreach ($result as $row) {
    $i++;
    $arrlistsend[]=array("No"=>$i,"LineNumber"=>$row["LineNumber"],"Keterangan"=>$row["Keterangan"],"ProductCode"=>$row["ProductCode"],"QtyOrder"=>$row["QtyOrder"],"StockStatusCode"=>$row["StockStatusCode"]);
}

$sql="select LineNumber,ProductCode,b.Keterangan,LotNumber,ExpDate,QtyShipped from outbound_Rcpt s
      JOIN ".$_SESSION['dbase'].".dbo.Barang b on b.kode=s.ProductCode
      WHERE s.Reference=:kodenota";
$sth = $dbh->prepare($sql);
$sth->bindParam(':kodenota',$kodenota,PDO::PARAM_STR);
$result = $sth->execute();
$result=$sth->fetchAll();

$j=0;
foreach ($result as $row) {
    $j++;
    $arrlistrcpt[]=array("No"=>$j,"LineNumber"=>$row["LineNumber"],"Keterangan"=>$row["Keterangan"],"ProductCode"=>$row["ProductCode"],"QtyShipped"=>$row["QtyShipped"],"ExpDate"=>$row["ExpDate"],"LotNumber"=>$row["LotNumber"]);
}

echo json_encode(array("csend"=>$i,"crcpt"=>$j,"rcpt"=>$arrlistrcpt,"send"=>$arrlistsend));
