<?php
//echo $_POST["tglawal"];
ini_set('max_execution_time', 1000);
/////////////////tgl transaksi///////////////
include_once "../script/connect.php";
$sql="select TglTransaksi from kategori where Cabang=:cabang";
$sth=$dbh->prepare($sql);
$result=$sth->execute(array(
    ":cabang"=>$_SESSION["divisi"]));
$result=$sth->fetchall();
//print_r($result[0]["TglTransaksi"]);
$tgltransaksi=strtotime($result[0]["TglTransaksi"]);

///////////////////////////////////////////
$tglawal=date('Y-m-d',strtotime("-30 days",$tgltransaksi));
$tglakhir=date('Y-m-d',strtotime("+1 days",$tgltransaksi));


//echo $tglawal." - ".$tglakhir;
$nopicklist="'-'";
if(isset($_POST["kodenota"])){
	$nopicklist=$_POST["kodenota"];
	
}



$sql="select * from [dbo].[fnWMS_RekapRetur](:kodenota,:tglawal,:tglakhir,:nopicklist) order by checked desc,tgl";
$sth = $dbh->prepare($sql);

$result = $sth->execute(array(
			":tglawal"=>$tglawal,
			":tglakhir"=>$tglakhir,
			":kodenota"=>$_SESSION['divisi'],
			":nopicklist"=>$nopicklist
			
			));

$result=$sth->fetchAll();
$listquery="";
//echo count($result);
 echo $nopicklist;
 $i=0;       
foreach ($result as $row) {	
$i++;
$checked="";
if($row['NoPickList']!=""){
	$checked='checked="checked"';
	$gudang=$row['Gudang'];
}
		$listquery.='<tr>
						<td><input type="checkbox" id="chk'.$i.'" name="rekap" value="'.$row['Kodenota'].'" onclick="getgudang(\''.$row['Gudang'].'\',this)" '.$checked.' /> '.$i.'</td>
						<td>'.$row['Kodenota'].'</td>
						<td>'.date('d-m-Y',strtotime($row['Tgl'])).'</td>
						<td>'.$row['Delivery'].'</td>
						<td>'.$row['Perusahaan'].'</td>
						<td>'.$row['Nama'].'</td>
						<td>'.$row['Gudang'].'</td>
						<td>'.$row['Keterangan'].'</td>
						
					</tr>';
		
		

	}
		
	
?>
<input type="hidden" id="notaedit" value="<?php if(isset($_POST["kodenota"])){echo $_POST["kodenota"];}?>">
<input type="hidden" id="gudangedit" value="<?php if(isset($gudang)){echo $gudang;}?>">
<div class="table-responsive">
	<table class="table table-hover table-bordered tablesorter" style="white-space: nowrap;margin-left: 2%;" id="RetTable">
		<thead>
			<tr>
			<th>No</th>
			<th>Kodenota</th>
			<th>Tanggal</th>
			<th>Delivery</th>
			<th>Shipto</th>
			<th>Sales</th>			
			<th>Gudang</th>
			<th>Keterangan</th>
			</tr>
		</thead>
		<?php echo $listquery;?>
	</table>
</div>