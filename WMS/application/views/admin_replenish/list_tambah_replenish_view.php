<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <title>Replenish Limit</title>
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
                    <h1>Rack dalam Limit</h1>
                    <hr />
                   
                    <div class="control-group">
                        <!-- div class="scroll2" -->
                            <table class="table table-bordered table-hover table-striped table-condensed">
                                <thead>
                                    <tr>
                                        <th>Rack Type</th>
                                        <th>Rack Slot</th>
                                        <th>SKU</th>
                                        <th>Barang</th>
                                        <th>Jumlah</th>
                                        <!--th>Proses</th-->
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $i = 0;
                                    foreach ($outstanding as $row) {
                                      
                                            ?>

                                            <tr onclick="document.getElementById('myForm<?php echo $i; ?>').submit();">
                                                <td><?php echo $row['RackType'] ?></td>
                                                <td><?php echo $row['Name'] ?></td>
                                                <td><?php echo $row['SKUCode'] ?></td>
                                                <td><?php echo $row['Keterangan'] ?></td>
                                                <td><?php echo $row['Jml'] ?>
                                                    <form method="POST" action="<?php echo base_url() ?>index.php/admin_replenish/pilih_barang_ambil" id="myForm<?php echo $i; ?>">
                                                        <input type="hidden" name="RackType" value="<?php echo $row['RackType'] ?>" />
                                                        <input type="hidden" name="SKUCode" value="<?php echo $row['SKUCode'] ?>" />
                                                        <input type="hidden" name="NamaRackDest" value="<?php echo $row['Name'] ?>" />
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
                    

                </div><!-- /.span4 -->
            </div><!-- /.row -->
        </div><!-- /.container -->
        <?php
        $this->load->view("include/footer");
        ?>
    </body>
</html>


