<?php session_start();

$_SESSION['server']=$_POST['server'];
$_SESSION['user']=$_POST['user'];
$_SESSION['pass']=$_POST['pass'];
$_SESSION['dbase']=$_POST['dbase'];

try{
$hostname=$_SESSION['server'];
$dbname=$_POST['dbase'];
$username=$_SESSION['user'];
$pw=$_SESSION['pass'];

$dbh = new PDO ("sqlsrv:server=$hostname;Database=$dbname","$username","$pw");
//$dbh = new PDO("dblib:host=$hostname;dbname=$dbname", $username, $pw); ==> Aktifkan jika diupload ke Server

setcookie("server", $hostname, time()+60*60*24*100, "/"); // Cookie set to expire in about 30 days
setcookie("dbase", $dbname, time()+60*60*24*100, "/"); // Cookie set to expire in about 30 days

$sql="select kode,jabatan from staff where nama =:user";
$sth = $dbh->prepare($sql);
$result = $sth->execute(array(":user"=>$_POST["user"]));
$result=$sth->fetchAll();
if(count($result)>0){
	foreach ($result as $row){	
		$_SESSION["jabatan"]=$row["jabatan"];
        $_SESSION["kodeuser"]=$row["kode"];
		
	}
	
}else{
	if(isset($_SESSION["jabatan"])){unset($_SESSION["jabatan"]);}
    if(isset($_SESSION["kodeuser"])){unset($_SESSION["kodeuser"]);}
}
}catch (PDOException $e) {
    echo "Failed to get DB handle: " . $e->getMessage() . "\n";
    exit;
}



$sql="select * from kategori";
$sth = $dbh->prepare($sql);
$result = $sth->execute();

$divisi="";
$result=$sth->fetchAll();

foreach ($result as $row){	
	$divisi.="<option value='".$row['Cabang']."'>".$row['NamaCabang']."</option>";
}

?>
<div class="form-group col-xs-12">
						
	<div class="col-xs-4"><label class="control-label">Divisi : </label></div>
	<div class="col-xs-8">
		<select class="form-control" id="SlctDivisi">
			<?php echo $divisi;?>
		
		</select>

	</div>
</div>
