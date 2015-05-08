<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <title>Daftar Replenish</title>
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
                    <h1>Daftar Replenish</h1>
                    <hr />

                    <div class="control-group">
                        <table class="table table-bordered table-hover table-striped">
                            <thead>
                                <tr>
                                    <th>Kode Rack</th>
                                    <th>Kode Bin</th>
                                    <th>Barang</th>
                                    <th>Exp Date</th>
                                    <th>Jumlah</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $i = 0;
                                foreach ($task as $row) {
                                    ?>
                                    <tr onclick="document.getElementById('myForm<?php echo $i; ?>').submit();">
                                        <td><?php echo $row['Name'] ?></td>
                                        <td><?php echo $row['BinCode'] ?></td>
                                        <td><?php echo $row['Keterangan'] ?></td>
                                        <td><?php echo $row['ExpDate'] ?></td>
                                        <td><?php echo $row['QtyKonversi'] ?>
                                            <form method="POST" action="<?php echo base_url() ?>index.php/replenish_manual/taruh_bin" id="myForm<?php echo $i; ?>">
                                                <input type="hidden" name="TransactionCode" value="<?php echo $row['TransactionCode'] ?>" />
                                                <input type="hidden" name="QueueNumber" value="<?php echo $row['QueueNumber'] ?>" />
                                                <input type="hidden" name="NoUrut" value="<?php echo $row['NoUrut'] ?>" />
                                                <input type="hidden" name="Keterangan" value="<?php echo $row['Keterangan'] ?>" />
                                                <input type="hidden" name="BinCode" value="<?php echo $row['BinCode'] ?>" />
                                                <input type="hidden" name="Qty" value="<?php echo $row['Qty'] ?>" />
                                                <input type="hidden" name="SKUCode" value="<?php echo $row['SKUCode'] ?>" />
                                                <input type="hidden" name="SrcRackSlot" value="<?php echo $row['SrcRackSlot'] ?>" />
                                                <input type="hidden" name="ExpDate" value="<?php echo $row['ExpDate'] ?>" />
                                                <input type="hidden" name="RackName" value="<?php echo $row['Name'] ?>" />
                                                <input type="hidden" name="QtyKonversi" value="<?php echo $row['QtyKonversi'] ?>" />
                                            </form>
                                        </td>

                                    </tr>
                                    <?php
                                    $i++;
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                    <?php
                    if ($jumlahtask == 0) {
                        ?>
                        <a href="<?php echo base_url() ?>index.php/replenish_manual/finish_replenish/<?php echo str_replace('=', '-', str_replace('/', '_', base64_encode($TransactionCode))); ?>" class="btn btn-large">Tutup Proses</a>
                        <?php
                    }
                    ?>
                </div><!-- /.span4 -->
            </div><!-- /.row -->
        </div><!-- /.container -->
        <?php
        $this->load->view("include/footer");
        ?>
    </body>
</html>


