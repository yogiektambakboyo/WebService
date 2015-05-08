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
                    <label>
                        Rack :
                        <strong>
                            <?php
                            echo $RackName;
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
                        Qty :
                        <strong>
                            <?php
                            echo $QtyKonversi;
                            ?>
                        </strong>
                    </label>
                    <?php
                    echo validation_errors();
                    if (isset($error)) {
                        echo "<div class='alert alert-error'><strong>" . $error . "</strong><br /></div>";
                    }
                    ?>
                   
                    <br />
                    <form class="form-horizontal" method="POST" action="<?php echo base_url() ?>index.php/replenish_manual/taruh_bin">
                        <div class="control-group">
                            <label id="label"><strong>Bin Temp</strong></label> : <input type="text" class="input-medium" placeholder="Kode Bin yang Dibawa" name="kodebin"/>
                            <div class="controls">

                            </div>
                        </div>
                        <div class="control-group">
                            <label id="label"><strong>Rack Dest</strong></label> : <input type="text" class="input-medium" id="RackSlotCode" placeholder="Kode Rack Tujuan" name="koderack"/>
                            <span id="RackName"></span>
                            <div class="controls">

                            </div>
                        </div>
                        <div class="control-group">
                            <label id="label"><strong>Bin Dest</strong></label> : <input type="text" class="input-medium" placeholder="Kode Bin Tujuan" name="kodebindest"/>
                            <div class="controls">

                            </div>
                        </div>
                        <div class="control-group">
                            <label id="label"><strong>Qty</strong></label> : <input type="text" class="input-small" placeholder="Jumlah" id="jumlahSKU" name="jumlahSKU"/>
                            <span id="satuan">

                            </span>
                        </div>
                        <div class="control-group">
                            <input type="hidden" name="TransactionCode" value="<?php echo $TransactionCode ?>" />
                            <input type="hidden" name="QueueNumber" value="<?php echo $QueueNumber ?>" />
                            <input type="hidden" name="NoUrut" value="<?php echo $NoUrut ?>" />
                            <input type="hidden" name="Keterangan" value="<?php echo $Keterangan ?>" />
                            <input type="hidden" name="BinCode" value="<?php echo $BinCode ?>" />
                            <input type="hidden" name="QtyAwal" value="<?php echo $Qty ?>" />
                            <input type="hidden" name="SKUCode" id="kodeSKU" value="<?php echo $SKUCode ?>" />
                            <input type="hidden" name="SrcRackSlot" value="<?php echo $SrcRackSlot ?>" />
                            <input type="hidden" name="ExpDate" value="<?php echo $ExpDate ?>" />
                            <input type="hidden" name="RackName" value="<?php echo $RackName ?>" />
                            <input type="hidden" name="QtyKonversi" value="<?php echo $QtyKonversi ?>" />
                            <input type="submit" name="btnProses" class="btn btn-large" value="Proses"/>
                            <input type="hidden" id="refreshed" value="no">
                            <div class="controls">

                            </div>
                        </div>
                    </form>
                    <p><a href="<?php echo base_url() ?>index.php/replenish_manual/list_task/<?php echo str_replace('=', '-', str_replace('/', '_', base64_encode($TransactionCode))); ?>" class="btn btn-inverse  btn-large">Kembali</a></p>
                </div><!-- /.span4 -->
            </div><!-- /.row -->
        </div><!-- /.container -->
        <?php
        $this->load->view("include/footer");
        ?>
        <script type="text/javascript">
            function detectChangeSatuan(input, interval) {
                var previous = "";

                setInterval( function() {

                    var val = $("#kodeSKU").val();
                    if (  val != previous ) {
                        previous = val;
                        var SKUCode=$("#kodeSKU").val();
                        var rasiohidden=$("#rasiohidden").val();
                        var postdata = {'SKUCode':SKUCode};
                        var link = "<?php echo base_url() ?>index.php/replenish_manual/get_ajax_satuan";
                        $.ajax({
                            type: 'POST',
                            url: link,
                            dataType: 'jsonp',
                            data: postdata,
                            jsonp: 'jsoncallback',
                            timeout: 5000,
                            success: function(data){
                                var str='<select name="rasio" class="input-small" id="rasio">';
                                $.each(data, function(i,item){
                                    str = str +'<option value="'+item["Rasio"]+'"';
                                    if(rasiohidden!='')
                                    {
                                        if(item["Rasio"]==rasiohidden)
                                        {
                                            str+='selected="selected"';
                                        }
                                    }
                                    str+='>'+item["Satuan"]+'</option>';
                                });
                                str+='</select>';
                                SKUCodelama=SKUCode;
                                $("#satuan").html(str);        
                            },
                            /*error: function (xhr, ajaxOptions, thrownError) {
                                alert(xhr.responseText);
                                alert(thrownError);
                            }*/
                            error: function(){
                                $("#satuan").html('');  
                            }
                        });   
                        
                    }

                }, interval);
            }
            detectChangeSatuan($("#kodeSKU").val(), 10);
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
        </script>
    </body>
</html>
