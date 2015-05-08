<?php 
include_once "../script/connect.php";
$arrkodenota=explode(",",$_POST["kodenota"]);

$notaretur="";
$notatolak="";

foreach($arrkodenota as $r){
	if(strpos($r,"/TO/")==true){
        $notatolak.=$notatolak.",'".$r."'";

	}else{
        $notaretur.=$notaretur.",'".$r."'";

	}
}
$notatolak=substr($notatolak, 1);
$notaretur=substr($notaretur, 1);

echo " tolakan ".$notatolak;
echo " retur ".$notaretur;
echo " keterangan ".$_POST["keterangan"];

//exit;

//echo "Stop";

$sql="select brg,sum(abs(jml)) jml
		from detailjual
		where kodenota in (".$notaretur.")
		group by brg";
$sth = $dbh->prepare($sql);
$result = $sth->execute();
$result=$sth->fetchall();
$detailjual=$result;

$sql="select brg,sum(abs(jml)) jml
		from bcp_tolakandms
		where kodenota in (".$notatolak.")
		group by brg";
$sth = $dbh->prepare($sql);
$result = $sth->execute();
$result=$sth->fetchall();
$detailtolakan=$result;


//print_r($_POST);
//echo $_SESSION["divisi"]."/".date("Y")."/000001"; 

///////////////////kodenota///////////////////
$sql="select top 1 * from bcp_masterpicklist order by createdate DESC";
$sth=$dbh->prepare($sql);
$result=$sth->execute();
$result=$sth->fetchall();

if(count($result)<1){
	$kodenota=$_SESSION["divisi"]."/PR/".date("Y")."/000001";
	//echo sprintf('%06d',substr($kodenota,-6)+1);
}elseif($_POST['notaedit']!=''){
	$kodenota=$_POST['notaedit'];
}else{
	$kodenota=$_SESSION["divisi"]."/PR/".date("Y")."/".sprintf('%06d',substr($result[0]["Kodenota"],-6)+1);	
}
//////////////////////////////////////////////
/////////////////tgl transaksi///////////////

$sql="select TglTransaksi from kategori where Cabang=:cabang";
$sth=$dbh->prepare($sql);
$result=$sth->execute(array(
			":cabang"=>$_SESSION["divisi"]));
$result=$sth->fetchall();
//print_r($result[0]["TglTransaksi"]);
$tgltransaksi=$result[0]["TglTransaksi"];

///////////////////////////////////////////

//print_r($detailjual);
try{
		$dbh->beginTransaction();
		if($_POST['notaedit']!=""){
			
			$sql="delete from bcp_detailpicklist where kodenota=:notaedit";
			$prepared=$dbh->prepare($sql);
			$prepared->execute(array(
					"notaedit"=>$_POST['notaedit']							
					));
			$sql="update masterjual set nopicklist=null, tglpicklist=null where nopicklist=:notaedit";
			$prepared=$dbh->prepare($sql);
			$prepared->execute(array(
					"notaedit"=>$_POST['notaedit']							
					));
			$sql="update bcp_tolakandms set nopicklist=null, tglpicklist=null where nopicklist=:notaedit";
			$prepared=$dbh->prepare($sql);
			$prepared->execute(array(
					"notaedit"=>$_POST['notaedit']							
					));
					
			
			$sql="update bcp_masterpicklist set Gudang=:gudang,EditDate=:editdate,EditBy=:editby,Keterangan=:keterangan
				where kodenota=:kodenota";
			$prepared=$dbh->prepare($sql);
			$prepared->execute(array(
					"kodenota"=>$kodenota,
					"gudang"=>$_POST["gudang"],					
					"editdate"=>date('Y-m-d H:i:s'),
					"editby"=>$_SESSION["kodeuser"],
                    "keterangan"=>$_POST["keterangan"]
					));
			
		}else{		
			$sql="insert into bcp_masterpicklist(Kodenota,Gudang,Tgl,CreateDate,CreateBy,EditDate,EditBy,Keterangan)
					values(:kodenota,:gudang,:tgl,:createdate,:createby,:editdate,:editby,:keterangan)";
			$prepared=$dbh->prepare($sql);
			$prepared->execute(array(
						"kodenota"=>$kodenota,
						"gudang"=>$_POST["gudang"],
						"tgl"=>$tgltransaksi,
						"createdate"=>date('Y-m-d H:i:s'),
						"createby"=>$_SESSION["kodeuser"],
						"editdate"=>date('Y-m-d H:i:s'),
						"editby"=>$_SESSION["kodeuser"],
                        "keterangan"=>$_POST["keterangan"]
						));
		}			
		$sql="insert into bcp_detailpicklist(Kodenota,Brg,Jml)
				values(:kodenota,:brg,:jml)";
		$prepared=$dbh->prepare($sql);
		
		foreach ($detailjual as $row){
			$prepared->execute(array(
				"kodenota"=>$kodenota,
				"brg"=>$row["brg"],
				"jml"=>$row["jml"]	
			));		
		}
		
		foreach ($detailtolakan as $row){
			$prepared->execute(array(
				"kodenota"=>$kodenota,
				"brg"=>$row["brg"],
				"jml"=>$row["jml"]	
			));		
		}
		
		$sql="update masterjual set nopicklist=:kodenota,TglPickList=:tgl 
				where kodenota in (".$notaretur.") and nopicklist is null and tglpicklist is null";
		$prepared=$dbh->prepare($sql);
		$prepared->execute(array(
				"kodenota"=>$kodenota,
				"tgl"=>$tgltransaksi	
			));
		
		$sql="update bcp_tolakandms set nopicklist=:kodenota,TglPickList=:tgl 
				where kodenota in (".$notatolak.") and nopicklist is null and tglpicklist is null";
		$prepared=$dbh->prepare($sql);
		$prepared->execute(array(
				"kodenota"=>$kodenota,
				"tgl"=>$tgltransaksi	
			));
		
		
		$dbh->commit();
		//$dbh->rollback();
		echo "Rekap picklist telah tersimpan dengan kode ".$kodenota;
}catch(Exception $e){
		$dbh->rollback();
		echo json_encode(array("msg"=>$e->getMessage()));
		
			
}
		
?>
