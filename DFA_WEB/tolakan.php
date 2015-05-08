<?php
session_start();
if ((!isset($_SESSION["usernamedfa"]))||(!isset($_SESSION["jabatandfa"]))||(!isset($_SESSION["cabangdfa"]))) {
    header("location:login.php");
}

if(($_SESSION["jabatandfa"]!="SPV LOG")&&($_SESSION["jabatandfa"]!="KOOR LOG")&&($_SESSION["jabatandfa"]!="LOG")&&($_SESSION["jabatandfa"]!="ADMINISTRATOR")){
    header("location:index.php");
}
include "setting/include.php";
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>DFA - Tolakan Demo Page</title>
    <link rel="Icon" href="favicon.ico">

    <!--Load Bootstrap -->
    <link href="js/bootstrap-3.2.0/css/bootstrap.min.css" rel="stylesheet">
    <link href="js/bootstrap-3.2.0/css/bootstrap-theme.min.css" rel="stylesheet">
    <link href="js/bootstrap-3.2.0/css/theme.css" rel="stylesheet">

    <link href="js/jquery-ui-1.11.2/jquery-ui.min.css" rel="stylesheet" type="text/css" />

    <link href="js/jtable.2.4.0/themes/metro/blue/jtable.min.css" rel="stylesheet" type="text/css" />
    <link href="js/pnotify.3.0/pnotify.custom.min.css" rel="stylesheet" type="text/css" />
    <link href="js/formValidator.2.6.1/css/validationEngine.jquery.css" rel="stylesheet" type="text/css" />

    <link href="js/select2-3.5.2/select2.css" rel="stylesheet" type="text/css">
    <link href="js/select2-3.5.2/select2-bootstrap.css" rel="stylesheet" type="text/css">

    <link type="text/css" href="js/jMetro/css/jquery-ui.css" rel="stylesheet" />
    <script src="js/jquery-1.8.3.min.js" type="text/javascript"></script>
    <script src="js/modernizr-2.6.2.js" type="text/javascript"></script>

    <script src="js/jquery-ui-1.11.2/jquery-ui.min.js" type="text/javascript"></script>

    <script src="js/jtable.2.4.0/jquery.jtable.js" type="text/javascript"></script>
    <script src="js/bootstrap-3.2.0/js/ie-emulation-modes-warning.js"></script>
    <script src="js/bootstrap-3.2.0/js/ie10-viewport-bug-workaround.js"></script>
    <script src="js/bootstrap-3.2.0/js/bootstrap.min.js"></script>
    <script src="js/pnotify.3.0/pnotify.custom.min.js"></script>
    <script src="js/accounting.min.js"></script>


    <script type="text/javascript" src="js/formValidator.2.6.1/js/jquery.validationEngine.js"></script>
    <script type="text/javascript" src="js/formValidator.2.6.1/js/languages/jquery.validationEngine-en.js"></script>

    <script type="text/javascript" src="js/select2-3.5.2/select2.min.js"></script>
</head>

<body oncontextmenu="return false">

<div class="navbar navbar-default navbar-fixed-top" role="navigation">
    <div class="container">
        <div class="navbar-header">
            <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
                <span class="sr-only">Toggle navigation</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>
            <a class="navbar-brand" href="#">Delivery Force Automation (DFA)</a>
        </div>
        <div class="navbar-collapse collapse">
            <ul class="nav navbar-nav">
                <?php
                if($_SESSION["jabatandfa"]=="ADMINISTRATOR"){
                    echo "<li><a href='index.php'><span class='glyphicon glyphicon-home'></span> Home</a></li>";
                }
                ?>
                <?php
                if($_SESSION["jabatandfa"]=="ADMINISTRATOR"){
                    echo "<li><a href='clearence.php'><span class='glyphicon glyphicon-trash'></span> Pembersihan TT/Stempel/Tak Terkirim</a></li>";
                }
                ?>
                <li class="active"><a href="tolakan.php"><span class="glyphicon glyphicon-eject"></span> Tolakan</a></li>
                <li><a href="logout.php"><span class="glyphicon glyphicon-log-out"></span> Log Out</a></li>
            </ul>
            <ul class="nav navbar-nav navbar-right">
                <li><a href="#"><span class="glyphicon glyphicon-user"></span> User : <?php echo $_SESSION["usernamedfa"];?></a></li>
            </ul>
        </div>
    </div>
</div>

<div class="container theme-showcase" role="main">

    <div class="divider-vertical-up"></div>
    <h3><span class="glyphicon glyphicon-eject"></span> Data Tolakan ( <label id="LblCbg"></label> )</h3>
    <div class="divider-vertical-down"></div>


    <div class="row" id="homeP">

        <div class="col-lg-12">
            <label for="slcfilter" class="col-sm-1">Pencarian</label>
            <div class="col-sm-4">
                <select id="slcfilter" name="slcfilter" class="form-control">
                    <option value="0">Semua</option>
                    <option value="1">Operator</option>
                    <option value="2">Tanggal</option>
                </select>
            </div>
            <div class="col-sm-1">
                <button type="submit" class="btn btn-primary" id="btnCari">Cari</button>
            </div>
            <div class="col-sm-1">
                <button type="submit" class="btn btn-success" id="btnProses">Proses</button>
            </div>
        </div>
        <br>
        <br>
        <div class="col-lg-12">
            <label for="slcfilter" class="col-sm-1" id="lblfilter" name="lblfilter">Pilihan</label>
            <div class="col-sm-4">
                <select id="slcfiltersopir" name="slcfiltersopir" class="form-control">
                </select>
                <input type="text" id="txttglawal" name="txttglawal" class="form-control">
            </div>
            <label for="txttglawal" class="col-sm-1" id="lblfiltertglakhir" name="lblfiltertglakhir">Tgl Akhir</label>
            <div class="col-sm-4">
                <input type="text" id="txttglakhir" name="txttglakhir"  class="form-control">
            </div>
        </div>


        <br>
        <br>
        <div id="TableDaftarKKPDVNoTunai"></div>
    </div>

    <div class="row" id="tolakanP">
        <div class="col-lg-12">
            <label id="LblFaktur" class="col-md-10">Faktur : -</label>
            <label id="LblNamaPerusahaan" class="col-md-10">Perusahaan : -</label>
            <div class="form-group">
                <button type="submit" class="btn btn-danger" id="btnCancelProses">Batal</button>
                <label></label>
                <label></label>
                <label></label>
                <button type="submit" class="btn btn-success" id="btnSubmitFinal">Proses</button>
            </div>
            <div class="divider-vertical-down"></div>
        </div>
    </div>

    <br>
    <div id="TableDaftarTolakan"></div>
    <div id="dialog-confirm"></div>
    <input type="text" hidden="true" id="Cbg" value="<?php echo($_SESSION["cabangdfa"]);?>">

</div>

<script type="text/javascript">
$(document).ready(function (){
    $('#homeP').show();
    $('#tolakanP').hide();
    $('#slcfiltersopir').hide();
    $('#btnProses').hide();
    $('#txttglawal').hide();
    $('#txttglakhir').hide();
    $('#lblfilter').hide();
    $('#lblfiltertglakhir').hide();
    $('#txttglawal').datepicker({ dateFormat: 'yy-mm-dd' });
    $('#txttglakhir').datepicker({ dateFormat: 'yy-mm-dd' });

    var fin="";
    var perusahaan = "";
    var filter = '';

    //Prepare jTable
    $('#TableDaftarKKPDVNoTunai').jtable({
        actions: {
            listAction: 'dfaaction.php?act=list&f='
        }
    });

    //Load person list from server
    $('#TableDaftarKKPDVNoTunai').jtable('load');

    //Prepare jTable
    $('#TableDaftarTolakan').jtable({
        actions: {
            listAction: 'dfaaction.php?act=list&f='
        }
    });

    //Load person list from server
    $('#TableDaftarTolakan').jtable('load');

    $('#btnCari').click(function(){
        if($('#slcfilter').val()=="1"){
            filter = ' and b.Operator=\''+$('#slcfiltersopir').val()+'\' ';

        }else if($('#slcfilter').val()=="2"){
            filter = ' and m.Tgl between \''+$('#txttglawal').val()+'\' and \''+$('#txttglakhir').val()+'\' ';
        }else{
            filter = ' ';
        }


        $('#TableDaftarKKPDVNoTunai').jtable('destroy');

        //Prepare jTable
        $('#TableDaftarKKPDVNoTunai').jtable({
            title : 'Rangkuman Pengiriman',
            selecting: true, //Enable selecting
            multiselect: false, //Allow multiple selecting
            selectingCheckboxes: true,
            actions: {
                listAction: 'dfaaction.php?act=listtolakan&f='+filter
            },
            fields:{
                Tgl :{
                    title:"Tgl"
                },
                Faktur :{
                    title:"Faktur"
                },
                Perusahaan :{
                    title:"Perusahaan"
                },
                SKU : {
                    title:"Jml SKU"
                },
                StatusT : {
                    title:"Status"
                }
            },
            //Register to selectionChanged event to hanlde events
            selectionChanged: function () {
                //Get all selected rows
                var $selectedRows = $('#TableDaftarKKPDVNoTunai').jtable('selectedRows');
                $('#SelectedRowList').empty();
                if ($selectedRows.length > 0) {
                    //Show selected rows
                    var i = 0;
                    $selectedRows.each(function () {
                        var record = $(this).data('record');
                        fin = "'"+record.Faktur+"'";
                        perusahaan = "'"+record.Perusahaan+"'";
                        i++;
                    });
                } else {
                    //No rows selected
                    fin = "";
                    perusahaan = "";
                }

                $('#LblFaktur').text("Faktur : "+fin.replace(/'/g, ''));
                $('#LblNamaPerusahaan').text("Perusahaan : "+perusahaan.replace(/'/g, ''));

            }
        });

        //Load person list from server
        $('#TableDaftarKKPDVNoTunai').jtable('load');

        $('#btnProses').show();
    });

    $('#btnProses').click(function(){
        if(fin.indexOf("\'undefined\'")>=0){
            new PNotify({
                title: 'Kesalahan',
                text: 'Tidak ada dokumen yang di proses',
                type : 'error'
            });
        }else if(fin==""){
            new PNotify({
                title: 'Kesalahan',
                text: 'Pilih dulu dokumen yang akan di proses',
                type : 'error'
            });
        }else{
            $('#homeP').hide();
            $('#tolakanP').show();

            $('#TableDaftarTolakan').jtable('destroy');

            //Prepare jTable
            $('#TableDaftarTolakan').jtable({
                title : 'Detail Tolakan',
                //selecting: true, //Enable selecting
                //multiselect: true, //Allow multiple selecting
                //selectingCheckboxes: true,
                actions: {
                    listAction: 'dfaaction.php?act=detailbrgtolakan&f='+fin,
                    createAction: function(postData) {
                        return $.Deferred(function ($dfd) {
                            $.ajax({
                                url: 'dfaaction.php?act=tambahbrgtolakan&f='+fin,
                                type: 'POST',
                                dataType: 'json',
                                data: postData,
                                success: function (data) {
                                    $dfd.resolve(data);
                                    $('#TableDaftarTolakan').jtable('reload');
                                },
                                error: function () {$dfd.reject();}
                            });
                        });
                    },
                    updateAction: function(postData) {
                        console.log(postData);
                        return $.Deferred(function ($dfd) {
                            $.ajax({
                                url: 'dfaaction.php?act=updatedetailtolakan',
                                type: 'POST',
                                dataType: 'json',
                                data: postData,
                                success: function (data) {
                                    $dfd.resolve(data);
                                    $('#TableDaftarTolakan').jtable('reload');
                                },
                                error: function () {$dfd.reject();}
                            });
                        });
                    },
                    deleteAction : function(postData) {
                        console.log(postData);
                        return $.Deferred(function ($dfd) {
                            $.ajax({
                                url: 'dfaaction.php?act=deletetolakan&f='+fin,
                                type: 'POST',
                                dataType: 'json',
                                data: postData,
                                success: function (data) {
                                    $dfd.resolve(data);
                                    $('#TableDaftarTolakan').jtable('reload');
                                },
                                error: function () {
                                    $dfd.reject();
                                }
                            });
                        });
                    }
                },
                fields:{
                    Faktur :{
                        title:"Faktur",
                        list : false,
                        key : true
                    },
                    Brg :{
                        title:"SKU",
                        key : true,
                        create : true,
                        list : false,
                        options : 'dfaaction.php?act=getsku&f='+fin,
                        inputClass: 'validate[required]'
                    },
                    Keterangan :{
                        title:"Nama Barang",
                        create : false,
                        edit : false
                    },
                    Jml :{
                        title:"Jumlah (Pcs)",
                        inputClass: 'validate[required]'
                    },
                    ReasonCode : {
                       title: "Reason Code",
                        list : false,
                        edit : true,
                        inputClass: 'validate[required]',
                        options: 'dfaaction.php?act=getreasoncode'
                    },
                    Alasan : {
                        title:"Alasan",
                        edit: false,
                        create : false
                    },
                    StatusT :{
                        title:"Status",
                        type: 'checkbox',
                        values : {'false':'Tidak Valid','true': 'Valid'},
                        create : false
                    }
                },
                //Initialize validation logic when a form is created
                formCreated: function (event, data) {
                    data.form.validationEngine();
                },
                //Validate form when it is being submitted   d
                formSubmitting: function (event, data) {
                    return data.form.validationEngine('validate');
                },
                //Dispose validation logic when form is closed
                formClosed: function (event, data) {
                    data.form.validationEngine('hide');
                    data.form.validationEngine('detach');
                }
            });

            //Load person list from server
            $('#TableDaftarTolakan').jtable('load');

        }
    });


    $('#btnCancelProses').click(function(){
        fnBukaBatalDialog();
    });


    //Submit Final
    $('#btnSubmitFinal').click(function(){
        if(fin == ""){
            new PNotify({
                title: 'Kesalahan',
                text: 'Tidak Ada Faktur Yang di Proses!!!',
                type : 'error'
            });
        }else{
            $.ajax({
                url: 'dfaaction.php?act=cekbrgtolakan&f='+fin,
                type: 'POST',
                data : {},
                dataType : 'json',
                success: function(data) {
                    var StatusOver = 1;
                    $.each(data,function(idx,obj){
                        StatusOver = obj.jml;
                    });
                    if(StatusOver==0){
                        $("#dialog-confirm").html("Apakah anda yakin akan memproses tolakan faktur : "+fin+" ?");

                        // Define the Dialog and its properties.
                        $("#dialog-confirm").dialog({
                            resizable: false,
                            modal: true,
                            title: "Konfirmasi Proses",
                            width: 400,
                            buttons: {
                                "Ya": function () {
                                    $(this).dialog('close');
                                    $.ajax({
                                        url: 'dfaaction.php?act=inputtolakantrpdo&f='+fin,
                                        type: 'POST',
                                        data : {},
                                        dataType : 'json',
                                        success: function(data) {
                                            var KodeTO = "";
                                            var Status = "";
                                            $.each(data,function(idx,obj){
                                                KodeTO = obj.KodeTO;
                                                Status = obj.Status;
                                            });
                                            if(Status=="1"){
                                                alert("Faktur berhasil diproses dengan Kode Tolakan : "+KodeTO);
                                                location.reload();
                                            }else{
                                                alert("Status : "+KodeTO);
                                            }
                                        },
                                        error: function () {
                                            new PNotify({
                                                title: 'Kesalahan',
                                                text: 'Koneksi Server Error',
                                                type : 'error'
                                            });
                                        }
                                    });
                                },
                                "Tidak": function () {
                                    $(this).dialog('close');
                                }
                            }
                        });

                    }else{
                        new PNotify({
                            title: 'Kesalahan',
                            text: 'Ada jumlah tolakan SKU yang melebihi order!!!',
                            type : 'error'
                        });
                        }
                },
                error: function () {
                    new PNotify({
                        title: 'Kesalahan',
                        text: 'Koneksi Server Error',
                        type : 'error'
                    });
                }
            });
        }
    });


    // Get Data For Select List
    function getCabangDivisi(){
        $.ajax({
            url: 'dfaaction.php?act=getnamacabang&f='+$('#Cbg').val(),
            type: 'POST',
            data : {} ,
            success: function (response) {
                $.each($.parseJSON(response), function(idx, obj) {
                    if(typeof obj.NamaCabang !== "undefined"){
                        $('#LblCbg').text(""+obj.NamaCabang);
                    }
                });
            },
            error: function () {}
        });
    }

    // Get Data For Sopir Select List
    function getListChecker(){
        $.ajax({
            url: 'dfaaction.php?act=getdetailcheckertolakan',
            type: 'POST',
            data : {} ,
            success: function (response) {
                $.each($.parseJSON(response), function(idx, obj) {
                    if(typeof obj.Sopir !== "undefined"){
                        $('#slcfiltersopir').append($('<option>', {
                            value: obj.Sopir,
                            text : obj.Sopir
                        }));
                    }
                });
            },
            error: function () {}
        });
    }

    getCabangDivisi();
    getListChecker();


    $('#slcfilter').on('change', function() {
        $('#btnProses').hide();
        if($('#slcfilter').val()=="1"){
            $('#slcfiltersopir').show();
            $('#txttglawal').hide();
            $('#txttglakhir').hide();
            $('#lblfilter').show();
            $('#lblfiltertglakhir').hide();
            $('#lblfilter').text("Checker");
            filter = ' and m.Sopir=\''+$('#slcfiltersopir').val()+'\' ';

        }else if($('#slcfilter').val()=="2"){
            $('#slcfiltersopir').hide();
            $('#txttglawal').show();
            $('#txttglakhir').show();
            $('#lblfilter').show();
            $('#lblfiltertglakhir').show();
            $('#lblfilter').text("Tgl Awal");
            filter = ' and m.Tgl between \''+$('#txttglawal').val()+'\' and \''+$('#txttglakhir').val()+'\' ';
        }else{
            $('#slcfiltersopir').hide();
            $('#txttglawal').hide();
            $('#txttglakhir').hide();
            $('#lblfilter').hide();
            $('#lblfiltertglakhir').hide();
            filter = ' ';
        }
    });

    // Dialog Batal
    function fnBukaBatalDialog() {
        $("#dialog-confirm").html("Apakah anda yakin membatalkan trasaksi ini?");

        // Define the Dialog and its properties.
        $("#dialog-confirm").dialog({
            resizable: false,
            modal: true,
            title: "Konfirmasi Batal",
            width: 400,
            buttons: {
                "Ya": function () {
                    $(this).dialog('close');
                    location.reload();
                },
                "Tidak": function () {
                    $(this).dialog('close');
                }
            }
        });
    }

});




</script>
</body>
</html>

