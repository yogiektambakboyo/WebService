<?php
//echo $_POST["tglawal"];
ini_set('max_execution_time', 1000);

if(isset($_POST["tglawal"])){
    include_once "../script/connectbon.php";
    $tglawal=$_POST["tglawal"];
    $tglakhir=$_POST["tglakhir"];
}else{
    include_once "../script/connect.php";
    /////////////////tgl transaksi///////////////
    $sql="select TglTransaksi from kategori where Cabang=:cabang";
    $sth=$dbh->prepare($sql);
    $result=$sth->execute(array(
        ":cabang"=>$_SESSION["divisi"]));
    $result=$sth->fetchall();
    //print_r($result);
    $tgltransaksi=strtotime($result[0]["TglTransaksi"]);

///////////////////////////////////////////
    $tglawal=date('Y-m-d',strtotime("-30 days",$tgltransaksi));
    $tglakhir=date('Y-m-d',strtotime("+1 days",$tgltransaksi));

    include_once "../script/connectbon.php";
}
//echo $tglakhir.' - '.$tglawal;
$sql="select im.POnumber,im.Keterangan,im.isHaveRcpt,im.isClosed,mb.Shipfrom as Shipto from inbound_master im
      join ".$_SESSION["dbase"].".dbo.masterbeli mb on mb.kodenota=im.POnumber
		where im.tgl between :tglawal and :tglakhir and im.TransactionType='Pembelian' and im.POnumber like :divisi";
$sth = $dbh->prepare($sql);
$result = $sth->execute(array(
    ":tglawal"=>$tglawal,
    ":tglakhir"=>$tglakhir,
    ":divisi"=>$_SESSION['divisi'].'%'

));

$result=$sth->fetchAll();
$listquery="";
//echo count($result);

$i=0;
foreach ($result as $row) {
    $i++;
    $listquery.='<tr>
						<td>'.$i.'</td>
						<td>'.$row['POnumber'].'</td>
						<td>'.$row['Shipto'].'</td>
						<td style="max-width:40px"><input class="tbltxtbox" type="text" value="'.$row['Keterangan'].'" disabled /></td>
						<td>'.$row['isHaveRcpt'].'</td>
						<td>'.$row['isClosed'].'</td>
						<td>
							<button type="button" class="btn btn-primary tblbtn" id="row'.$i.'" onclick="showdetailmodal(\'taskinbound/detailinbound.php?nopo='.$row['POnumber'].'\')">View</button>
						    <button type="button" class="btn btn-warning tblbtn" onclick="checkdivisi(\'view/closetask.php?inbound=pembelian&nopo='.$row['POnumber'].'&shipto='.$row['Shipto'].'\',\'#viewarea\')">Close</button>
						</td>
					</tr>';



}

//<a href="taskinbound/detailinbound.php?nopo='.$row['POnumber'].'" data-toggle="modal" data-target="#myModal"></a>
?>
<input type="hidden" id="notaedit" value="<?php if(isset($_POST["kodenota"])){echo $_POST["kodenota"];}?>">
<input type="hidden" id="gudangedit" value="<?php if(isset($gudang)){echo $gudang;}?>">
<div class="table-responsive">
    <table class="table table-hover table-bordered tablesorter" style="white-space: nowrap;margin-left: 2%;" id="RetTable">
        <thead>
        <tr>
            <th>No</th>
            <th>PO Number</th>
            <th>ShipTo</th>
            <th>Keterangan</th>
            <th>isHaveRcpt</th>
            <th>isClosed</th>
            <th>Option</th>
        </tr>
        </thead>
        <?php echo $listquery;?>
    </table>
</div>

