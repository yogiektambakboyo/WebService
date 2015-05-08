<?php
//echo $_POST["tglawal"];
$tglawal=date('Y-m-d',strtotime("-50 days"));
$tglakhir=date('Y-m-d',strtotime("+2 days"));
if(isset($_POST["tglawal"])){
	include_once "../script/connect.php";
	$tglawal=$_POST["tglawal"];
	$tglakhir=$_POST["tglakhir"];
}else{
	include_once "../script/connect.php";
}

	

$sql="select *
from BCP_MasterPickList
where createdate between :tglawal and :tglakhir and kodenota like '".$_SESSION['divisi']."%'";

$sth = $dbh->prepare($sql);
$result = $sth->execute(array(
			":tglawal"=>$tglawal,
			":tglakhir"=>$tglakhir
			));

$result=$sth->fetchAll();
$listquery="";


 $i=0;       
foreach ($result as $row) {	
$i++;
		$listquery.='<tr id="tbl'.$i.'" class="tr'.$i.'">
						<td>'.$i.'</td>
						<td>'.$row['Kodenota'].'</td>
						<td>'.$row['Gudang'].'</td>
						<td>'.$row['Tgl'].'</td>
						<td>'.$row['CreateDate'].'</td>
						<td>'.$row['CreateBy'].'</td>
						<td>'.$row['EditDate'].'</td>
						<td>'.$row['EditBy'].'</td>
						<td>'.$row['WMSRcpt'].'</td>
						<td>'.$row['Keterangan'].'</td>
					</tr>';
		
		

	}
		
	
?>
<div class="table-responsive">
	<table class="table table-hover table-bordered tablesorter" style="white-space: nowrap;margin-left: 2%;" id="RetTable">
		<thead>
			<tr>
                <th>No</th>
                <th>Kodenota</th>
                <th>Gudang</th>
                <th>Tgl</th>
                <th>CreateDate</th>
                <th>CreateBy</th>
                <th>EditDate</th>
                <th>EditBy</th>
                <th>WMSRcpt</th>
                <th>Keterangan</th>
			</tr>
		</thead>
		<div id="rightbox"></div>
		<?php echo $listquery;?>
	</table>
</div>