<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <title>Daftar Detail Transfer Masuk</title>
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
                    <h1>Detail Transfer Masuk</h1>
                    <hr />
                    <?php
                    echo "<strong>No. Transaksi: </strong>" . $admin_transfermasuk['transactionCode'] . "<br />";
                    echo "<strong>No. PickList: </strong>" . $admin_transfermasuk['ERPCode'] . "<br />";
                    if ($admin_transfermasuk['isFinish'] == 0) {
                        echo "<strong>Belum Finish</strong><br />";
                    } else {
                        echo "<strong>Sudah Finish</strong><br />";
                    }
                    if ($admin_transfermasuk['isCancel'] == 0) {
                        echo "<strong>Belum Batal</strong><br />";
                    } else {
                        echo "<strong>Sudah Batal</strong><br />";
                    }
                    echo "<strong>Catatan : </strong>" . $admin_transfermasuk['note'] . "<br />";
                    ?>
                    <table class="table table-bordered table-hover table-striped">
                        <tr>
                            <th>No</th>
                            <th>Bin</th>
                            <th>SKU</th>
                            <th>ExpDate</th>
                            <th>Jumlah</th>
                            <th>RackSlot Skrg</th>
                            <th>RackSlot Tujuan</th>
                            <th>Waktu Buat</th>
                            <th>Catatan</th>
                            <th>Detail</th>
                        </tr>
                        <?php
                        $i = 1;
                        foreach ($detail_admin_transfermasuk as $row) {
                            ?>
                            <tr>
                                <td><?php echo $i++ ?></td>
                                <td><?php echo $row['BinCode'] ?></td>
                                <td><?php echo $row['keterangan'] ?></td>
                                <td><?php echo date('d-m-Y', strtotime($row['tanggalExp'])); ?></td>
                                <td><?php echo $row['Qtykonversi'] ?></td>
                                <td><?php echo $row['RackSlotSekarang'] ?></td>
                                <td>
                                    <?php
                                    if ($row['Status2'] == 0) {
                                        ?>
                                        <a href="<?php echo base_url() ?>index.php/admin_transfermasuk/edit_destrack/<?php echo str_replace('=', '-', str_replace('/', '_', base64_encode($row['TransactionCode']))) ?>/<?php echo str_replace('=', '-', str_replace('/', '_', base64_encode($row['NoUrut']))) ?>/<?php echo str_replace('=', '-', str_replace('/', '_', base64_encode($row['DestRackSlot']))) ?>"><?php echo $row['RackSlotTujuan'] ?></a>
                                        <?php
                                    } else {
                                        echo $row['RackSlotTujuan'];
                                    }
                                    ?>
                                </td>
                                <td><?php echo $row['waktuBuat'] ?></td>
                                <td>
                                    <a href="<?php echo base_url() ?>index.php/admin_transfermasuk/edit_note/<?php echo str_replace('=', '-', str_replace('/', '_', base64_encode($row['TransactionCode']))) ?>/<?php echo str_replace('=', '-', str_replace('/', '_', base64_encode($row['NoUrut']))) ?>/<?php echo str_replace('=', '-', str_replace('/', '_', base64_encode($row['Status2']))) ?>">
                                    <?php
                                    if ($row['Status2'] == 0) {
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
                                <td style="text-align: center">
                                    <a href="<?php echo base_url() ?>index.php/admin_transfermasuk/history_detail_admin_transfermasuk/<?php echo str_replace('=', '-', str_replace('/', '_', base64_encode($admin_transfermasuk['transactionCode']))) ?>/<?php echo str_replace('=', '-', str_replace('/', '_', base64_encode($row['NoUrut']))) ?>" class="btn btn-info">Detail</a>
                                </td>
                            </tr>
                            <?php
                        }
                        ?>
                    </table>
                    <p><a href="<?php echo base_url() ?>index.php/admin_transfermasuk/daftar_admin_transfermasuk" class="btn btn-large btn-inverse">Kembali</a></p>
                </div><!-- /.span4 -->
            </div><!-- /.row -->
        </div><!-- /.container -->
        <?php
        $this->load->view("include/footer");
        ?>
    </body>
</html>
