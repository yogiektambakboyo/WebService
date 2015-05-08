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
                    if ($this->session->flashdata('error')) {
                        ?>
                        <div class="alert alert-error">
                            <strong><?php echo $this->session->flashdata('error'); ?></strong>
                        </div>
                        <?php
                    }
                    ?>
                    <label>
                        Kode Bin :
                        <strong>
                            <?php
                            echo $this->session->userdata('binAwal');
                            ?>
                        </strong>
                    </label>
                    <label>
                        Barang :
                        <strong>
                            <?php
                            echo $this->session->userdata('ket');
                            ?>
                        </strong>
                    </label>
                    <label>
                        Jumlah Barang :
                        <strong>
                            <?php
                            echo $this->session->userdata('qtyKonv');
                            ?>
                        </strong>
                    </label>
                    <label>
                        Dest Rack :
                        <strong>
                            <?php
                            echo $this->session->userdata('rackTjnNama');
                            ?>
                        </strong>
                    </label>
                    <form class="form-horizontal" method="POST" action="">
                        <div class="control-group">
                            <label id="label"><strong>Bin Asal</strong></label> : <input type="text" class="input-medium" maxlength="7" placeholder="Masukkan Kode Bin" name="kodebin" id="kodebin"/>
                            <input type="hidden" name="SrcBin" value="<?php echo $this->session->userdata('binAwal') ?>" />
                            <div class="controls">

                            </div>
                        </div>
                        <div class="control-group">
                            <label id="label"><strong>Rack Asal</strong></label> : <input type="text" class="input-medium" maxlength="8" placeholder="Masukkan Kode Rack" name="RackSlotCode" id="RackSlotCode"/>
                            <input type="hidden" name="SrcRack" value="<?php echo $this->session->userdata('rackAwalKode') ?>" />
                            <div class="controls">

                            </div>
                        </div>
                        <div class="control-group">
                            <label id="label"><strong>Qty</strong></label> : 
                            <input type="text" class="input-small" placeholder="Jumlah Barang" name="jumlahSKU" id="jumlahSKU" value="<?php if (!isset($brgnow)) echo set_value('jumlahSKU'); ?>"/>
                            <select name="satuan" class="input-small">
                                <?php
                                foreach ($Satuan as $row) {
                                    ?>
                                    <option value="<?php echo $row['Rasio']; ?>" ><?php echo $row['Satuan']; ?></option>
                                <?php } ?>    
                            </select>
                        </div>
                        <div class="control-group">
                            <label id="label"><strong>Bin Temp</strong></label> : <input type="text" class="input-medium" placeholder="Masukkan Kode Bin" name="kodebin2" id="kodebin2"/>
                            <div class="controls">

                            </div>
                        </div>
                        <div class="control-group">
                            <input type="hidden" id="refreshed" value="no">
                            <input type="submit" name="btnProses" class="btn btn-large" value="Proses"/>
                            <div class="controls">

                            </div>
                        </div>
                    </form>
                    <p><a href="<?php echo base_url() ?>index.php/replenish/all_outstanding" class="btn btn-large btn-inverse">Kembali</a></p>
                </div><!-- /.span4 -->
            </div><!-- /.row -->
        </div><!-- /.container -->
        <?php
        $this->load->view("include/footer");
        ?>
        <script type="text/javascript">
            $( document ).ready(function() {
                $('#kodebin').focus();
            });
            function changefocus(input, interval) {
                var previous = "";

                setInterval( function() {

                    var val = $("#kodebin").val();
                    if (  val != previous ) {
                        previous = val;
                        if ($("#kodebin").val().length == $("#kodebin").attr('maxLength')) {
                            $('#RackSlotCode').focus();
                        }
                    }

                }, interval);
            }

            changefocus($("#kodebin").val(), 10);
        </script>
    </body>
</html>
