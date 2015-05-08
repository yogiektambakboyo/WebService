<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <title>Tambah Barang Kembali</title>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <?php
        $this->load->view('include/header');
        ?>
         <link href="<?php echo base_url() ?>files/mobiscroll/css/mobiscroll.scroller.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo base_url() ?>files/mobiscroll/css/mobiscroll.scroller.jqm.css" rel="stylesheet" type="text/css" />

        <link href="css/mobiscroll.animation.css" rel="stylesheet" type="text/css" />
    </head>

    <body>
        <div class="container">
            <div class="row">
                <div class="span12 center">
                    <h1>Tambah Barang Kembali</h1>
                    <hr />
                    <h5><?php echo $this->session->userdata('bpb') ?></h5>
                    <h5><?php echo $this->session->userdata('namaSupplier') ?></h5>
                    <br />
                    <?php
                    echo validation_errors();
                    if (isset($error)) {
                        ?>
                        <div class="alert alert-error">
                            <?php
                            echo $error;
                            ?>
                        </div>
                        <?php
                    }
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

                    <form class="form-horizontal" method="POST" action="<?php echo base_url() ?>index.php/shipping/tambahbrgkembali">
                        <div class="control-group input-append">
                            <input type="text" class="input-large" placeholder="Kode Barang / Barcode" name="barcodesku" id="barcodesku"/>
                            <a href="<?php echo base_url() ?>index.php/shipping/cari_barang" class="btn">Cari</a>
                        </div>
                        <div class="control-group">
                            <label id="label"><strong>Kode Bin</strong></label> : <input type="text" id="BinCode" maxlength="7" class="input-medium" placeholder="Masukkan Kode Bin" name="BinCode" value="<?php echo set_value('BinCode', isset($BinCode) ? $BinCode : '') ?>"/>
                        </div>
                        <div class="control-group input-append" id="SKUdiv">
                            <label id="label"><strong>SKU</strong></label> : <input type="text" class="input-medium" placeholder="Kode Barang" name="kodeSKU" id="kodeSKU" readonly="readonly" value="<?php echo set_value('kodeSKU', isset($barang) ? $barang['kode'] : '') ?>"/>
                        </div>
                        <div class="control-group">
                            <label id="label"><strong>Nama Barang</strong></label> : <input type="text" class="input-large" placeholder="Nama Barang" name="namaSKU" id="namaSKU" readonly="readonly" value="<?php echo set_value('namaSKU', isset($barang) ? $barang['keterangan'] : '') ?>"/>
                        </div>
                        
                        <div class="control-group">
                             <label id="label"><strong>Exp Date</strong></label> : <input type="text" class="input-medium tanggal" placeholder="Masukkan ED SKU" name="edSKU" value="<?php echo set_value('edSKU', isset($ExpDate) ? $ExpDate : '') ?>"/>
                        </div>
                        <div class="control-group">
                            <label id="label"><strong>Qty</strong></label> : 
                            <input type="text" class="input-small" id="Qty" placeholder="Jumlah Barang" name="jumlahSKU" value="<?php echo set_value('jumlahSKU') ?>"/>
                            <span id="satuan">

                            </span>
                        </div>
                        <div class="control-group">
                            <input type="submit" name="btnTambah" class="btn btn-large" value="Tambah"/>
                            <input type="hidden" id="refreshed" value="no">
                        </div>
                    </form>
                    <p><a href="<?php echo base_url() ?>index.php/shipping/list_kembali" class="btn btn-inverse btn-large">Kembali</a></p>
                </div><!-- /.span4 -->
            </div><!-- /.row -->
        </div><!-- /.container -->
        <?php
        $this->load->view("include/footer");
        ?>
        
        <script src="<?php echo base_url() ?>files/mobiscroll/js/mobiscroll.core.js"></script>
        <script src="<?php echo base_url() ?>files/mobiscroll/js/mobiscroll.scroller.js" type="text/javascript"></script>

        <script src="<?php echo base_url() ?>files/mobiscroll/js/mobiscroll.datetime.js" type="text/javascript"></script>
        <script src="<?php echo base_url() ?>files/mobiscroll/js/mobiscroll.select.js" type="text/javascript"></script>

        <script type="text/javascript">
           var d = new Date();
            $(function(){
                $('.tanggal').mobiscroll().date({
                     theme: 'default',
                     display: 'modal',
                     mode: 'clickpick',
                     dateFormat: 'yyyy/mm/dd',
                     startYear: d.getFullYear()
                });    
               
            });
            function detectChangeBarcodeSKU(input, interval) {
                var previous = "";

                setInterval( function() {

                    var val = $("#barcodesku").val();
                    if (  val != previous ) {
                        previous = val;
                        var barcodeSKU=$("#barcodesku").val();
                        var postdata = {'barcodeSKU':barcodeSKU};
                        if($.trim(barcodeSKU)!=''){
                    
                            var link = "<?php echo base_url() ?>index.php/shipping/get_ajax_barang";
                            $.ajax({
                                type: 'POST',
                                url: link,
                                dataType: 'jsonp',
                                data: postdata,
                                jsonp: 'jsoncallback',
                                timeout: 5000,
                                success: function(data){
                                    if(data.Status==true)
                                    {
                                        $("#kodeSKU").val(data.Kode);
                                        $("#namaSKU").val(data.Keterangan);
                                    }
                                    else{
                                        window.location.replace("<?php echo base_url() ?>index.php/shipping/cari_barang/"+barcodeSKU);
                                    }
                                },
                                /*error: function (xhr, ajaxOptions, thrownError) {
                                alert(xhr.responseText);
                                alert(thrownError);
                            }*/
                                error: function(){
                                    $("#kodeSKU").val('');
                                    $("#namaSKU").val('');
                                }
                            }); 
                        }
                        else
                        {
                            $("#kodeSKU").val('');
                            $("#namaSKU").val('');
                        }
                        
                    }

                }, interval);
            }

            detectChangeBarcodeSKU($("#barcodesku").val(), 10);
            function detectChange(input, interval) {
                var previous = "";

                setInterval( function() {

                    var val = $("#kodeSKU").val();
                    if (  val != previous ) {
                        previous = val;
                        var SKUCode=$("#kodeSKU").val();
                        var postdata = {'SKUCode':SKUCode};
                        var link = "<?php echo base_url() ?>index.php/receive/get_ajax_satuan";
                        $.ajax({
                            type: 'POST',
                            url: link,
                            dataType: 'jsonp',
                            data: postdata,
                            jsonp: 'jsoncallback',
                            timeout: 5000,
                            success: function(data){
                                var str='<select name="ratio" class="input-small" id="rasio">';
                                $.each(data, function(i,item){
                                    str = str +'<option value="'+item["Rasio"]+'"';
                                    
                                    if(item["Satuan"]==item["DERP"])
                                    {
                                        str+='selected="selected"';
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

            detectChange($("#kodeSKU").val(), 10);
            
            function detectChangeURL(input, interval) {
                var previous = "";

                setInterval( function() {

                    var val = $("#BinCode").val();
                    if (  val != previous ) {
                        previous = val;
                        $("#SKUdiv a").attr("href", "<?php echo base_url() ?>index.php/shipping/cari_barang/"+$("#BinCode").val());
                        
                    }

                }, interval);
            }

            detectChangeURL($("#BinCode").val(), 10);
          function changefocus(input, interval) {
                var previous = "";

                setInterval( function() {

                    var val = $("#BinCode").val();
                    if (  val != previous ) {
                        previous = val;
                        if ($("#BinCode").val().length == $("#BinCode").attr('maxLength')) {
                            $('#Qty').focus();
                          }
                    }

                }, interval);
            }

            changefocus($("#BinCode").val(), 10);
        </script>
    </body>
</html>
