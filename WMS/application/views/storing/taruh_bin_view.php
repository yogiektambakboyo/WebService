<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <title>Taruh Bin</title>
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
                    <h1>Taruh Bin</h1>
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
                        Dest Rack :
                        <strong>
                            <?php
                            echo $DestRackSlot;
                            ?>
                        </strong>
                    </label>
                    <form class="form-horizontal" method="POST" action="<?php echo base_url() ?>index.php/storing/proses_taruh_bin">
                        <div class="control-group">
                            <label id="label"><strong>Bin</strong></label> : <input type="text" class="input-medium" placeholder="Masukkan Kode Bin" maxlength="7" name="kodebin" id="BinCode"/>
                            <div class="controls">

                            </div>
                        </div>
                        <div class="control-group">
                            <label id="label"><strong>Rack Dest</strong></label> : <input type="text" class="input-medium" id="RackSlotCode" maxlength="8" placeholder="Masukkan Kode Rack" name="koderack"/>
                            <span id="RackName"></span>
                            <div class="controls">

                            </div>
                        </div>
                        <div class="control-group">
                            <label id="label"><strong>Qty</strong></label> : <input type="text" class="input-small" readonly="readonly" placeholder="Jumlah Barang" name="jumlah" id="jumlah" value="<?php echo $Qty/$Ratio; ?>"/>
                            <select name="satuan" class="input-small">
                                <?php
                                foreach ($Satuan as $row) {
                                    ?>
                                    <option value="<?php echo $row['Rasio']; ?>"
                                    <?php
                                    if ($row['Satuan'] == $RatioName) {
                                        ?>
                                                selected="selected"
                                                <?php
                                            }else{
                                                ?>
                                                disabled="disabled"
                                                <?php
                                            }
                                            ?>
                                            ><?php echo $row['Satuan']; ?></option>
                                        <?php } ?>    
                            </select>
                            <div class="controls">

                            </div>
                        </div>
                        <div class="control-group">
                            <label id="label"><strong>Di Gang</strong></label> : 
                            <select name="isonaisle" class="input-small">
                                <option value="1">Ya</option>
                                <option value="0">Tidak</option>
                            </select>
                        </div>
                        <div class="control-group" id="BinDest">
                            <label id="label"><strong>Bin Dest</strong></label> : 
                            <input type="text" class="input-medium" placeholder="Kode Bin Tujuan" name="kodebindest" />
                            <div class="controls"></div>
                        </div>
                        <div class="control-group">
                            <input type="hidden" id="refreshed" value="no">
                            <input type="submit" name="btnProses" class="btn btn-large" value="Proses"/>
                            <div class="controls">

                            </div>
                        </div>
                        <input type="hidden" name="TransactionCode" value="<?php echo $TransactionCode ?>" />
                        <input type="hidden" name="QueueNumber" value="<?php echo $QueueNumber ?>" />
                        <input type="hidden" name="NoUrut" value="<?php echo $NoUrut ?>" />
                        <input type="hidden" name="Keterangan" value="<?php echo $Keterangan ?>" />
                        <input type="hidden" name="ERPCode" value="<?php echo $ERPCode ?>" />
                        <input type="hidden" name="BinCode" value="<?php echo $BinCode ?>" />
                        <input type="hidden" name="Ratio" value="<?php echo $Ratio ?>" />
                        <input type="hidden" name="RatioName" value="<?php echo $RatioName ?>" />
                        <input type="hidden" name="DestRackSlot" value="<?php echo $DestRackSlot ?>" />
                        <input type="hidden" name="Qty" value="<?php echo $Qty ?>" />
                        <input type="hidden" name="SKUCode" value="<?php echo $SKUCode ?>" />
                    </form>
                    <p><a href="<?php echo base_url() ?>index.php/storing/list_my_outstanding" class="btn btn-large btn-inverse">Kembali</a></p>
                </div><!-- /.span4 -->
            </div><!-- /.row -->
        </div><!-- /.container -->
        <?php
        $this->load->view("include/footer");
        ?>
        <script type="text/javascript">
            $( document ).ready(function() {
                $('#BinCode').focus();
            });
            function detectChangeRack(input, interval) {
                var previous = "";

                setInterval( function() {

                    var val = $("#RackSlotCode").val();
                    if (  val != previous ) {
                        previous = val;
                        var postdata = {'RackSlotCode':val};
                        var link = "<?php echo base_url() ?>index.php/storing/get_ajax_rack";
                        $.ajax({
                            type: 'POST',
                            url: link,
                            dataType: 'jsonp',
                            data: postdata,
                            jsonp: 'jsoncallback',
                            timeout: 5000,
                            success: function(data){
                                /*if(data.DestBin==true){
                                    $("#BinDest").html('<label id="label"><strong>Bin Dest</strong></label> : <input type="text" class="input-medium" placeholder="Kode Bin Tujuan" name="kodebindest" /><div class="controls"></div>');
                                }
                                else{
                                    $("#BinDest").html('');
                                }*/
                                $("#RackName").html(data.RackName);        
                            },
                            /*error: function (xhr, ajaxOptions, thrownError) {
                                alert(xhr.responseText);
                                alert(thrownError);
                            }*/
                            error: function(){
                                $("#RackName").html('');  
                                //$("#BinDest").html('');
                            }
                        });   
                        
                    }

                }, interval);
            }

            detectChangeRack($("#RackSlotCode").val(), 10);
            
            function changefocus(input, interval) {
                var previous = "";

                setInterval( function() {

                    var val = $("#BinCode").val();
                    if (  val != previous ) {
                        previous = val;
                        if ($("#BinCode").val().length == $("#BinCode").attr('maxLength')) {
                            $('#RackSlotCode').focus();
                          }
                    }

                }, interval);
            }

            changefocus($("#BinCode").val(), 10);
        </script>
    </body>
</html>
