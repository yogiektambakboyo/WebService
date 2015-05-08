<?php
//echo $_POST["tglawal"];
ini_set('max_execution_time', 1000);
$tglawal=date('Y-m-01');
$tglakhir=date('Y-m-t');
if(isset($_POST["tglawal"])){
	include_once "../script/connect.php";
	$tglawal=$_POST["tglawal"];
	$tglakhir=$_POST["tglakhir"];
}else{
	include_once "../script/connect.php";
}

$nopicklist="";
if(isset($_POST["kodenota"])){
	$nopicklist="union all
					select distinct(dj.gudang),mj.kodenota,mj.tgl,mj.shipto,mj.sales,mj.keterangan,mj.nopicklist,c.nama as delivery
					from masterjual mj
					join detailjual dj on dj.kodenota=mj.kodenota
					left join masterpendingretur mpr on mpr.noretur=mj.kodenota
					left join collector c on c.kode=mpr.delivery
					where mj.nopicklist='".$_POST["kodenota"]."'
					union all
					select dj.gudang,a.kodenota,max(a.createdate) tgl,mj.shipto,mj.sales,a.alasan,a.nopicklist,c.nama as delivery from(
					SELECT tl.kodenota,tl.kodeinvoice,md.kodenota kkdv,md.createdate,md.sopir,tl.alasan,tl.nopicklist
					FROM bcp_tolakandms tl 
					join DetailDelivery dd on dd.faktur=tl.kodeinvoice
					join MasterDelivery md on md.kodenota=dd.kodenota
					where tl.nopicklist='".$_POST["kodenota"]."'
					  )a
					 join detailjual dj on dj.kodenota=a.kodeinvoice
					 join masterjual mj on mj.kodenota=a.kodeinvoice
					 left join collector c on c.kode=a.sopir
					 group by a.kodenota,c.nama,dj.gudang,mj.shipto,mj.sales,a.alasan,a.nopicklist
					";
	
}

$sql="select b.*,p.perusahaan,s.nama
from
(select distinct(dj.gudang),mj.kodenota,mj.tgl,mj.shipto,mj.sales,mj.keterangan,mj.nopicklist,c.nama as delivery
from masterjual mj
join detailjual dj on dj.kodenota=mj.kodenota
left join masterpendingretur mpr on mpr.noretur=mj.kodenota
left join collector c on c.kode=mpr.delivery
where mj.kodenota like '%R%' and mj.tglpicklist is null and mj.nopicklist is null and mj.tgl between :tglawal and :tglakhir and mj.kodenota like :kodenota
union all
select dj.gudang,a.kodenota,max(a.createdate) tgl,mj.shipto,mj.sales,a.alasan,a.nopicklist,c.nama as delivery from(
SELECT tl.kodenota,tl.kodeinvoice,md.kodenota kkdv,md.createdate,md.sopir,tl.alasan,tl.nopicklist
FROM bcp_tolakandms tl 
join DetailDelivery dd on dd.faktur=tl.kodeinvoice
join MasterDelivery md on md.kodenota=dd.kodenota
where tl.tgl between :tglawal2 and :tglakhir2 and tl.kodenota like :kodenota2 and tl.nopicklist is null
  )a
 join detailjual dj on dj.kodenota=a.kodeinvoice
 join masterjual mj on mj.kodenota=a.kodeinvoice
 left join collector c on c.kode=a.sopir
 group by a.kodenota,c.nama,dj.gudang,mj.shipto,mj.sales,a.alasan,a.nopicklist
".$nopicklist."
) b
join pelanggan p on p.kode=b.shipto
join salesperson s on s.kode=b.sales

";

$sth = $dbh->prepare($sql);
$result = $sth->execute(array(
			":tglawal"=>$tglawal,
			":tglakhir"=>$tglakhir,
			":kodenota"=>$_SESSION['divisi']."%",
			":tglawal2"=>$tglawal,
			":tglakhir2"=>$tglakhir,
			":kodenota2"=>$_SESSION['divisi']."%"
			));

$result=$sth->fetchAll();
$listquery="";


 $i=0;       
foreach ($result as $row) {	
$i++;
$checked="";
if($row['nopicklist']!=""){
	$checked='checked="checked"';
	$gudang=$row['gudang'];
}
		$listquery.='<tr>
						<td><input type="checkbox" id="chk'.$i.'" name="rekap" value="'."'".$row['kodenota']."'".'" onclick="getgudang(\''.$row['gudang'].'\',this)" '.$checked.' /> '.$i.'</td>
						<td>'.$row['kodenota'].'</td>
						<td>'.date('d-m-Y',strtotime($row['tgl'])).'</td>
						<td>'.$row['delivery'].'</td>
						<td>'.$row['perusahaan'].'</td>
						<td>'.$row['nama'].'</td>						
						<td>'.$row['gudang'].'</td>
						<td>'.$row['keterangan'].'</td>
						
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