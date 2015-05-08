<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <title>Replenish Manual</title>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <?php
        $this->load->view('include/header');
        ?>
    </head>

    <body>
        <div class="container">
            <div class="row">
                <div class="span12 center">
                    <h1>Replenish Manual</h1>
                    <hr />
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
                    <br />

                    <div id="message">
                    </div>
                    <input type="hidden" id="rowid"/>
                    <input type="hidden" id="BinCodetemp"/>
                    <input type="hidden" id="rasiohidden" value=""/>
                    <table style="margin: 0px auto;">
                        <tr>
                            <td><strong>Rack Asal</strong></td><td>:</td>
                            <td><input type="text" class="input-large" id="SrcRackName" placeholder="Rack Asal" name="SrcRackName" <?php
                    if ($this->session->flashdata('RackNameCari')) {
                        echo 'readonly="readonly"';
                    }
                    ?> value="<?php
                                       if ($this->session->flashdata('RackNameCari')) {
                                           echo $this->session->flashdata('RackNameCari');
                                       }
                    ?>" />
                                <input type="hidden" name="SrcRack" id="SrcRack" value="<?php
                                       if ($this->session->flashdata('RackSlotCode')) {
                                           echo $this->session->flashdata('RackSlotCode');
                                       }
                    ?>" />
                            </td>
                            <td><a href="<?php echo base_url() ?>index.php/admin_replenish/cari_rack" class="btn">Cari</a></td>
                        </tr>
                        <tr>
                            <td><strong>Bin Asal</strong></td><td>:</td>
                            <td><select name="SrcBin" id="SrcBin"></select></td><td></td>
                        </tr>
                        <tr>
                            <td><strong>SKU</strong></td><td>:</td>
                            <td><input type="text" class="input-large" readonly="readonly" placeholder="Kode Barang" name="SKUCode" id="SKUCode" /></td><td></td>
                        </tr>
                        <tr>
                            <td><strong>Barang</strong></td><td>:</td>
                            <td><input type="text" class="input-large" placeholder="Nama Barang" name="Keterangan" id="Keterangan" readonly="readonly"/>
                                <input type="hidden" name="WHCode" id="WHCode" /></td><td></td>
                        </tr>
                        <tr>
                            <td><strong>Qty Asal</strong></td><td>:</td>
                            <td><input type="text" readonly="readonly" class="input-medium" placeholder="Qty Asal" name="SrcQtykonversi" id="SrcQtykonversi" />
                                <input type="hidden" readonly="readonly" name="SrcQty" id="SrcQty"/></td><td></td>
                        </tr>
                        <tr>
                            <td><strong>Rack Tujuan</strong></td><td>:</td>
                            <td><input type="text" class="input-large" placeholder="Rack Tujuan" name="DestRackName" id="DestRackName" />
                                <input type="hidden" name="DestRack" id="DestRack" /></td><td></td>
                        </tr>
                        <tr>
                            <td><strong>Qty Ambil</strong></td><td>:</td>
                            <td><input type="text" class="input-small" placeholder="Jumlah" name="Qty" id="Qty" /></td><td><span id="satuan"></span></td>
                        </tr>
                    </table>
                    <div class="control-group">
                        <button class="btn btn-large" onclick="tambah_replenish()" id="btnTambah">Tambah</button>
                        <button class="btn btn-large" onclick="ubah_replenish()" id="btnUbah">Ubah</button>
                        <button class="btn btn-large" onclick="reset_replenish()" id="btnReset">Reset</button>
                        <button class="btn btn-large" onclick="batal_replenish()" id="btnBatal">Batal</button>
                    </div>
                    <table class="table table-bordered table-hover table-striped">
                        <thead>
                            <tr>
                                <td>Rack Asal</td>
                                <td>Bin Asal</td>
                                <td>Barang</td>
                                <td>Qty Asal</td>
                                <td>Qty Ambil</td>
                                <td>Rack Tujuan</td>
                                <td></td>
                            </tr>
                        </thead>
                        <tbody id="replenish">

                        </tbody>
                    </table>
                    <input type="hidden" id="refreshed" value="no">
                    <a href="<?php echo base_url() ?>index.php/admin_replenish/save_replenish" class="btn btn-large">Simpan</a>
                </div><!-- /.span4 -->
            </div><!-- /.row -->
        </div><!-- /.container -->
        <?php
        $this->load->view("include/footer");
        ?>
        <script type="text/javascript">
            var BinCode=<?php
        if ($this->session->flashdata('BinCode')) {
            echo "'" . $this->session->flashdata('BinCode') . "'";
        } else {
            echo "''";
        }
        ?>;
            var RackName=[
<?php
$i = 0;
foreach ($RackName as $row) {
    echo '"' . $row['Name'] . '"';
    if ($i < count($RackName) - 1) {
        echo ',';
    }
}
?>
    ];
    $(document).ready(function() {
        $("#btnUbah").hide();
        $("#btnBatal").hide();
        $("#replenish").load('<?php echo base_url() . "index.php/admin_replenish/list_replenish" ?>');
    });
    $( "#SrcRackName" ).autocomplete({
        source: RackName
    });
    $("#SrcRackName").keyup(function(){
        $("#SrcRackName").val($("#SrcRackName").val().toUpperCase());
    });
    $( "#DestRackName" ).autocomplete({
        source: RackName
    });
    $("#DestRackName").keyup(function(){
        $("#DestRackName").val($("#DestRackName").val().toUpperCase());
    });
    $("#SrcRackName").blur(function(){
        var postdata = {'RackName': $("#SrcRackName").val()};
        if( $("#SrcRackName").val()!=''){
            var link = "<?php echo base_url() ?>index.php/admin_replenish/get_ajax_cekrackname";
            $.ajax({
                type: 'POST',
                url: link,
                dataType: 'jsonp',
                data: postdata,
                jsonp: 'jsoncallback',
                timeout: 5000,
                success: function(data){
                    if(data.status==true)
                    {
                        $("#SrcRackName").attr('readonly',true);
                        $("#SrcRack").val(data.RackSlotCode);
                    }
                }
                /*error: function (xhr, ajaxOptions, thrownError) {
                    alert(xhr.responseText);
                    alert(thrownError);
                }
                 error: function(){
                                    //alert(data,null,'Error','Ok');
                                    alert('There is a problem',null,'Error','Ok');
                            }*/
            }); 
        }
    });
    $("#DestRackName").blur(function(){
        var postdata = {'RackName': $("#DestRackName").val()};
        if( $("#DestRackName").val()!=''){
            var link = "<?php echo base_url() ?>index.php/admin_replenish/get_ajax_cekrackname";
            $.ajax({
                type: 'POST',
                url: link,
                dataType: 'jsonp',
                data: postdata,
                jsonp: 'jsoncallback',
                timeout: 5000,
                success: function(data){
                    if(data.status==true)
                    {
                        $("#DestRack").val(data.RackSlotCode);
                    }
                }
                /*error: function (xhr, ajaxOptions, thrownError) {
                    alert(xhr.responseText);
                    alert(thrownError);
                }
                 error: function(){
                                    //alert(data,null,'Error','Ok');
                                    alert('There is a problem',null,'Error','Ok');
                            }*/
            }); 
        }
    });
    function detectChangeRack(input, interval) {
        var previous = "";

        setInterval( function() {

            var val = $("#SrcRack").val();
            if (  val != previous ) {
                previous = val;
                var RackSlotCode=$("#SrcRack").val();
                var postdata = {'RackSlotCode':RackSlotCode};
                var link = "<?php echo base_url() ?>index.php/admin_replenish/get_ajax_binrack";
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
                            if(BinCode==item["BinCode"])
                            {
                                str+='selected="selected"';
                            }
                            else if($('#BinCodetemp').val()==item["BinCode"])
                            {
                                str+='selected="selected"';
                            }
                            str+='>'+item["BinCode"]+'</option>';
                        });
                        $("#SrcBin").html(str);        
                    },
                    /*error: function (xhr, ajaxOptions, thrownError) {
                                alert(xhr.responseText);
                                alert(thrownError);
                            }*/
                    error: function(){
                        $("#SrcBin").html('');  
                    }
                });   
                        
            }

        }, interval);
    }

    detectChangeRack($("#SrcRack").val(), 10);
    function detectChangeSKU(input, interval) {
        var previous = "";

        setInterval( function() {

            var val = $("#SrcBin").val();
            if (  val != previous ) {
                previous = val;
                var RackSlotCode=$("#SrcBin").val();
                var postdata = {'BinCode':RackSlotCode};
                var link = "<?php echo base_url() ?>index.php/admin_replenish/get_ajax_binsku";
                $.ajax({
                    type: 'POST',
                    url: link,
                    dataType: 'jsonp',
                    data: postdata,
                    jsonp: 'jsoncallback',
                    timeout: 5000,
                    success: function(data){
                        $("#SKUCode").val(data.SKUCode);  
                        $("#Keterangan").val(data.Keterangan);  
                        $("#WHCode").val(data.WHCode);  
                        $("#SrcQtykonversi").val(data.Qtykonversi); 
                        $("#SrcQty").val(data.Qty);
                    },
                    /*error: function (xhr, ajaxOptions, thrownError) {
                                alert(xhr.responseText);
                                alert(thrownError);
                            }*/
                    error: function(){
                        $("#SKUCode").val('');  
                        $("#Keterangan").val('');  
                        $("#WHCode").val('');  
                        $("#SrcQtykonversi").val('');  
                        $("#SrcQty").val('');
                    }
                });   
                        
            }

        }, interval);
    }

    detectChangeSKU($("#SrcBin").val(), 10);
            
    function detectChangeSatuan(input, interval) {
        var previous = "";

        setInterval( function() {

            var val = $("#SKUCode").val();
            if (  val != previous ) {
                previous = val;
                var SKUCode=$("#SKUCode").val();
                var postdata = {'SKUCode':SKUCode};
                var link = "<?php echo base_url() ?>index.php/admin_replenish/get_ajax_satuan";
                $.ajax({
                    type: 'POST',
                    url: link,
                    dataType: 'jsonp',
                    data: postdata,
                    jsonp: 'jsoncallback',
                    timeout: 5000,
                    success: function(data){
                        var satuan=$('#rasiohidden').val();
                        var str='<select name="satuan" class="input-small" id="Rasio">';
                        $.each(data, function(i,item){
                            str = str +'<option value="'+item["Rasio"]+'"';
                          
                            if(item["Rasio"]==satuan)
                            {
                                str+='selected="selected"';
                            }
                            str+='>'+item["Satuan"]+'</option>';
                        });
                        str+='</select>';
                        $("#satuan").html(str);        
                    },
                    /*error: function (xhr, ajaxOptions, thrownError) {
                        alert(xhr.status);
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

    detectChangeSatuan($("#SKUCode").val(), 10);
            
    function tambah_replenish(){
            
        var SrcRackName = $("#SrcRackName").val();
        var SrcRack = $("#SrcRack").val();
        var BinCode = $("#SrcBin").val();
        var SKUCode = $("#SKUCode").val();
        var Keterangan = $("#Keterangan").val();
        var SrcQty = $("#SrcQty").val();
        var SrcQtykonversi = $("#SrcQtykonversi").val();
        var DestRack = $("#DestRack").val();
        var DestRackName = $("#DestRackName").val();
        var Qty = $("#Qty").val();
        var Rasio = $("#Rasio").val();
        var submit = {
            "SrcRackName": SrcRackName,
            "SrcRack": SrcRack,
            "BinCode": BinCode,
            "SKUCode":SKUCode,
            "Keterangan":Keterangan,
            "SrcQty":SrcQty,
            "SrcQtykonversi":SrcQtykonversi,
            "DestRack":DestRack,
            "DestRackName":DestRackName,
            "Qty":Qty,
            "Rasio":Rasio
                    
        };
               
        $.ajax({
            url: '<?php echo base_url() . "index.php/admin_replenish/add_replenish"; ?>',
            type: "post",
            datatype: 'json',
            data:submit,
            success: function(data) {
                if(data.status){
                    var msg = "<div class='alert alert-success'><h5>Data berhasil ditambahkan.</h5></div>";
                    $("#message").html(msg);
                    $("#replenish").load('<?php echo base_url() . "index.php/admin_replenish/list_replenish" ?>');
                    $("#SrcRackName").val('');
                    $("#SrcRackName").attr('readonly',false);
                    $('#rowid').val('');
                    $('#SrcRackName').val('');
                    $('#SrcRack').val('');
                    $('#BinCodetemp').val('');
                    $('#SKUCode').val('');
                    $('#Keterangan').val('');
                    $('#SrcQty').val('');
                    $('#SrcQtykonversi').val('');
                    $('#DestRack').val('');
                    $('#DestRackName').val('');
                    $('#Qty').val('');
                    $('#rasiohidden').val('');
                    $('#SrcRackName').attr('readonly',false);
                    $("#btnUbah").hide();
                    $("#btnBatal").hide();
                    $("#btnReset").show();
                    $("#btnTambah").show();
                    $("#satuan").html(''); 
                } else {
                    $("#message").html(data.error);
                    $("#replenish").load('<?php echo base_url() . "index.php/admin_replenish/list_replenish" ?>');
                }
            }
                    
        });
    }
    function edit_replenish(rowid,SrcRackName,SrcRack,BinCode,SKUCode,Keterangan,SrcQty,SrcQtykonversi,DestRack,DestRackName,Qty,Rasio){ //btn edit di list di klik 
       
        $('#rowid').val(rowid);
        $('#SrcRackName').val(SrcRackName);
        $('#SrcRack').val(SrcRack);
        $('#BinCodetemp').val(BinCode);
        $('#SKUCode').val(SKUCode);
        $('#Keterangan').val(Keterangan);
        $('#SrcQty').val(SrcQty);
        $('#SrcQtykonversi').val(SrcQtykonversi);
        $('#DestRack').val(DestRack);
        $('#DestRackName').val(DestRackName);
        $('#Qty').val(Qty);
        $('#rasiohidden').val(Rasio);
        $('#SrcRackName').attr('readonly',true);
        $("#btnUbah").show();
        $("#btnBatal").show();
        $("#btnReset").hide();
        $("#btnTambah").hide();
        $("#message").html('');
    }
    function remove_replenish(rowid){ //btn edit di list di klik
        var submit = {
            "rowid": rowid
        }
        $.ajax({
            url: '<?php echo base_url() . "index.php/admin_replenish/delete_replenish"; ?>',
            type: "post",
            datatype: 'json',
            data:submit,
            success: function() {
                var msg = "<div class='alert alert-success'><h5>Data berhasil dihapus.</h5></div>";
                $("#message").html(msg);
                $("#replenish").load('<?php echo base_url() . "index.php/admin_replenish/list_replenish" ?>');
            }
        });
    }
    function reset_replenish(){
        $('#rowid').val('');
        $('#SrcRackName').val('');
        $('#SrcRack').val('');
        $('#BinCodetemp').val('');
        $('#SKUCode').val('');
        $('#Keterangan').val('');
        $('#SrcQty').val('');
        $('#SrcQtykonversi').val('');
        $('#DestRack').val('');
        $('#DestRackName').val('');
        $('#Qty').val('');
        $('#rasiohidden').val('');
        $('#SrcRackName').attr('readonly',false);
        $("#message").html('');
        $("#satuan").html(''); 
    }
    function batal_replenish(){ //btn btn diklik
        $('#rowid').val('');
        $('#SrcRackName').val('');
        $('#SrcRack').val('');
        $('#BinCodetemp').val('');
        $('#SKUCode').val('');
        $('#Keterangan').val('');
        $('#SrcQty').val('');
        $('#SrcQtykonversi').val('');
        $('#DestRack').val('');
        $('#DestRackName').val('');
        $('#Qty').val('');
        $('#rasiohidden').val('');
        $('#SrcRackName').attr('readonly',false);
        $("#btnUbah").hide();
        $("#btnBatal").hide();
        $("#btnReset").show();
        $("#btnTambah").show();
        $("#message").html('');
        $("#satuan").html(''); 
    }
    function ubah_replenish(){ //btn edit diklik untuk ubah data ke database
        var SrcRackName = $("#SrcRackName").val();
        var SrcRack = $("#SrcRack").val();
        var BinCode = $("#SrcBin").val();
        var SKUCode = $("#SKUCode").val();
        var Keterangan = $("#Keterangan").val();
        var SrcQty = $("#SrcQty").val();
        var SrcQtykonversi = $("#SrcQtykonversi").val();
        var DestRack = $("#DestRack").val();
        var DestRackName = $("#DestRackName").val();
        var Qty = $("#Qty").val();
        var Rasio = $("#Rasio").val();
        var RowId=$('#rowid').val();
        var submit = {
            "RowId":RowId,
            "SrcRackName": SrcRackName,
            "SrcRack": SrcRack,
            "BinCode": BinCode,
            "SKUCode":SKUCode,
            "Keterangan":Keterangan,
            "SrcQty":SrcQty,
            "SrcQtykonversi":SrcQtykonversi,
            "DestRack":DestRack,
            "DestRackName":DestRackName,
            "Qty":Qty,
            "Rasio":Rasio
                    
        };
               
        $.ajax({
            url: '<?php echo base_url() . "index.php/admin_replenish/edit_replenish"; ?>',
            type: "post",
            datatype: 'json',
            data:submit,
            success: function(data) {
                if(data.status){
                    var msg = "<div class='alert alert-success'><h5>Data berhasil diubah.</h5></div>";
                    $("#message").html(msg);
                    $("#replenish").load('<?php echo base_url() . "index.php/admin_replenish/list_replenish" ?>');
                    $("#SrcRackName").val('');
                    $("#SrcRackName").attr('readonly',false);
                    $('#rowid').val('');
                    $('#SrcRackName').val('');
                    $('#SrcRack').val('');
                    $('#BinCodetemp').val('');
                    $('#SKUCode').val('');
                    $('#Keterangan').val('');
                    $('#SrcQty').val('');
                    $('#SrcQtykonversi').val('');
                    $('#DestRack').val('');
                    $('#DestRackName').val('');
                    $('#Qty').val('');
                    $('#rasiohidden').val('');
                    $('#SrcRackName').attr('readonly',false);
                    $("#btnUbah").hide();
                    $("#btnBatal").hide();
                    $("#btnReset").show();
                    $("#btnTambah").show();
                    $("#satuan").html(''); 
                } else {
                    $("#message").html(data.error);
                    $("#replenish").load('<?php echo base_url() . "index.php/admin_replenish/list_replenish" ?>');
                }
            }
                    
        });
    }
        </script>
    </body>
</html>