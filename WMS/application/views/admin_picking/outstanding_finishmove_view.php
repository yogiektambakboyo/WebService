<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <title>Outstanding Move Picking</title>
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
                    <h1>Outstanding Move Picking</h1>
                    <hr />
                    <?php
                    echo validation_errors();
                    if (isset($error)) {
                        echo "<div class='alert alert-error'><strong>" . $error . "</strong><br /></div>";
                    }
                    if (isset($pesan)) {
                        ?>
                        <div class="alert alert-success">
                            <strong>
                                <?php
                                echo $pesan;
                                ?>
                            </strong>
                        </div>
                        <?php
                    }
                    ?>
                    <?php
                    echo "<strong>No. Transaksi: </strong>" . $TransactionCode . "<br />";
                    echo "<strong>No. Nota: </strong>" . $ERPCode . "<br />";
                    ?>
                    <table class="table table-bordered table-hover table-striped">
                        <tr>
                            <th>Tipe</th>
                            <th>Bin</th>
                            <th>SKU</th>
                            <th>Barang</th>
                            <th>ExpDate</th>
                            <th>Jumlah</th>
                            <th>RackSlot Skrg</th>
                        </tr>
                        <?php
                        foreach ($outstanding as $row) {
                            ?>
                            <tr>
                                <td><?php echo $row['Jenis'] ?></td>
                                <td><?php echo $row['Bin'] ?></td>
                                <td><?php echo $row['SKUCode'] ?></td>
                                <td><?php echo $row['Keterangan'] ?></td>
                                <td><?php echo date('d-m-Y', strtotime($row['ExpDate'])); ?></td>
                                <td><?php echo $row['Qty'] ?></td>
                                <td><?php echo $row['SrcRackName'] ?></td>
                            </tr>
                            <?php
                        }
                        ?>
                    </table>
                    <input type="hidden" id="refreshed" value="no">
                    <p><a href="<?php echo base_url() ?>index.php/admin_picking/cek_finishmove/<?php echo str_replace('=', '-', str_replace('/', '_', base64_encode($TransactionCode))); ?>/<?php echo str_replace('=', '-', str_replace('/', '_', base64_encode($ERPCode))); ?>" class="btn btn-large btn-info"><i class="icon-refresh"></i> Refresh</a></p>
                    <p><a href="<?php echo base_url() ?>index.php/admin_picking/edit_mastertaskpck/<?php echo str_replace('=', '-', str_replace('/', '_', base64_encode($TransactionCode))); ?>" class="btn btn-large btn-inverse">Kembali</a></p>
                </div><!-- /.span4 -->
            </div><!-- /.row -->
        </div><!-- /.container -->
        <?php
        $this->load->view("include/footer");
        ?>
    </body>
</html>
