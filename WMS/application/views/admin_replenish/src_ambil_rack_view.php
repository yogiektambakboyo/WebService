<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <title>Replenish Sumber</title>
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
                    <h1>Diambil dari Rack</h1>
                    <hr />
                   <label id="label"><strong>Untuk Rack</strong></label> : <input type="text" readonly="readonly" class="input-medium" placeholder="Menuju Ke" value="<?php echo $NamaRackDest; ?>"/>
                    <div class="control-group">
                        <!-- div class="scroll2" -->
                            <table class="table table-bordered table-hover table-striped table-condensed">
                                <thead>
                                    <tr>
                                        <th>Rack Type</th>
                                        <th>Rack Slot</th>
                                        <th>Bin</th>
                                        <th>SKU</th>
                                        <th>Barang</th>
                                        <th>Exp Date</th>
                                        <th>Jumlah</th>
                                        <!--th>Proses</th-->
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $i = 0;
                                    foreach ($src as $row) {
                                      
                                            ?>

                                            <tr onclick="document.getElementById('myForm<?php echo $i; ?>').submit();">
                                                <td><?php echo $row['RackType'] ?></td>
                                                <td><?php echo $row['Name'] ?></td>
                                                <td><?php echo $row['BinCode'] ?></td>
                                                <td><?php echo $row['SKUCode'] ?></td>
                                                <td><?php echo $row['Keterangan'] ?></td>
                                                <td><?php echo date('d-m-Y',strtotime($row['ExpDate'])); ?></td>
                                                <td><?php echo $row['Qty'] ?>
                                                    <form method="POST" action="<?php echo base_url() ?>index.php/admin_replenish/input_qty" id="myForm<?php echo $i; ?>">
                                                        <input type="hidden" name="DestRackSlot" value="<?php echo $destrackslot ?>" />
                                                        <input type="hidden" name="NamaRackDest" value="<?php echo $NamaRackDest ?>" />
                                                        <input type="hidden" name="RackType" value="<?php echo $row['RackType'] ?>" />
                                                        <input type="hidden" name="SKUCode" value="<?php echo $row['SKUCode'] ?>" />
                                                        <input type="hidden" name="BinCode" value="<?php echo $row['BinCode'] ?>" />
                                                        <input type="hidden" name="WHCode" value="<?php echo $row['WHCode'] ?>" />
                                                        <input type="hidden" name="ExpDate" value="<?php echo $row['ExpDate'] ?>" />
                                                        <input type="hidden" name="NamaRackSrc" value="<?php echo $row['Name'] ?>" />
                                                        <input type="hidden" name="Jml" value="<?php echo $row['Qty'] ?>" />
                                                        <input type="hidden" name="RackSlotCode" value="<?php echo $row['RackSlotCode'] ?>" />
                                                    </form>
                                                </td>
                                            </tr>
                                            <?php
                                        $i++;
                                    }
                                    ?>
                                </tbody>
                            </table>
                        <!--/div -->
                    </div>
                    <div class="container">
                        <div class="row">
                            <div class="span12 center">
                                <p><a href="<?php echo base_url() ?>index.php/admin_replenish/tambah_replenish" class="btn btn-large btn-inverse">Kembali</a></p>
                            </div><!-- /.span4 -->
                        </div><!-- /.row -->
                    </div><!-- /.container -->

                </div><!-- /.span4 -->
            </div><!-- /.row -->
        </div><!-- /.container -->
        <?php
        $this->load->view("include/footer");
        ?>
    </body>
</html>


