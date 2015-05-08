<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <title>Receive</title>
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
                    <h1>Receive</h1>
                    <hr />
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
                    <div id="message">
                    </div>
                    <input type="hidden" id="rowid"/>
                    <input type="hidden" id="rasiohidden" value=""/>
                    <div class="control-group">
                        <label id="label"><strong>Rack</strong></label> : <input type="text" class="input-medium" readonly="readonly" placeholder="Kode Rack" name="koderack" value="<?php echo $this->session->userdata('koderack') ?>"/>
                    </div>
                    <div class="control-group">
                        <label id="label"><strong>Barcode SKU</strong></label> : <input type="text" class="input-medium" placeholder="Kode Barang / Barcode" name="barcodesku" id="barcodesku" />
                    </div>
                    <div class="control-group input-append">
                        <label id="label"><strong>SKU</strong></label> : <input type="text" class="input-medium" readonly="readonly" placeholder="Kode Barang" id="kodeSKU" value="<?php echo set_value('kodeSKU', isset($barang) ? $barang['kode'] : '') ?>"/>
                        <a href="<?php echo base_url() ?>index.php/receive/cari_barang" class="btn">Cari</a>
                    </div>
                    <div class="control-group">
                        <label id="label"><strong>Barang</strong></label> : <input type="text" class="input-large" placeholder="Nama Barang" name="namaSKU" id="namaSKU" readonly="readonly" value="<?php echo set_value('namaSKU', isset($barang) ? $barang['keterangan'] : '') ?>"/>
                    </div>
                    <div class="control-group">
                        <label id="label"><strong>Bin</strong></label> : <input type="text" class="input-medium" placeholder="Kode Bin" id="kodeBIN" maxlength="7" value="<?php echo set_value('kodeBIN') ?>"/>
                    </div>

                    <div class="control-group">
                        <label id="label"><strong>Exp Date</strong></label> : <input type="text" class="input-medium tanggal" placeholder="Masukkan ED SKU" id="edSKU" value="<?php echo set_value('edSKU') ?>"/>
                    </div>
                    <div class="control-group">
                        <label id="label"><strong>Qty</strong></label> : <input type="text" class="input-small" placeholder="Jumlah" id="jumlahSKU" value="<?php echo set_value('jumlahSKU') ?>"/>
                        <span id="satuan">

                        </span>
                    </div>
                    <div class="control-group">
                        <button class="btn btn-large" onclick="tambah_retur()" id="btnTambah">Tambah</button>
                        <button class="btn btn-large" onclick="ubah_retur()" id="btnUbah">Ubah</button>
                        <button class="btn btn-large" onclick="batal_retur()" id="btnBatal">Batal</button>
                    </div>
                    <table class="table table-bordered table-hover table-striped">
                        <thead>
                            <tr>
                                <td>Kode Bin</td>
                                <td>Nama Barang</td>
                                <td>ED</td>
                                <td>Jumlah</td>
                                <td>Aksi</td>
                            </tr>
                        </thead>
                        <tbody id="retur">

                        </tbody>
                    </table>
                    <input type="hidden" id="refreshed" value="no">
                    <a href="<?php echo base_url() ?>index.php/receive/save_retur" class="btn btn-large">Simpan</a>
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
            $(document).ready(function() {
                $("#btnUbah").hide();
                $("#btnBatal").hide();
                $("#retur").load('<?php echo base_url() . "index.php/receive/list_retur" ?>');
            });
            var d = new Date();
            $(function(){
                $('.tanggal').mobiscroll().date({
                     theme: 'default',
                     display: 'bottom',
                     mode: 'clickpick',
                     dateFormat: 'yyyy/mm/dd',
                     startYear: d.getFullYear()
                });    
               
            });
            function tambah_retur(){
                var bin = $("#kodeBIN").val();
                var barang = $("#kodeSKU").val();
                var ed = $("#edSKU").val();
                var jumlah = $("#jumlahSKU").val();
                var rasio = $("#rasio").val();
                var namabarang=$("#namaSKU").val();
                var submit = {
                    "bin": bin,
                    "barang": barang,
                    "ed": ed,
                    "jumlah":jumlah,
                    "rasio":rasio,
                    "namabarang":namabarang
                }
                $.ajax({
                    url: '<?php echo base_url() . "index.php/receive/add_retur"; ?>',
                    type: "post",
                    datatype: 'json',
                    data:submit,
                    success: function(data) {
                        if(data.status){
                            var msg = "<div class='alert alert-success'><h5>Data berhasil ditambahkan.</h5></div>";
                            $("#message").html(msg);
                            $("#retur").load('<?php echo base_url() . "index.php/receive/list_retur" ?>');
                            $("#kodeBIN").val('');
                            $("#kodeSKU").val('');
                            $("#edSKU").val('');
                            $("#jumlahSKU").val('');
                            $("#namaSKU").val('');
                            $("#rasiohidden").val('');
                        } else {
                            $("#message").html(data.error);
                            $("#retur").load('<?php echo base_url() . "index.php/receive/list_retur" ?>');
                        }
                    }
                });
            }
            
            function edit_retur(rowid,id,name,ed,qty,namabarang,rasio){ //btn edit di list di klik
                $("#rowid").attr('disabled','disabled');
                $("#kodeBIN").attr('disabled','disabled');
                $("#kodeSKU").attr('disabled','disabled');
                $("#edSKU").attr('disabled','disabled');
                $("#message").html('');
                $("#btnTambah").hide();
                $("#btnUbah").show();
                $("#btnBatal").show();
                $("#rowid").val(rowid);
                $("#kodeBIN").val(id);
                $("#kodeSKU").val(name);
                $("#edSKU").val(ed);
                $("#jumlahSKU").val(qty);
                $("#namaSKU").val(namabarang);
                $("#rasiohidden").val(rasio);
            }
            
            function remove_retur(rowid){ //btn edit di list di klik
                var submit = {
                    "rowid": rowid
                }
                $.ajax({
                    url: '<?php echo base_url() . "index.php/receive/delete_retur"; ?>',
                    type: "post",
                    datatype: 'json',
                    data:submit,
                    success: function() {
                        var msg = "<div class='alert alert-success'><h5>Retur berhasil diubah.</h5></div>";
                        $("#message").html(msg);
                        $("#retur").load('<?php echo base_url() . "index.php/receive/list_retur" ?>');
                    }
                });
            }
            
            function ubah_retur(){ //btn edit diklik untuk ubah data ke database
                $("#rowid").removeAttr('disabled');
                $("#kodeBIN").removeAttr('disabled');
                $("#kodeSKU").removeAttr('disabled');
                $("#edSKU").removeAttr('disabled');
                $("#message").html('');
                var rowid = $("#rowid").val();
                var bin = $("#kodeBIN").val();
                var barang = $("#kodeSKU").val();
                var ed = $("#edSKU").val();
                var jumlah = $("#jumlahSKU").val();
                var rasio = $("#rasio").val();
                var namabarang=$("#namaSKU").val();
                var submit = {
                    "rowid": rowid,
                    "bin": bin,
                    "barang": barang,
                    "ed": ed,
                    "jumlah":jumlah,
                    "rasio":rasio,
                    "namabarang":namabarang
                }
                $.ajax({
                    url: '<?php echo base_url() . "index.php/receive/edit_retur"; ?>',
                    type: "post",
                    datatype: 'json',
                    data:submit,
                    success: function(data) {
                        if(data.status){
                            var msg = "<div class='alert alert-success'><h5>Data berhasil diubah.</h5></div>";
                            $("#message").html(msg);
                            $("#retur").load('<?php echo base_url() . "index.php/receive/list_retur" ?>');
                            $("#kodeBIN").val('');
                            $("#kodeSKU").val('');
                            $("#edSKU").val('');
                            $("#jumlahSKU").val('');
                            $("#namaSKU").val('');
                            $("#rasiohidden").val('');
                            $("#btnTambah").show();
                            $("#btnUbah").hide();
                            $("#btnBatal").hide();
                        } else {
                            $("#message").html(data.error);
                            $("#retur").load('<?php echo base_url() . "index.php/receive/list_retur" ?>');
                        }
                    }
                });
            }
            
            function batal_retur(){ //btn btn diklik
                $("#rowid").removeAttr('disabled');
                $("#kodeBIN").removeAttr('disabled');
                $("#kodeSKU").removeAttr('disabled');
                $("#edSKU").removeAttr('disabled');
                $("#kodeBIN").val('');
                $("#kodeSKU").val('');
                $("#edSKU").val('');
                $("#jumlahSKU").val('');
                $("#namaSKU").val('');
                $("#rasiohidden").val('');
                $("#message").html('');
                $("#btnTambah").show();
                $("#btnUbah").hide();
                $("#btnBatal").hide();
            }
            $("#barcodesku").change(function(){
                var barcodeSKU=$("#barcodesku").val();
                var postdata = {'barcodeSKU':barcodeSKU};
                if($.trim(barcodeSKU)!=''){
                    
                    var link = "<?php echo base_url() ?>index.php/receive/get_ajax_barang";
                    $.ajax({
                        type: 'POST',
                        url: link,
                        dataType: 'jsonp',
                        data: postdata,
                        jsonp: 'jsoncallback',
                        timeout: 5000,
                        success: function(data){
                            $("#kodeSKU").val(data.Kode);
                            $("#namaSKU").val(data.Keterangan);
                            
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
            });
            function detectChange(input, interval) {
                var previous = "";

                setInterval( function() {

                    var val = $("#kodeSKU").val();
                    if (  val != previous ) {
                        previous = val;
                        var SKUCode=$("#kodeSKU").val();
                        var rasiohidden=$("#rasiohidden").val();
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
                                var str='<select name="satuan" class="input-small" id="rasio">';
                                $.each(data, function(i,item){
                                    str = str +'<option value="'+item["Rasio"]+'"';
                                    if(rasiohidden!='')
                                    {
                                        if(item["Rasio"]==rasiohidden)
                                        {
                                            str+='selected="selected"';
                                        }
                                    }
                                    else{
                                 
                                        if(item["Satuan"]==item["DERP"])
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
            detectChange($("#kodeSKU").val(), 10);
            function detectChangeBarcodeSKU(input, interval) {
                var previous = "";

                setInterval( function() {

                    var val = $("#barcodesku").val();
                    if (  val != previous ) {
                        previous = val;
                        var barcodeSKU=$("#barcodesku").val();
                        var postdata = {'barcodeSKU':barcodeSKU};
                        if($.trim(barcodeSKU)!=''){
                    
                            var link = "<?php echo base_url() ?>index.php/receive/get_ajax_barang";
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
                                        window.location.replace("<?php echo base_url() ?>index.php/receive/cari_barang/"+barcodeSKU);
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
            
            function getEDlama(input, interval) {
                var previous = "";
                var previous2 = "";

                setInterval( function() {

                    var val = $("#kodeSKU").val();
                    var val2= $(".tanggal").val();
                    if (  val != previous || val2!=previous2 ) {
                      
                        previous = val;
                        previous2 = val2;
                        var SKUCode=$("#kodeSKU").val();
                        var EDinput=$(".tanggal").val();
                        var postdata = {'SKUCode':SKUCode,'EDinput':EDinput};
                        var link = "<?php echo base_url() ?>index.php/receive/get_ajax_EDlama";
                        $.ajax({
                            type: 'POST',
                            url: link,
                            dataType: 'jsonp',
                            data: postdata,
                            jsonp: 'jsoncallback',
                            timeout: 5000,
                            success: function(data){
                               $(".tanggal").val(data.EDlama);           
                            },
                            /*error: function (xhr, ajaxOptions, thrownError) {
                                alert(xhr.responseText);
                                alert(thrownError);
                            }*/
                            error: function(){
                                $(".tanggal").val('');  
                            }
                        });   
                        
                    }

                }, interval);
            }

            getEDlama($("#kodeSKU").val(), 10);
			
			function changefocus(input, interval) {
                var previous = "";

                setInterval( function() {

                    var val = $("#kodeBIN").val();
                    if (  val != previous ) {
                        previous = val;
                        if ($("#kodeBIN").val().length == $("#kodeBIN").attr('maxLength')) {
                            $('#jumlahSKU').focus();
                          }
                    }

                }, interval);
            }

            changefocus($("#kodeBIN").val(), 10);
        </script>
    </body>
</html>