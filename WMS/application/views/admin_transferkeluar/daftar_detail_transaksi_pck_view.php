<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <title>Daftar Detail Task Transfer Stok Keluar</title>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="description" content="">
        <meta name="author" content="">
        <?php
        $this->load->view('include/header');
        ?>
    </head>

    <body>
        <div class="container">
            <div class="row">
                <div class="span12 center">
                    <h1>Detail Task Transfer Stok Keluar</h1>
                    <hr />
                    <?php
                    echo "<strong>No. Transaksi: </strong>" . $transaksi['transactionCode'] . "<br />";
                    echo "<strong>No. Nota: </strong>" . $transaksi['ERPCode'] . "<br />";
                    if ($transaksi['isFinish'] == 0) {
                        echo "<strong>Belum Finish</strong><br />";
                    } else {
                        echo "<strong>Sudah Finish</strong><br />";
                    }
                    if ($transaksi['isCancel'] == 0) {
                        echo "<strong>Belum Batal</strong><br />";
                    } else {
                        echo "<strong>Sudah Batal</strong><br />";
                    }
                    echo "<strong>Catatan : </strong>" . $transaksi['note'] . "<br />";
                    ?>
                    <table class="table table-bordered table-hover table-striped">
                        <tr>
                            <th>No</th>
                            <th>SKU</th>
                            <th>RackSlot Skrg</th>
                            <th>RackSlot Tujuan</th>
                            <th>Jumlah yg Butuh</th>
                            <th>Jumlah yg Diambil</th>
                            <th>Pengambil</th>
                            <th>Penaruh</th>
                            <th>Task Tambahan</th>
                            <th>Catatan</th>
                        </tr>
                        <?php
                        $i = 1;
                        foreach ($detail_transaksi as $row) {
                            ?>
                            <tr>
                                <td><?php echo $i++ ?></td>
                                <td><?php echo $row['Keterangan'] ?></td>
                                <td>
                                    <?php
                                    if($row['Curr']!=NULL)
                                    {
                                        echo $row['Curr'];
                                    }
                                    else
                                    {
                                        echo "Belum Diambil";
                                    }
                                    ?>
                                </td>
                                <td>
                                    <?php
                                    if($row['Dest']!=NULL)
                                    {
                                        echo $row['Dest'];
                                    }
                                    else
                                    {
                                        echo "Belum Ditaruh";
                                    }
                                    ?>
                                </td>
                                <td><?php echo $row['KonversiQtyNeedStart'] ?></td>
                                <td><?php echo $row['KonversiQty'] ?></td>
                                <td>
                                    <?php
                                    if($row['User_1stName']!=NULL)
                                    {
                                        echo $row['User_1stName'];
                                    }
                                    else
                                    {
                                        echo "Tidak ada Pengambil";
                                    }
                                    ?>
                                </td>
                                <td>
                                    <?php
                                    if($row['User_2ndName']!=NULL)
                                    {
                                        echo $row['User_2ndName'];
                                    }
                                    else
                                    {
                                        echo "Tidak ada Penaruh";
                                    }
                                    ?>
                                </td>
                                <td>
                                    <a href="<?php echo base_url() ?>index.php/admin_transferkeluar/edit_note/<?php echo str_replace('=', '-', str_replace('/', '_', base64_encode($row['TransactionCode']))) ?>/<?php echo str_replace('=', '-', str_replace('/', '_', base64_encode($row['NoUrut']))) ?>/<?php echo str_replace('=', '-', str_replace('/', '_', base64_encode($row['Status']))) ?>">
                                    <?php
                                    if ($row['Status'] == 0) {
                                        ?>
                                        
                                            <?php
                                            if ($row['Note'] != NULL) {
                                                if (strlen($row['Note']) > 10) {
                                                    echo substr($row['Note'], 0, 10) . "...";
                                                } else {
                                                    echo $row['Note'];
                                                }
                                            } else {
                                                echo "Tambah";
                                            }
                                            ?>
                                        </a>
                                        <?php
                                    } else {
                                        if ($row['Note'] != NULL) {
                                            if (strlen($row['Note']) > 10) {
                                                echo substr($row['Note'], 0, 10) . "...";
                                            } else {
                                                echo $row['Note'];
                                            }
                                        } else {
                                            echo "Catatan Kosong";
                                        }
                                    }
                                    ?>
                                </td>
                                <td>
                                    <?php
                                    if($row['AddTask']!=0)
                                    {
                                        echo "Task Tambahan";
                                    }
                                    else
                                    {
                                        echo "Task Normal";
                                    }
                                    ?>
                                </td>
                            </tr>
                            <?php
                        }
                        ?>
                    </table>
                    <p><a href="<?php echo base_url() ?>index.php/admin_transferkeluar/daftar_task_picking" class="btn btn-large btn-inverse">Kembali</a></p>
                </div><!-- /.span4 -->
            </div><!-- /.row -->
        </div><!-- /.container -->
        <?php
        $this->load->view("include/footer");
        ?>
    </body>
</html>
