<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <title>Jumlah <?php echo $Keterangan; ?></title>
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
                    <h1>Jumlah <?php echo $Keterangan; ?></h1>
                    <hr />
                    <?php
                    echo validation_errors();
                    if (isset($error)) {
                        ?>
                        <div class='alert alert-error'><strong><?php echo $error; ?></strong><br /></div>
                        <?php
                    }
                    ?>
                    <div class="control-group">
                        <label id="label"><strong>Bin Dest</strong></label> : <input type="text" readonly="readonly" class="input-medium" placeholder="Kode Bin Tujuan" value="<?php echo $this->session->userdata('kodebindest'); ?>" />
                        <div class="controls">

                        </div>
                    </div>
                    <br />
                    <form class="form-horizontal" method="POST" action="<?php echo base_url() ?>index.php/shipping/proses_masuk_barang">

                        <div class="control-group">
                            <label id="label"><strong>Qty</strong></label> : <input type="text" class="input-medium" placeholder="Masukkan Jumlah Barang" name="jumlah" value="<?php echo $Qty; ?>"/>
                            <div class="controls">

                            </div>
                        </div>
                        <div class="control-group">
                            <select name="satuan" >
<?php
foreach ($satuan as $row) {
    ?>
                                    <option  
                                    <?php
                                    if ($row['Satuan'] == 'PCS') {
                                        echo 'selected="selected"';
                                    }
                                    ?>
                                        value="<?php echo $row['Rasio']; ?>" ><?php echo $row['Satuan']; ?></option>
                                    <?php
                                }
                                ?>
                            </select>
                            <div class="controls">

                            </div>
                        </div>
                        <input type="hidden" name="TransactionCode" value="<?php echo $TransactionCode; ?>" />
                        <input type="hidden" name="SKUCode" value="<?php echo $SKUCode; ?>" />
                        <input type="hidden" name="Keterangan" value="<?php echo $Keterangan; ?>" />
                        <input type="hidden" name="Qty" value="<?php echo $Qty; ?>" />
                        <input type="hidden" name="NoUrut" value="<?php echo $NoUrut; ?>" />
                        <div class="control-group">
                            <input type="hidden" id="refreshed" value="no">
                            <input type="submit" name="btnProses" class="btn btn-large" value="Proses"/>
                            <div class="controls">

                            </div>
                        </div>
                    </form>
                    <p><a href="<?php echo base_url() ?>index.php/shipping/list_sku_bin" class="btn btn-large btn-inverse">Kembali</a></p>
                </div><!-- /.span4 -->
            </div><!-- /.row -->
        </div><!-- /.container -->
<?php
$this->load->view("include/footer");
?>

    </body>
</html>
