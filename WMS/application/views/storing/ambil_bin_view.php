<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <title>Ambil Bin</title>
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
                    <h1>Ambil Bin</h1>
                    <hr />
                    <?php
                    echo validation_errors();
                    if (isset($error)) {
                        echo "<div class='alert alert-error'><strong>" . $error . "</strong><br /></div>";
                    }
                    ?>
                    <label>
                        <?php
                        if ($this->session->userdata('ProjectCode') == 'BPB') {
                            echo "BPB :";
                        } else {
                            echo "Picklist Retur :";
                        }
                        ?>
                        <strong>
                            <?php
                            echo $ERPCode;
                            ?>
                        </strong>
                    </label>
                    <label>
                        Kode Bin :
                        <strong>
                            <?php
                            echo $BinCode;
                            ?>
                        </strong>
                    </label>
                    <label>
                        Barang :
                        <strong>
                            <?php
                            echo $Keterangan;
                            ?>
                        </strong>
                    </label>
                    <label>
                        Jumlah Barang :
                        <strong>
                            <?php
                            echo $Qtykonversi;
                            ?>
                        </strong>
                    </label>
                    <label>
                        Dest Rack :
                        <strong>
                            <?php
                            echo $DestRackSlot;
                            ?>
                        </strong>
                    </label>
                    <form class="form-horizontal" method="POST" action="<?php echo base_url() ?>index.php/storing/proses_ambil_bin">
                        
                        <input type="hidden" name="kodebin" id="kodebin" value="<?php echo $BinCode ?>"/>
                        <input type="hidden" name="TransactionCode" value="<?php echo $TransactionCode; ?>" />
                        <input type="hidden" name="NoUrut" value="<?php echo $NoUrut; ?>" />
                        <input type="hidden" name="QueueNumber" value="<?php echo $QueueNumber; ?>" />
                        <input type="hidden" name="Keterangan" value="<?php echo $Keterangan ?>" />
                        <input type="hidden" name="ERPCode" value="<?php echo $ERPCode ?>" />
                        <input type="hidden" name="BinCode" value="<?php echo $BinCode ?>" />
                        <input type="hidden" name="Ratio" value="<?php echo $Ratio ?>" />
                        <input type="hidden" name="Qty" value="<?php echo $Qty ?>" />
                        <input type="hidden" name="SKUCode" value="<?php echo $SKUCode ?>" />
                        <input type="hidden" name="RatioName" value="<?php echo $RatioName ?>" />
                        <input type="hidden" name="DestRackSlot" value="<?php echo $DestRackSlot ?>" />
                            
                        <div class="control-group">
                            <input type="hidden" id="refreshed" value="no">
                            <input type="submit" name="btnProses1" class="btn btn-large" value="Proses & Ambil Kembali"/>
                            <div class="controls">

                            </div>
                        </div>
                    </form>
                    <form class="form-horizontal" method="POST" action="<?php echo base_url() ?>index.php/storing/proses_ambil_bin">
                        
                        <input type="hidden" name="kodebin" id="kodebin" value="<?php echo $BinCode ?>"/>
                        <input type="hidden" name="TransactionCode" value="<?php echo $TransactionCode; ?>" />
                        <input type="hidden" name="NoUrut" value="<?php echo $NoUrut; ?>" />
                        <input type="hidden" name="QueueNumber" value="<?php echo $QueueNumber; ?>" />
                        <input type="hidden" name="Keterangan" value="<?php echo $Keterangan ?>" />
                        <input type="hidden" name="ERPCode" value="<?php echo $ERPCode ?>" />
                        <input type="hidden" name="BinCode" value="<?php echo $BinCode ?>" />
                        <input type="hidden" name="Ratio" value="<?php echo $Ratio ?>" />
                        <input type="hidden" name="Qty" value="<?php echo $Qty ?>" />
                        <input type="hidden" name="SKUCode" value="<?php echo $SKUCode ?>" />
                        <input type="hidden" name="RatioName" value="<?php echo $RatioName ?>" />
                        <input type="hidden" name="DestRackSlot" value="<?php echo $DestRackSlot ?>" />
                            
                        <div class="control-group">
                            <input type="hidden" id="refreshed" value="no">
                            <input type="submit" name="btnProses2" class="btn btn-large" value="Proses & Simpan"/>
                            <div class="controls">

                            </div>
                        </div>
                    </form>
                    <p><a href="<?php echo base_url() ?>index.php/storing/back_tambah_bpb_storing" class="btn btn-large btn-inverse">Kembali</a></p>
                </div><!-- /.span4 -->
            </div><!-- /.row -->
        </div><!-- /.container -->
        <?php
        $this->load->view("include/footer");
        ?>
    </body>
</html>
