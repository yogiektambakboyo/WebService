<?php
if(isset($_SESSION["user"])){

?>
<style>
<?php
include_once "connectbon.php";
$sql="select * from menugrup where jabatan=:jabatan";
$sth = $dbh->prepare($sql);
$result = $sth->execute(array(
			":jabatan"=>$_SESSION["jabatan"]
			));
  //var_dump($_SESSION);
$result=$sth->fetchAll();
foreach ($result as $row) {
		echo "#".$row['Menu']."{display:block}";
}
?>
</style>
<?php } ?>
<li class="menugrup" id="rekapretur">
	<a href="javascript:void(0)" data-target=".retur-menu" class="nav-header" data-toggle="collapse">
		<i class="fa fa-fw fa-dashboard"></i>Rekap Retur<i class="fa fa-collapse"></i>
	</a>


	<ul class="retur-menu nav nav-list collapse">					
		<li><a href="javascript:void(0)" onclick="checkdivisi('view/retur.php','#viewarea')"><span class="fa fa-caret-right"></span> Baru</a></li>            
		<li><a href="javascript:void(0)" onclick="checkdivisi('view/returtbl.php','#viewarea')"><span class="fa fa-caret-right"></span> Tabel</a></li>
	</ul>
</li>
<li class="menugrup" id="taskinbound">
	<a href="javascript:void(0)" data-target=".inbound-menu" class="nav-header" data-toggle="collapse">
		<i class="fa fa-fw fa-dashboard"></i>Task Inbound<i class="fa fa-collapse"></i>
	</a>
	<ul class="inbound-menu nav nav-list collapse">					
		<li><a href="#view/taskinbound.php?menu=pembelian.php" onclick="checkdivisi('view/taskinbound.php?menu=pembelian.php','#viewarea')"><span class="fa fa-caret-right"></span> Pembelian</a></li>
        <li><a href="#view/taskinbound.php?menu=transfer.php" onclick="checkdivisi('view/taskinbound.php?menu=transfer.php','#viewarea')"><span class="fa fa-caret-right"></span> Transfer Masuk</a></li>
        <li><a href="#view/taskinbound.php?menu=returtolakan.php" onclick="checkdivisi('view/taskinbound.php?menu=returtolakan.php','#viewarea')"><span class="fa fa-caret-right"></span> Retur/Tolakan</a></li>
	</ul>
</li>
<li class="menugrup" id="taskoutbound">
    <a href="javascript:void(0)" data-target=".outbound-menu" class="nav-header" data-toggle="collapse">
        <i class="fa fa-fw fa-dashboard"></i>Task Outbound<i class="fa fa-collapse"></i>
    </a>
    <ul class="outbound-menu nav nav-list collapse">
        <li><a href="#view/outbound_so.php" onclick="checkdivisi('view/outbound_so.php','#viewarea')"><span class="fa fa-caret-right"></span> Sales Order</a></li>
        <li><a href="#view/outbound_transferkeluar.php" onclick="checkdivisi('view/outbound_transferkeluar.php','#viewarea');$(this).closest('li').addClass('active');"><span class="fa fa-caret-right"></span> Transfer Keluar</a></li>
        <li><a href="#view/outbound_returbeli.php" onclick="checkdivisi('view/outbound_returbeli.php','#viewarea');$(this).closest('li').addClass('active');"><span class="fa fa-caret-right"></span> Retur Beli</a></li>

    </ul>
</li>