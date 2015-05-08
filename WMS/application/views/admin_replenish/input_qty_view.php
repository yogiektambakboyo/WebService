<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <title>Ambil Dari Rack <?php echo $NamaRackSrc; ?></title>
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
                    <h1>Ambil Dari Rack <?php echo $NamaRackSrc; ?></h1>
                    <hr />
                    <?php
                    echo validation_errors();
                    if (isset($error)) {
                        echo "<div class='alert alert-error'><strong>" . $error . "</strong><br /></div>";
                    }
                    ?>
                    <label id="label"><strong>Untuk Rack</strong></label> : <input type="text" readonly="readonly" class="input-medium" placeholder="Menuju Ke" value="<?php echo $NamaRackDest; ?>"/>
                    

                    <form class="form-horizontal" method="POST" action="<?php echo base_url() ?>index.php/admin_replenish/input_qty">
                        <div class="control-group">
                            <label id="label"><strong>Jumlah</strong></label> : <input type="text" class="input-medium" placeholder="Masukkan Jumlah Barang" name="jumlah" value="<?php echo $jumlah; ?>"/>
                            <div class="controls">
                                <input type="hidden" name="RackType" value="<?php echo $RackType ?>" />
                                <input type="hidden" name="SKUCode" value="<?php echo $SKUCode ?>" />
                                <input type="hidden" name="BinCode" value="<?php echo $BinCode ?>" />
                                <input type="hidden" name="WHCode" value="<?php echo $WHCode ?>" />
                                <input type="hidden" name="ExpDate" value="<?php echo $ExpDate ?>" />
                                <input type="hidden" name="jumlahawal" value="<?php echo $jumlahawal ?>" />
                                <input type="hidden" name="NamaRackDest" value="<?php echo $NamaRackDest ?>" />
                                <input type="hidden" name="NamaRackSrc" value="<?php echo $NamaRackSrc ?>" />
                                <input type="hidden" name="DestRackSlot" value="<?php echo $DestRackSlot ?>" />
                                <input type="hidden" name="RackSlotCode" value="<?php echo $RackSlotCode ?>" />   
                            </div>
                        </div>
                        <div class="control-group">
                            <input type="submit" name="btnProses" class="btn btn-large" value="Proses"/>
                            <div class="controls">

                            </div>
                        </div>
                    </form>
                    <p>
                    <form method="POST" action="<?php echo base_url() ?>index.php/admin_replenish/pilih_barang_ambil">
                        <input type="hidden" name="RackType" value="<?php echo $RackType ?>" />
                        <input type="hidden" name="SKUCode" value="<?php echo $SKUCode ?>" />
                        <input type="hidden" name="NamaRackDest" value="<?php echo $NamaRackDest ?>" />
                        <input type="hidden" name="RackSlotCode" value="<?php echo $RackSlotCode ?>" /> 
                        <input type="submit" name="btnkembali" class="btn btn-large btn-inverse" value="Kembali"/>
                    </form>
                    </p>
                </div><!-- /.span4 -->
            </div><!-- /.row -->
        </div><!-- /.container -->
        <?php
        $this->load->view("include/footer");
        ?>
    </body>
</html>
