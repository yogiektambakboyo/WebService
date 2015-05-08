<?php
include_once "../script/connectbon.php";

$ponumber=$_GET['nopo'];


$sql="select ir.LineNumber,ir.ProductCode,b.keterangan,ir.LotNumber,ir.ExpDate,ir.QtyReceived,ir.StockStatusCode
        from Inbound_Rcpt ir
        join ".$_SESSION['dbase'].".dbo.barang b on b.kode=ir.productcode
        where ir.POnumber=:POnumber";
$sth = $dbh->prepare($sql);
$result = $sth->execute(array(":POnumber"=>$ponumber));

$result=$sth->fetchAll();
$listrcpt="";
//echo count($result);

 $i=0;       
foreach ($result as $row) {	
$i++;
    $listrcpt.='<tr>
						<td>'.$i.'</td>
						<td>'.$row['LineNumber'].'</td>
						<td>'.$row['keterangan'].'</td>
						<td>'.$row['LotNumber'].'</td>
						<td>'.$row['ExpDate'].'</td>						
						<td>'.$row['QtyReceived'].'</td>
						<td>'.$row['StockStatusCode'].'</td>	
					</tr>';
		
		

	}


$sql="select isend.LineNumber,isend.ProductCode,b.keterangan,isend.SupplierRef,isend.Other,isend.QtyOrder,isend.StockStatusCode
        from Inbound_Send isend
         join ".$_SESSION['dbase'].".dbo.barang b on b.kode=isend.productcode
        where isend.POnumber=:POnumber";
$sth = $dbh->prepare($sql);
$result = $sth->execute(array(":POnumber"=>$ponumber));

$result=$sth->fetchAll();
$listsend="";
//echo count($result);

$i=0;
foreach ($result as $row) {
    $i++;
    $listsend.='<tr>
						<td>'.$i.'</td>
						<td>'.$row['LineNumber'].'</td>
						<td>'.$row['keterangan'].'</td>
						<td>'.$row['SupplierRef'].'</td>
						<td>'.$row['Other'].'</td>
						<td>'.$row['QtyOrder'].'</td>
						<td>'.$row['StockStatusCode'].'</td>
					</tr>';



}

$sql="select ibs.productcode,b.keterangan,ibs.qtyorder,isnull(ir.qtyreceived,0) qtyreceived,isnull(cast((db.jml*db.rasio) as int),0) as GBS
        from inbound_send ibs
        left join inbound_rcpt ir on ir.productcode=ibs.productcode and ir.ponumber=ibs.ponumber
        left join ".$_SESSION['dbase'].".dbo.detailbeli db on db.brg=ibs.productcode and db.kodenota=ibs.ponumber
        left join ".$_SESSION['dbase'].".dbo.barang b on b.kode=ibs.productcode
        where ibs.ponumber=:POnumber and (ir.qtyreceived<>isnull((db.jml*db.rasio),0) or ir.qtyreceived is null)";
$sth = $dbh->prepare($sql);
$result = $sth->execute(array(":POnumber"=>$ponumber));

$result=$sth->fetchAll();
$listselisih="";
//echo count($result);

$i=0;
foreach ($result as $row) {
    $i++;
    $listselisih.='<tr>
                        <td>'.$i.'</td>
                        <td>'.$row['keterangan'].'</td>
                        <td>'.$row['qtyorder'].'</td>
                        <td>'.$row['qtyreceived'].'</td>
                        <td>'.$row['GBS'].'</td>
                        <td>
                            <select class="form-control">
                                <option>Task tambahan</option>
                                <option>Retur beli</option>
                                <option>Faktur ekspedisi</option>
                                <option>Faktur karyawan</option>
                            </select>
                        </td>
                    </tr>';

}
?>
Po. Number : <?php echo $_GET['nopo']; ?>
<div class="bs-example bs-example-tabs" role="tabpanel" data-example-id="togglable-tabs">
    <ul id="myTab" class="nav nav-tabs" role="tablist">
        <li role="presentation" class="active"><a href="#receipt" id="receipt-tab" role="tab" data-toggle="tab" aria-controls="receipt" aria-expanded="true">Receipt</a></li>
        <li role="presentation"><a href="#send" role="tab" id="send-tab" data-toggle="tab" aria-controls="send">Send</a></li>
        <!--<li role="presentation"><a href="#selisih" role="tab" id="selisih-tab" data-toggle="tab" aria-controls="selisih">Selisih</a></li>-->

    </ul>
    <div id="myTabContent" class="tab-content">
        <div role="tabpanel" class="tab-pane fade in active" id="receipt" aria-labelledby="receipt-tab">

            <div class="table-responsive">

                <table class="table table-hover table-bordered tablesorter" style="white-space: nowrap;">
                    <thead>
                    <tr>
                        <th>No</th>
                        <th>LineNumber</th>
                        <th>Nama Product</th>
                        <th>LotNumber</th>
                        <th>ExpDate</th>
                        <th>QtyReceived</th>
                        <th>StockStatusCode</th>
                    </tr>
                    </thead>
                    <?php echo $listrcpt;?>
                </table>
            </div>

        </div>
        <div role="tabpanel" class="tab-pane fade" id="send" aria-labelledby="send-tab">
            <div class="table-responsive">

                <table class="table table-hover table-bordered tablesorter" style="white-space: nowrap;">
                    <thead>
                    <tr>
                        <th>No</th>
                        <th>LineNumber</th>
                        <th>Nama Product</th>
                        <th>LotNumber</th>
                        <th>ExpDate</th>
                        <th>QtyOrder</th>
                        <th>StockStatusCode</th>
                    </tr>
                    </thead>
                    <?php echo $listsend;?>
                </table>
            </div>
        </div>

        <!--<div role="tabpanel" class="tab-pane fade" id="selisih" aria-labelledby="receipt-tab">

            <div class="table-responsive">

                <table class="table table-hover table-bordered tablesorter" style="white-space: nowrap;">
                    <thead>
                    <tr>
                        <th>No</th>
                        <th>Nama Product</th>
                        <th>Order</th>
                        <th>Received</th>
                        <th>GBS</th>
                        <th>Option</th>
                    </tr>
                    </thead>
                    <?php //echo $listselisih;?>
                </table>
            </div>

        </div>-->

    </div>
</div>