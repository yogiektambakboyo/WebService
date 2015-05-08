<?php
include_once "../script/connect.php";

$ponumber=$_GET['nopo'];
switch ($_GET['inbound']){
    case 'pembelian':
        $sql="SELECT Keterangan,Brg,convert(int,Selisih) as Selisih FROM [dbo].[fnWMS_CekSelisihInboundPO] (:POnumber)";
        break;
    case 'TransferMasuk':
        $sql="SELECT * FROM [dbo].[fnWMS_CekSelisihInboundTRI] (:POnumber)";
        break;
    default:
        $sql="SELECT * FROM [dbo].[fnWMS_CekSelisihInboundRTJ] (:POnumber)";

}



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
                        <td>'.$row['Keterangan'].'</td>
                        <td>'.$row['Brg'].'</td>
                        <td>'.$row['Selisih'].'</td>

                    </tr>';

}
?>


          <div class="col-xs-6">

                <table class="table table-hover table-bordered tablesorter" style="white-space: nowrap;">
                    <thead>
                    <tr>
                        <th>No</th>
                        <th>Nama Barang</th>
                        <th>Kode Barang</th>
                        <th>Selisih</th>

                    </tr>
                    </thead>
                    <?php echo $listselisih;?>
                </table>
          </div>
        <div class="col-xs-1">

        </div>
        <div style="position:fixed;left: 60%;">
            <button type="button" class="btn btn-primary" id="Movebtn"><i class="fa fa-angle-double-right"></i></button>
        </div>

        <div class="col-xs-5">

            <table class="table table-hover table-bordered tablesorter" id="movetbl" style="white-space: nowrap;">
                <thead>
                <tr>
                    <th>Kode Barang</th>
                    <th>Jumlah</th>
                    <th></th>
                </tr>
                </thead>

            </table>
        </div>
