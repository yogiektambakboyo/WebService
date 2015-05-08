<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <title>Tujuan Shipping Kembali</title>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <?php
        $this->load->view('include/header');
        ?>
    </head>

    <body>
        <div class="container">
            <div class="row">
                <div class="span12 center">
                    <h1>Tujuan Shipping Kembali</h1>
                    <hr />
                    <h5>PickList : <?php echo $this->session->userdata('ERPCode') ?></h5>
                    <h5>Barang: <?php echo $Keterangan ?></h5>
                    <h5>Exp Date: <?php echo date("d-m-Y", strtotime($ExpDate)) ?></h5>             
                    <h5>Bin: <?php echo $BinCode ?></h5>                    
                    <br />
                    <?php
                    echo validation_errors();
                    if ($this->session->flashdata('pesan')) {
                        ?>
                        <div class="alert alert-success">
                            <?php
                            echo $this->session->flashdata('pesan');
                            ?>
                        </div>
                        <?php
                    }
                    if ($this->session->flashdata('error')) {
                        ?>
                        <div class="alert alert-error">
                            <?php
                            echo $this->session->flashdata('error');
                            ?>
                        </div>
                        <?php
                    }
                    ?>

                    <form class="form-horizontal" method="POST" action="<?php echo base_url() ?>index.php/shipping/proses_kembali_barang">

                        <div class="control-group">
                            <label id="label"><strong>Bin Dest</strong></label> : <input type="text" class="input-medium" placeholder="Masukkan Bin" name="DestBin" />
                        </div>
                        <div class="control-group">
                            <label id="label"><strong>Rack Dest</strong></label> : <input type="text" class="input-medium" id="RackSlotCode" placeholder="Masukkan RackSlot" name="DestRackSlot" />
                            <span id="RackName"></span>
                        </div>
                        <div class="control-group">
                            <label id="label"><strong>Jumlah</strong></label> : <input type="text" class="input-medium" 
                            <?php
                            if ($Status2 == 'E') {
                                ?>
                                readonly="readonly"
                                <?php
                                }
                                ?>
                                placeholder="Masukkan Jumlah" name="Qty" value="<?php echo set_value('Qty', $QtyNeedNow) ?>"/>
                        
                                <select name="Rasio" class="input-small">
                                    <?php
                                    foreach ($Rasio as $row) {
                                        ?>
                                        <option value="<?php echo $row['Rasio']; ?>"
                                        <?php
                                        if ($row['Rasio'] == 1) {
                                            ?>
                                                    selected="selected"
                                                    <?php
                                                }
                                                ?>

                                                ><?php echo $row['Satuan']; ?></option>
                                                <?php
                                            }
                                            ?>    
                                </select>
                        </div>
                        <div class="control-group">
                            <input type="hidden" name="Keterangan" value="<?php echo $Keterangan; ?>">
                            <input type="hidden" name="QtyNeedNow" value="<?php echo $QtyNeedNow; ?>">
                            <input type="hidden" name="ExpDate" value="<?php echo $ExpDate; ?>">
                            <input type="hidden" name="NoUrut" value="<?php echo $NoUrut; ?>">
                            <input type="hidden" name="SKUCode" value="<?php echo $SKUCode; ?>">
                            <input type="hidden" name="BinCode" value="<?php echo $BinCode; ?>">
                            <input type="hidden" name="WHCode" value="<?php echo $WHCode; ?>">
                            <input type="hidden" name="Status2" value="<?php echo $Status2; ?>">
                            <input type="submit" name="btnProses" class="btn btn-large" value="Proses"/>
                            <input type="hidden" id="refreshed" value="no">
                        </div>
                    </form>
                    <div class="container">
                        <div class="row">
                            <div class="span12 center">
                                <p><a href="<?php echo base_url() ?>index.php/shipping/list_kembali" class="btn btn-large btn-inverse">Kembali</a></p>
                            </div><!-- /.span4 -->
                        </div><!-- /.row -->
                    </div><!-- /.container -->
                </div><!-- /.span4 -->
            </div><!-- /.row -->
        </div><!-- /.container -->
        <?php
        $this->load->view("include/footer");
        ?>
        <script type="text/javascript">
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
