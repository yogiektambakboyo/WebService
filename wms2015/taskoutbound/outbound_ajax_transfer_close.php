<?php
include_once "../script/connectbon.php";
//$sql="EXECUTE SP_XXXMSSQLSTOREDPROCEDURE :kodenota";
$sql= "select m.Kodenota,d.Brg,b.Keterangan,CONVERT(int,(d.Jml*d.Rasio)) as QtyGBS,ISNULL(r.QtyShipped,0) as QtyWMS,CONVERT(int,((d.Jml*d.Rasio)-(ISNULL(r.QtyShipped,0)))) as QtyDiff from dbo.Outbound_Master m
join ".$_SESSION['dbase'].".dbo.DetailTransfer d on d.kodenota=m.kodenota
join ".$_SESSION['dbase'].".dbo.Barang b on b.Kode=d.Brg
left join dbo.Outbound_Rcpt r on r.reference=d.kodenota and r.ProductCode=d.Brg
WHERE m.kodenota=:kodenota and isClosed=0 and isHaveRcpt=1 and ((d.Jml*d.Rasio)-(ISNULL(r.QtyShipped,0)))>0";
$sth = $dbh->prepare($sql);
$kodenota = $_GET["id"];
$arrlistclose=null;
$sth->bindParam(':kodenota',$kodenota,PDO::PARAM_STR);
$result = $sth->execute();
$result = $sth->fetchAll();
$i=0;
foreach ($result as $row) {
    $i++;
    $arrlistclose[]=array("No"=>$i,"Kodenota"=>$row["Kodenota"],"Brg"=>$row["Brg"],"Keterangan"=>$row["Keterangan"],"QtyGBS"=>$row["QtyGBS"],"QtyWMS"=>$row["QtyWMS"],"QtyDiff"=>$row["QtyDiff"]);
}
echo json_encode(array("counter"=>count($result),"data"=>$arrlistclose));
