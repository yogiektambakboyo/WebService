<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <title>Input Replenish</title>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <?php
        $this->load->view('include/header');
        ?>
    </head>

    <body>
        <div class="container">
            <div class="row">
                <div class="span12 center">
                    <h1>Input Replenish <?php echo $TransactionCode; ?></h1>
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
                    <input type="hidden" id="bincodehidden" value=""/>
                    <input type="hidden" id="whcodehidden" value=""/>
                    <div class="control-group">
                        <label id="label"><strong>Rack</strong></label> : <input type="text" class="input-medium" placeholder="Kode Rack" id="rackcode" name="koderack" value="<?php echo set_value('koderack') ?>"/>
                    </div>
                    <div class="control-group">
                        <label id="label"><strong>Bin</strong></label> : 
                        <select id="bincode">

                        </select>
                    </div>
                    <div class="control-group input-append">
                        <label id="label"><strong>SKU</strong></label> : <input type="text" class="input-medium" readonly="readonly" placeholder="Kode Barang" id="kodeSKU" />
                    </div>
                    <div class="control-group">
                        <label id="label"><strong>Barang</strong></label> : <input type="text" class="input-large" placeholder="Nama Barang" name="namaSKU" id="namaSKU" readonly="readonly" />
                    </div>
                    <div class="control-group">
                        <label id="label"><strong>Exp Date</strong></label> : <input type="text" class="input-large" placeholder="Exp Date" name="expdate" id="expdate" readonly="readonly" />
                    </div>
                    <div class="control-group input-append">
                        <label id="label"><strong>Qty Awal</strong></label> : <input type="text" class="input-medium" readonly="readonly" placeholder="Jumlah Awal" id="QtyAwalKonversi" />
                        <input type="hidden" class="input-medium" readonly="readonly" placeholder="Jumlah Awal" id="QtyAwal"/>
                    </div>
                    <div class="control-group">
                        <label id="label"><strong>Qty</strong></label> : <input type="text" class="input-small" placeholder="Jumlah" id="jumlahSKU" value="<?php echo set_value('jumlahSKU') ?>"/>
                        <span id="satuan">

                        </span>
                    </div>
                    <div class="control-group">
                        <label id="label"><strong>Bin Temp</strong></label> : <input type="text" name="bintemp" class="input-medium" placeholder="Kode Bin Temp" id="bintemp" value="<?php echo set_value('bintemp') ?>"/>
                    </div>
                    <div class="control-group">
                        <button class="btn btn-large" onclick="tambah_replenish()" id="btnTambah">Tambah</button>
                        <button class="btn btn-large" onclick="ubah_replenish()" id="btnUbah">Ubah</button>
                        <button class="btn btn-large" onclick="batal_replenish()" id="btnBatal">Batal</button>
                    </div>
                    <table class="table table-bordered table-hover table-striped">
                        <thead>
                            <tr>
                                <td>Kode Rack</td>
                                <td>Kode Bin</td>
                                <td>Nama Barang</td>
                                <td>Jumlah</td>
                                <td>Bin Temp</td>
                                <td>Aksi</td>
                            </tr>
                        </thead>
                        <tbody id="replenish">

                        </tbody>
                    </table>
                    <input type="hidden" id="refreshed" value="no">
                    <div class="control-group">
                        <a href="<?php echo base_url() ?>index.php/replenish_manual/save_replenish/<?php echo str_replace('=', '-', str_replace('/', '_', base64_encode($TransactionCode))); ?>" class="btn btn-large">Simpan</a>
                    </div>
                    <div class="control-group">
                        <a href="<?php echo base_url() ?>index.php/replenish_manual/finish_replenish/<?php echo str_replace('=', '-', str_replace('/', '_', base64_encode($TransactionCode))); ?>" class="btn btn-large">Tutup Proses</a>
                    </div>
                    

                </div><!-- /.span4 -->
            </div><!-- /.row -->
        </div><!-- /.container -->
        <?php
        $this->load->view("include/footer");
        ?>
        <script type="text/javascript">
            $(document).ready(function() {
                $("#btnUbah").hide();
                $("#btnBatal").hide();
                $("#replenish").load('<?php echo base_url() . "index.php/replenish_manual/list_replenish" ?>');
            });
           
            function tambah_replenish(){
                var rackcode = $("#rackcode").val();
                var bincode = $("#bincode").val();
                var kodeSKU= $("#kodeSKU").val();
                var namaSKU= $("#namaSKU").val();
                var jumlahSKU= $("#jumlahSKU").val();
                var rasio = $("#rasio").val();
                var QtyAwal = $("#QtyAwal").val();
                var QtyAwalKonversi = $("#QtyAwalKonversi").val();
                var bintemp=$("#bintemp").val();
                var expdate=$("#expdate").val();
                var whcode=$("#whcodehidden").val();
                var submit = {
                    "rackcode": rackcode,
                    "bincode": bincode,
                    "kodeSKU": kodeSKU,
                    "namaSKU": namaSKU,
                    "jumlahSKU":jumlahSKU,
                    "rasio":rasio,
                    "bintemp":bintemp,
                    "QtyAwal":QtyAwal,
                    "QtyAwalKonversi":QtyAwalKonversi,
                    "expdate":expdate,
                    "whcode":whcode
                }
                $.ajax({
                    url: '<?php echo base_url() . "index.php/replenish_manual/add_replenish"; ?>',
                    type: "post",
                    datatype: 'json',
                    data:submit,
                    success: function(data) {
                        if(data.status){
                            var msg = "<div class='alert alert-success'><h5>Data berhasil ditambahkan.</h5></div>";
                            $("#message").html(msg);
                            $("#replenish").load('<?php echo base_url() . "index.php/replenish_manual/list_replenish" ?>');
                            $("#rackcode").val('');
                            $("#bincode").html('');
                            $("#kodeSKU").val('');
                            $("#namaSKU").val('');
                            $("#bintemp").val('');
                            $("#jumlahSKU").val('');
                            $("#QtyAwal").val('');
                            $("#QtyAwalKonversi").val('');
                            $("#expdate").val('');
                            $("#whcodehidden").val('');
                        } else {
                            $("#message").html(data.error);
                            $("#replenish").load('<?php echo base_url() . "index.php/replenish_manual/list_replenish" ?>');
                        }
                    }
                });
            }
            
            function edit_replenish(rowid,rackcode,bincode,QtyAwal,QtyAwalKonversi,jumlahSKU,namaSKU,kodeSKU,bintemp,rasio){ //btn edit di list di klik
                $("#rowid").attr('disabled','disabled');
                $("#rackcode").attr('disabled','disabled');
                $("#message").html('');
                $("#btnTambah").hide();
                $("#btnUbah").show();
                $("#btnBatal").show();
                $("#rowid").val(rowid);
                $("#rackcode").val(rackcode);
                $("#QtyAwal").val(QtyAwal);
                $("#QtyAwalKonversi").val(QtyAwalKonversi);
                $("#jumlahSKU").val(jumlahSKU);
                $("#namaSKU").val(namaSKU);
                $("#kodeSKU").val(kodeSKU);
                $("#bintemp").val(bintemp);
                $("#bincodehidden").val(bincode);
                $("#rasiohidden").val(rasio);
            }
            
            function remove_replenish(rowid){ //btn edit di list di klik
                var submit = {
                    "rowid": rowid
                }
                $.ajax({
                    url: '<?php echo base_url() . "index.php/replenish_manual/delete_replenish"; ?>',
                    type: "post",
                    datatype: 'json',
                    data:submit,
                    success: function() {
                        var msg = "<div class='alert alert-success'><h5>Retur berhasil diubah.</h5></div>";
                        $("#message").html(msg);
                        $("#replenish").load('<?php echo base_url() . "index.php/replenish_manual/list_replenish" ?>');
                    }
                });
            }
            
            function ubah_replenish(){ //btn edit diklik untuk ubah data ke database
                $("#rowid").removeAttr('disabled');
                $("#rackcode").removeAttr('disabled');
                $("#message").html('');
                var rackcode = $("#rackcode").val();
                var bincode = $("#bincode").val();
                var kodeSKU= $("#kodeSKU").val();
                var namaSKU= $("#namaSKU").val();
                var jumlahSKU= $("#jumlahSKU").val();
                var rasio = $("#rasio").val();
                var QtyAwal = $("#QtyAwal").val();
                var QtyAwalKonversi = $("#QtyAwalKonversi").val();
                var bintemp=$("#bintemp").val();
                var expdate=$("#expdate").val();
                var whcode=$("#whcodehidden").val();
                var rowid=$("#rowid").val();
                var submit = {
                    "rowid": rowid,
                    "rackcode": rackcode,
                    "bincode": bincode,
                    "kodeSKU": kodeSKU,
                    "namaSKU": namaSKU,
                    "jumlahSKU":jumlahSKU,
                    "rasio":rasio,
                    "bintemp":bintemp,
                    "QtyAwal":QtyAwal,
                    "QtyAwalKonversi":QtyAwalKonversi,
                    "expdate":expdate,
                    "whcode":whcode
                }
                $.ajax({
                    url: '<?php echo base_url() . "index.php/replenish_manual/edit_replenish"; ?>',
                    type: "post",
                    datatype: 'json',
                    data:submit,
                    success: function(data) {
                        if(data.status){
                            var msg = "<div class='alert alert-success'><h5>Data berhasil diubah.</h5></div>";
                            $("#message").html(msg);
                            $("#replenish").load('<?php echo base_url() . "index.php/replenish_manual/list_replenish" ?>');
                            $("#rackcode").val('');
                            $("#bincode").html('');
                            $("#kodeSKU").val('');
                            $("#namaSKU").val('');
                            $("#bintemp").val('');
                            $("#jumlahSKU").val('');
                            $("#QtyAwal").val('');
                            $("#QtyAwalKonversi").val('');
                            $("#bincodehidden").val('');
                            $("#rasiohidden").val('');
                            $("#expdate").val('');
                            $("#whcodehidden").val('');
                            
                        } else {
                            $("#message").html(data.error);
                            $("#replenish").load('<?php echo base_url() . "index.php/replenish_manual/list_replenish" ?>');
                        }
                    }
                });
            }
            
            function batal_replenish(){ //btn btn diklik
                $("#rowid").removeAttr('disabled');
                $("#rackcode").removeAttr('disabled');
                $("#kodeSKU").removeAttr('disabled');
                $("#edSKU").removeAttr('disabled');
                $("#rackcode").val('');
                $("#bincode").html('');
                $("#kodeSKU").val('');
                $("#namaSKU").val('');
                $("#bintemp").val('');
                $("#jumlahSKU").val('');
                $("#QtyAwal").val('');
                $("#expdate").val('');
                $("#QtyAwalKonversi").val('');
                $("#bincodehidden").val('');
                $("#rasiohidden").val('');
                $("#message").html('');
                $("#btnTambah").show();
                $("#btnUbah").hide();
                $("#btnBatal").hide();
            }
           
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

                    var val = $("#rackcode").val();
                    if (  val != previous ) {
                        previous = val;
                        var rackcode=$("#rackcode").val();
                        var postdata = {'rackcode':rackcode};
                        var bincodehidden=$("#bincodehidden").val();
                        if($.trim(rackcode)!=''){
                    
                            var link = "<?php echo base_url() ?>index.php/replenish_manual/get_ajax_bin";
                            $.ajax({
                                type: 'POST',
                                url: link,
                                dataType: 'jsonp',
                                data: postdata,
                                jsonp: 'jsoncallback',
                                timeout: 5000,
                                success: function(data){
                                    var str='';
                                    $.each(data, function(i,item){
                                        str = str +'<option value="'+item["BinCode"]+'"';
                                        if(bincodehidden!='')
                                        {
                                            if(item["BinCode"]==bincodehidden)
                                            {
                                                str+='selected="selected"';
                                            }
                                        }
                                        str+='>'+item["BinCode"]+'</option>';
                                    });
                                    $("#bincode").html(str);
                                },
                                /*error: function (xhr, ajaxOptions, thrownError) {
                                alert(xhr.responseText);
                                alert(thrownError);
                            }*/
                                error: function(){
                                    $("#bincode").html('');
                                }
                            }); 
                        }
                        else
                        {
                            $("#bincode").html('');
                        }
                        
                    }

                }, interval);
            }

            detectChangeRack($("#rackcode").val(), 10);
            
            function detectChangeBin(input, interval) {
                var previous = "";

                setInterval( function() {

                    var val = $("#bincode").val();
                    if (  val != previous ) {
                        previous = val;
                        var bincode=$("#bincode").val();
                        var postdata = {'bincode':bincode};
                        if($.trim(bincode)!=''){
                    
                            var link = "<?php echo base_url() ?>index.php/replenish_manual/get_ajax_sku";
                            $.ajax({
                                type: 'POST',
                                url: link,
                                dataType: 'jsonp',
                                data: postdata,
                                jsonp: 'jsoncallback',
                                timeout: 5000,
                                success: function(data){
                                    $("#kodeSKU").val(data.SKUCode);
                                    $("#namaSKU").val(data.Keterangan);
                                    $("#QtyAwal").val(data.Qty);
                                    $("#QtyAwalKonversi").val(data.QtyKonversi);
                                    $("#expdate").val(data.ExpDate);
                                    $("#whcodehidden").val(data.WHCode);
                                },
                                /*error: function (xhr, ajaxOptions, thrownError) {
                                alert(xhr.responseText);
                                alert(thrownError);
                            }*/
                                error: function(){
                                    $("#kodeSKU").val('');
                                    $("#namaSKU").val('');
                                    $("#QtyAwal").val('');
                                    $("#QtyAwalKonversi").val('');
                                    $("#jumlahSKU").val('');
                                    $("#expdate").val('');
                                    $("#whcodehidden").val('');
                                }
                            }); 
                        }
                        else
                        {
                            $("#kodeSKU").val('');
                            $("#namaSKU").val('');
                            $("#QtyAwal").val('');
                            $("#QtyAwalKonversi").val('');
                            $("#jumlahSKU").val('');
                            $("#expdate").val('');
                            $("#whcodehidden").val('');
                        }
                        
                    }

                }, interval);
            }

            detectChangeBin($("#bincode").val(), 10);
            
            
        </script>
    </body>
</html>