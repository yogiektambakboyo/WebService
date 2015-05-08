<?php
include_once "../script/connect.php";
$sql="EXECUTE SP_XXXMSSQLSTOREDPROCEDURE :tglAwal :tglAkhir";
$sth = $dbh->prepare($sql);
$tglAwal = $_GET["tglAwal"];
$tglAkhir = $_GET["tglAkhir"];
$sth->bindParam(':tglAwal',$tglAwal,PDO::PARAM_STR);
$sth->bindParam(':tglAkhir',$tglAkhir,PDO::PARAM_STR);
//$result = $sth->execute();
