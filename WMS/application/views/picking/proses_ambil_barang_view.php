<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <title>Ambil Barang</title>
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
                    <h1>Ambil Barang</h1>
                    <hr />
                    <?php
                    echo validation_errors();
                    if (isset($error)) {
                        echo "<div class='alert alert-error'><strong>" . $error . "</strong><br /></div>";
                    }
                    ?>
                    <label id="label"><strong>Bin Temp</strong></label> : <input type="text" readonly="readonly" class="input-large" placeholder="Kode Bin Sekarang" value="<?php echo $this->session->userdata('BinNow'); ?>" />
                    <br />
                    <form class="form-horizontal" method="POST" action="<?php echo base_url() ?>index.php/picking/proses_ambil_barang">
                        <label id="label"><strong>Barang</strong></label> : <?php echo $Keterangan; ?>
                        <br/>
                        <label id="label"><strong>Jumlah</strong></label> : <?php echo $Konversi; ?>
                            
                        <div class="control-group">
                            <label id="label"><strong>Rack Src</strong></label> : <input type="text" id="RackSlotCode" maxlength="8" class="input-medium" placeholder="Masukkan Kode Rack Asal Barang" name="koderack"/>
                            <span id="RackName"></span>
                            <div class="controls">

                            </div>
                        </div>
                        <div class="control-group">
                            <label id="label"><strong>Bin Src</strong></label> : <input type="text" class="input-medium" id="BinCode" maxlength="7" placeholder="Masukkan Kode Bin Asal Barang" name="kodebin"/>
                            <div class="controls">

                            </div>
                        </div>
                        <div class="control-group">
                            <label id="label"><strong>Qty</strong></label> : <input type="text" class="input-small" placeholder="Jumlah Barang Terambil" name="jumlah"/>
                            <select name="satuan" class="input-small" >
                                <?php
                                foreach ($satuan as $row) {
                                    ?>
                                    <option value="<?php echo $row['Rasio']; ?>" ><?php echo $row['Satuan']; ?></option>
                                    <?php
                                }
                                ?>
                            </select>
                            <div class="controls">

                            </div>
                        </div>
                        <input type="hidden" name="Konversi" value="<?php echo $Konversi; ?>"/>
                        <input type="hidden" name="Keterangan" value="<?php echo $Keterangan; ?>"/>
                        <input type="hidden" name="TransactionCode" value="<?php echo $TransactionCode; ?>" />
                        <input type="hidden" name="SKUCode" value="<?php echo $SKUCode; ?>" />
                        <input type="hidden" name="Qty" value="<?php echo $Qty; ?>" />
                        <input type="hidden" name="Needed" value="<?php echo $Needed; ?>" />
                        <input type="hidden" name="NoUrut" value="<?php echo $NoUrut; ?>" />
                        <input type="hidden" name="AddTask" value="<?php echo $AddTask; ?>" />
                        <div class="control-group">
                            <input type="submit" name="btnProses" class="btn btn-large" value="Proses"/>
                            <input type="hidden" id="refreshed" value="no">
                            <div class="controls">

                            </div>
                        </div>
                    </form>
                    <p><a href="<?php echo base_url() ?>index.php/picking/refresh_all_outstanding" class="btn btn-inverse  btn-large">Kembali</a></p>
                </div><!-- /.span4 -->
            </div><!-- /.row -->
        </div><!-- /.container -->
        <?php
        $this->load->view("include/footer");
        ?>
        <script type="text/javascript" >
            $( document ).ready(function() {
                $('#RackSlotCode').focus();
            });
            function changefocus(input, interval) {
                var previous = "";

                setInterval( function() {

                    var val = $("#RackSlotCode").val();
                    if (  val != previous ) {
                        previous = val;
                        if ($("#RackSlotCode").val().length == $("#RackSlotCode").attr('maxLength')) {
                            $('#BinCode').focus();
                          }
                    }

                }, interval);
            }

            changefocus($("#RackSlotCode").val(), 10);
            function detectChangeRack(input, interval) {
                var previous = "";

                setInterval( function() {

                    var val = $("#RackSlotCode").val();
                    if (  val != previous ) {
                        previous = val;
                        var postdata = {'RackSlotCode':val};
                        var link = "<?php echo base_url() ?>index.php/receive/get_ajax_rack";
                        $.ajax({
                            type: 'POST',
                            url: link,
                            dataType: 'jsonp',
                            data: postdata,
                            jsonp: 'jsoncallback',
                            timeout: 5000,
                            success: function(data){
                                
                                $("#RackName").html(data.RackName);        
                            },
                            /*error: function (xhr, ajaxOptions, thrownError) {
                                alert(xhr.responseText);
                                alert(thrownError);
                            }*/
                            error: function(){
                                $("#RackName").html('');  
                            }
                        });   
                        
                    }

                }, interval);
            }

            detectChangeRack($("#RackSlotCode").val(), 10);
        </script>
    </body>
</html>
