<?php
include_once "../script/connectbon.php";
$closed = $_GET["isClosed"];
if($closed==2){
    $sql="select Kodenota,CONVERT(CHAR(10), Tgl, 120) as Tgl,TransactionType,Keterangan,isHaveRcpt,isClosed,DriverId,DispatchNo from dbo.OutBound_Master WHERE (Tgl between :tglAwal and :tglAkhir) and TransactionType='TransferKeluar'  and LEFT(Kodenota,5)=:cabang order by tgl";
}else{
    $sql="select Kodenota,CONVERT(CHAR(10), Tgl, 120) as Tgl,TransactionType,Keterangan,isHaveRcpt,isClosed,DriverId,DispatchNo from dbo.OutBound_Master WHERE isClosed like :isClose and (Tgl between :tglAwal and :tglAkhir)  and TransactionType='TransferKeluar' and LEFT(Kodenota,5)=:cabang order by tgl";
}
$sth = $dbh->prepare($sql);
$isClosed = $_GET["isClosed"];
$tglAwal = $_GET["tglAwal"];
$tglAkhir = $_GET["tglAkhir"];
if($closed<2){
    $sth->bindParam(":isClose",$isClosed,PDO::PARAM_STR);
}
$sth->bindParam(':tglAwal',$tglAwal,PDO::PARAM_STR);
$sth->bindParam(':tglAkhir',$tglAkhir,PDO::PARAM_STR);
$sth->bindParam(':cabang',$_SESSION["divisi"],PDO::PARAM_STR);


$result = $sth->execute();
$result=$sth->fetchAll();
$counter = 0;
foreach ($result as $row) {
    $counter++;
    echo '<tr>';
    echo '<td>'.$counter.'</td>';
    echo '<td>'.$row['Kodenota'].'</td>';
    echo '<td>'.$row['Tgl'].'</td>';
    echo '<td>'.$row['TransactionType'].'</td>';
    echo '<td>'.$row['Keterangan'].'</td>';
    echo '<td>'.$row['isHaveRcpt'].'</td>';
    echo '<td>'.$row['isClosed'].'</td>';
    echo '<td>'.$row['DriverId'].'</td>';
    echo '<td>'.$row['DispatchNo'].'</td>';
    echo '<td><a href="#" onclick="showDetail(\''.$row['Kodenota'].'\')" id="outbound_transfer_modal"><span class="glyphicon glyphicon-eye-open"></span> Detail</a></td>';
    if(($row['isHaveRcpt'] == 1)&&($row['isClosed']== 0)){
        echo '<td><a href="#" onclick="" id="outbound_transfer_modal_close"><span class="glyphicon glyphicon-saved"></span> Close</a></td>';
    }else{
        echo '<td></td>';
    }
    echo '</tr>';
}