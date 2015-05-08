<?php
//echo $_POST["tglawal"];
ini_set('max_execution_time', 1000);
$tglawal=date('Y-m-01');
$tglakhir=date('Y-m-t');
if(isset($_POST["tglawal"])){
	include_once "../script/connectbon.php";
	$tglawal=$_POST["tglawal"];
	$tglakhir=$_POST["tglakhir"];
}else{
	include_once "../script/connectbon.php";
}

$sql="select POnumber,TransactionType,Keterangan,isHaveRcpt,isClosed from inbound_master 
		where createdate between :tglawal and :tglakhir";
$sth = $dbh->prepare($sql);
$result = $sth->execute(array(
			":tglawal"=>$tglawal,
			":tglakhir"=>$tglakhir	
			
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
						<td>'.$row['TransactionType'].'</td>
						<td>'.$row['Keterangan'].'</td>
						<td>'.$row['isHaveRcpt'].'</td>						
						<td>'.$row['isClosed'].'</td>
						<td>
							<button type="button" class="btn btn-primary" id="row'.$i.'" onclick="showdetailmodal(\'taskinbound/detailinbound.php?nopo='.$row['POnumber'].'\')">View</button>
						    <button type="button" class="btn btn-warning closebtn">Close</button>
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
			<th>Transaction Type</th>
			<th>Keterangan</th>
			<th>isHaveRcpt</th>
			<th>isClosed</th>			
			<th>Option</th>			
			</tr>
		</thead>
		<?php echo $listquery;?>
	</table>
</div>