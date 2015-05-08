<?php
session_start();
if ((!isset($_SESSION["usernamedfa"]))||(!isset($_SESSION["jabatandfa"]))||(!isset($_SESSION["cabangdfa"]))) {
    header("location:login.php");
}

if($_SESSION["jabatandfa"]=="INKASO"){
    header("location:clearence.php");
}
if(($_SESSION["jabatandfa"]=="SPV LOG")||($_SESSION["jabatandfa"]=="KOOR LOG")||($_SESSION["jabatandfa"]=="LOG")){
    header("location:tolakan.php");
}
include "setting/include.php";
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>DFA - Delivery Force Automation</title>
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
                <li class="active"><a href="index.php"><span class="glyphicon glyphicon-home"></span> Home</a></li>
                <?php
                    if($_SESSION["jabatandfa"]=="ADMINISTRATOR"){
                        echo "<li><a href='clearence.php'><span class='glyphicon glyphicon-trash'></span> Pembersihan TT/Stempel/Tak Terkirim</a></li>";
                    }
                ?>
                <?php
                if($_SESSION["jabatandfa"]=="ADMINISTRATOR"){
                    echo "<li><a href='tolakan.php'><span class='glyphicon  glyphicon-eject'></span> Tolakan</a></li>";
                }
                ?>
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
    <h3><span class="glyphicon glyphicon-list"></span> Delivery Data ( <label id="LblCbg"></label> )</h3>
    <div class="divider-vertical-down"></div>


    <div class="row" id="homeP">

        <div class="col-lg-12">
            <label for="slcsopir" class="col-sm-1">Sopir</label>
            <div class="col-sm-4">
                <select id="slcsopir" name="slcsopir" class="form-control">
                    <option value="-1">-- Pilih Sopir --</option>
                </select>
            </div>
            <div class="col-sm-1">
                <button type="submit" class="btn btn-primary" id="btnCari">Cari</button>
            </div>
            <div class="col-sm-1">
                <button class="btn btn-default" id="btnrefreshslc"><span class="glyphicon glyphicon-refresh"></span></button>
            </div>
            <div class="col-md-5">
                <label name="txtnamasopir" id="txtnamasopir"></label>
            </div>
        </div>
        <br>
        <br>
        <div id="TableDaftarKKPDVPerSopir"></div>
        <div class="form-group">
            <div class="divider-vertical-up"></div>
            <div class="col-md-4">
                <label id="lbltunai">Tunai : </label>
                <label id="tunai"></label>
            </div>
            <div class="col-md-4">
                <label id="lbltransfer">Transfer : </label>
                <label id="transfer"></label>
            </div>
            <div class="col-md-4">
                <label id="lblcekbg">Cek BG : </label>
                <label id="cekbg"></label>
            </div>
            <div class="divider-vertical-down"></div>
        </div>
        <div class="form-group center-block">
            <div class="divider-vertical-up"></div>
            <div class="divider-vertical-down"></div>
            <div class="col-lg-12 center-block">
                <button type="submit" class="btn btn-success center-block" id="btnSubmit">Proses</button>
            </div>
        </div>
    </div>

    <div class="row" id="kkpdvP">
        <div class="col-lg-12">
            <label id="LblNamaSopir" class="col-md-10">Sopir : -</label>
            <div class="form-group">
                <button type="submit" class="btn btn-danger" id="btnCancelProses">Batal</button>
                <label></label>
                <label></label>
                <label></label>
                <button type="submit" class="btn btn-success" id="btnSubmitFinal">Proses</button>
            </div>
            <div class="divider-vertical-down"></div>
        </div>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th  class="col-sm-1">#</th>
                    <th>Pembayaran</th>
                    <th>Nominal</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td><button id="btnDetailTunai" class="btn btn-primary"><span class="glyphicon glyphicon-list"></span></button></td>
                    <td>Tunai</td>
                    <td><label id="tunaiRp"></label></td>
                </tr>
                <tr>
                    <td colspan="3">
                        <div id="DetailTunai"></div>
                    </td>
                </tr>
                <tr>
                    <td><button id="btnDetailTransfer" class="btn btn-primary"><span class="glyphicon glyphicon-list"></span></button></td>
                    <td>Transfer</td>
                    <td><label id="transferRp"></label></td>
                </tr>
                <tr>
                    <td colspan="3">
                        <div id="DetailTransfer"></div>
                    </td>
                </tr>
                <tr>
                    <td><button id="btnDetailCekBG"  class="btn btn-primary"><span class="glyphicon glyphicon-list"></span></button></td>
                    <td>Cek BG</td>
                    <td><label id="cekbgRp"></label></td>
                </tr>
                <tr>
                    <td colspan="3">
                        <div id="DetailCekBG"></div>
                    </td>
                </tr>
            </tbody>
        </table>
        <div id="dialog-confirm"></div>
    </div>


    <br>
    <input type="text" hidden="true" id="Cbg" value="<?php echo($_SESSION["cabangdfa"]);?>">

</div>

<script type="text/javascript">

    $(document).ready(function () {
        $('#kkpdvP').hide();
        // Declare variable
        var selectedData = [];var fin = "";
        var showTunDet = false;var showBGDet = true;
        var namaSopir = "";
        var options = {
            symbol : "Rp. ",
            decimal : ",",
            thousand: ".",
            precision : 2,
            format: "%s%v"
        };
        var TotalTunai = 0;var TotalCekBG = 0;var TotalTransfer=0;
        var TotalCekBGXX = 0;var TotalTransferXX=0;
        // Validasi Kolom Bank dan Tgl Jatuh Tempo Terisi
        var Pass = 0;
        var NoCekBGNotValid = '';var selectedKodeSopir ='';
        var selectedNamaSopir ='';
        //Inisialisasi
        // Hide Tombol Submit
        $('#btnSubmit').hide();
        $('#lbltunai').hide();
        $('#lbltransfer').hide();
        $('#lblcekbg').hide();

        

        // Get Data For Sopir Select List
        function getListSopir(){
            $.ajax({
                url: 'dfaaction.php?act=getdetailsopir',
                type: 'POST',
                data : {} ,
                success: function (response) {
                    $.each($.parseJSON(response), function(idx, obj) {
                        if(typeof obj.Sopir !== "undefined"){
                            $('#slcsopir').append($('<option>', {
                                value: obj.Sopir,
                                text : obj.Sopir+" - "+obj.Nama
                            }));
                        }
                    });
                },
                error: function () {}
            });
        }

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
        // Get Proses KKPDV
        function getProsesKKPDV(ArrKKPDV){
            $.ajax({
                url: 'dfaaction.php?act=proseskkpdv&f='+ArrKKPDV,
                type: 'POST',
                data : {} ,
                dataType: 'json',
                success: function (response) {
                    TotalTunai = 0;
                    TotalCekBG = 0;
                    TotalCekBGXX = 0;
                    TotalTransfer = 0;
                    TotalTransferXX = 0;
                    $.each(response, function(idx, obj) {
                        TotalTunai = TotalTunai + obj.Tunai;
                        TotalCekBG = TotalCekBG + obj.BG;
                        TotalCekBGXX = TotalCekBGXX + obj.BG;
                        TotalTransfer = TotalTransfer + obj.Transfer;
                        TotalTransferXX = TotalTransferXX + obj.Transfer;
                    });

                    $('#tunaiRp').text(accounting.formatMoney(TotalTunai,options));
                    //$('#tunaiRpRound').text(accounting.formatMoney(Math.floor(TotalTunai/100)*100,options));
                    $('#transferRp').text(accounting.formatMoney(TotalTransfer,options));
                    $('#cekbgRp').text(accounting.formatMoney(TotalCekBG,options));

                    if(TotalTransfer>1){
                        $('#DetailTransfer').jtable({
                            title : 'Detail Transfer',
                            selecting: true, //Enable selecting
                            multiselect: true, //Allow multiple selecting
                            /*                    selectingCheckboxes: true,*/
                            actions: {
                                listAction: 'dfaaction.php?act=getdetailtransfer&f='+fin,
                                updateAction: function(postData) {
                                    return $.Deferred(function ($dfd) {
                                        $.ajax({
                                            url: 'dfaaction.php?act=updatetransfer',
                                            type: 'POST',
                                            dataType: 'json',
                                            data: postData,
                                            success: function (data) {
                                                $dfd.resolve(data);
                                                getTotalTransferValid(fin);
                                            },
                                            error: function () {$dfd.reject();}
                                        });
                                    });
                                }
                            },
                            fields:{
                                Kode : {
                                    title : "Kode",key: true,list:false
                                },
                                KodeNota : {
                                    title : "Kode Nota",key:true,edit:false
                                },
                                KodeBank :{
                                    title : "Kode Bank",
                                    inputClass: 'validate[required]',
                                    options: 'dfaaction.php?act=getnamabanktransfer'
                                },
                                TglTransfer : {
                                    title: "Tgl Transfer" ,type: 'date',displayFormat: 'yy-mm-dd',inputClass: 'validate[required,custom[date]]'
                                },
                                Jml : {
                                    title : "Nominal",
                                    inputClass: 'validate[required]'
                                },
                                TransferStatus:{
                                    title:"Transfer Status",
                                    type: 'checkbox',
                                    values: { 'false': 'Tidak Valid', 'true': 'Valid' }
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

                        $('#DetailTransfer').jtable('load');
                    }

                    if(TotalCekBG>1){
                        $('#DetailCekBG').jtable({
                            title : 'Detail BG',
                            selecting: true, //Enable selecting
                            multiselect: true, //Allow multiple selecting
                            /*                    selectingCheckboxes: true,*/
                            actions: {
                                listAction: 'dfaaction.php?act=getdetailcekbg&f='+fin,
                                updateAction: function(postData) {
                                    return $.Deferred(function ($dfd) {
                                        $.ajax({
                                            url: 'dfaaction.php?act=updatecekbg',
                                            type: 'POST',
                                            dataType: 'json',
                                            data: postData,
                                            success: function (data) {
                                                $dfd.resolve(data);
                                                getTotalCekGValid(fin);
                                            },
                                            error: function () {$dfd.reject();}
                                        });
                                    });
                                }
                            },
                            fields:{
                                Kode : {
                                    title : "Kode",key : true,edit : false,list : false
                                },
                                KodeNota : {
                                    title : "Kode Nota",key : true,edit : false
                                },
                                NoCekBG :{
                                    title :"No CekBG",
                                    inputClass: 'validate[required,maxSize[50]]'
                                },
                                Bank :{
                                    title :"Bank",
                                    inputClass: 'validate[required]',
                                    options: 'dfaaction.php?act=getnamabank'
                                },
                                BankBaru : {
                                    title:"Tambah Bank Baru",
                                    type: 'checkbox',
                                    values: { 'false': 'Tidak', 'true': 'Ya' },
                                    list : false,
                                    edit : true
                                },
                                TglJatuhTempo :{
                                    title :"Tgl Jatuh Tempo",type: 'date',displayFormat: 'yy-mm-dd',inputClass: 'validate[required,custom[date]]'
                                },
                                Jml :{
                                    title :"Nominal"
                                },
                                BGStatus:{
                                    title:"BG Status",
                                    type: 'checkbox',
                                    values: { 'false': 'Tidak Valid', 'true': 'Valid' }
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

                        $('#DetailCekBG').jtable('load');
						
                    }
                },
                error: function () {
                }
            });
        }

        // Get Total Cek BG Valid
        function getTotalCekGValid(ArrKKPDV){
            $.ajax({
                url: 'dfaaction.php?act=gettotalcekbgvalid&f='+ArrKKPDV,
                type: 'POST',
                data : {} ,
                dataType: 'json',
                success: function (response) {
                    TotalCekBG = response[0].BG;
                    $('#cekbgRp').text(accounting.formatMoney(TotalCekBG,options));
                },
                error: function () {}
            });
        }

        // Get Total Cek BG Valid
        function getTotalTransferValid(ArrKKPDV){
            $.ajax({
                url: 'dfaaction.php?act=gettotaltransfervalid&f='+ArrKKPDV,
                type: 'POST',
                data : {} ,
                dataType: 'json',
                success: function (response) {
                    TotalTransfer = response[0].Transfer;
                    $('#transferRp').text(accounting.formatMoney(TotalTransfer,options));
                },
                error: function () {}
            });
        }

        // Get Total Cek BG Valid
        function CekValidDataCekBG(ArrKKPDV){
            $.ajax({
                url: 'dfaaction.php?act=cekvaliddatacekbg&f='+ArrKKPDV,
                type: 'POST',
                data : {} ,
                dataType: 'json',
                success: function (response) {
                    if(response[0].Jml>0){
                        Pass=0;
                        if(response[0].NoCekBG == 'yogixxaditya'){
                            NoCekBGNotValid = 'KKPDV ini sudah diproses, Batalkan transaksi ini dan pilih KKPDV lain!';
                        }else if(response[0].NoCekBG == 'yogixxxaditya'){
                            NoCekBGNotValid = 'Anda memilih KKPDV yang tidak mempunyai nominal tunai,Cek BG dan Transfer!';
                        }else{
                            NoCekBGNotValid = 'Data CekBG '+response[0].NoCekBG+' Kurang Lengkap!!!';
                        }
                    }else{
                        Pass=1;
                    }

                    if(Pass==0){
                        new PNotify({
                            title: 'Kesalahan',
                            text: ' '+NoCekBGNotValid,
                            type : 'error'
                        });
                    }else{
                        $.ajax({
                            url: 'dfaaction.php?act=cekvaliddatadetailtranfer&f='+ArrKKPDV,
                            type: 'POST',
                            data : {} ,
                            dataType: 'json',
                            success: function (responses) {
                                if(responses[0].Jml>0){
                                    if(responses[0].Faktur == 'yogixxaditya'){
                                        NoCekBGNotValid = 'KKPDV ini sudah diproses, Batalkan transaksi ini dan pilih KKPDV lain!';
                                    }else if(responses[0].Faktur == 'yogixxxaditya'){
                                        NoCekBGNotValid = 'Anda memilih KKPDV yang tidak mempunyai nominal tunai,Cek BG dan transfer!';
                                    }else{
                                        NoCekBGNotValid = 'Data Transfer KKPDV  '+responses[0].Faktur+' Kurang Lengkap!!!';
                                    }
                                    new PNotify({
                                        title: 'Kesalahan',
                                        text: ' '+NoCekBGNotValid,
                                        type : 'error'
                                    });
                                }else{
                                    $.ajax({
                                        url: 'dfaaction.php?act=generatenc&f='+ArrKKPDV+'&g='+selectedKodeSopir+'&h='+selectedNamaSopir,
                                        type: 'POST',
                                        data : {} ,
                                        success: function (response) {
                                            alert("Status : "+ response);
                                            location.reload();
                                        },
                                        error: function () {
                                            alert("Error : "+ response);
                                        }
                                    });
                                }
                            },
                            error: function () {
                                alert("Error : "+ response);
                            }
                        });
                    }

                },
                error: function () {
                }
            });
        }

        //

        $('#btnSubmit').click(function(){
            if(fin==""){
                new PNotify({
                    title: 'Kesalahan',
                    text: 'Pilih Dulu KKPDV yang akan diproses!!',
                    type : 'error'
                });
            }else{
                $('#homeP').hide();
                $('#kkpdvP').show();
                getProsesKKPDV(fin);
                $('#LblNamaSopir').text("Sopir : "+namaSopir);

            }
        });

        getListSopir();
        getCabangDivisi();

        $('#btnrefreshslc').click(function(){
            $('#slcsopir').empty();
            getListSopir();
        });

        //Prepare jTable
        $('#TableDaftarKKPDVPerSopir').jtable({
            actions: {
                listAction: 'dfaaction.php?act=list&f='
            }
        });

        //Load person list from server
        $('#TableDaftarKKPDVPerSopir').jtable('load');

        $('#btnCari').click(function(){
            if(($("#slcsopir option:selected").val()=="-1")||($("#slcsopir option:selected").val()==undefined)){
                new PNotify({
                    title: 'Kesalahan',
                    text: 'Pilih Kode Sopir Dulu!!',
                    type : 'error'
                });
            }else{
                // MunculKan Label Tunai dan BG
                $('#lbltunai').show();
                $('#lblcekbg').show();
                $('#lbltransfer').show();
                $.ajax({
                    url: 'dfaaction.php?act=getsopir&f='+$("#slcsopir option:selected").val(),
                    type: 'POST',
                    data : {} ,
                    success: function (response) {
                            $('#txtnamasopir').text("NAMA SOPIR : "+response);
                            namaSopir = response;
                            selectedNamaSopir = response;
                    },
                    error: function () {
                    }
                });

                selectedKodeSopir = $("#slcsopir option:selected").val();

                $('#TableDaftarKKPDVPerSopir').jtable('destroy');

                //Prepare jTable
                $('#TableDaftarKKPDVPerSopir').jtable({
                    title : 'Rangkuman Pengiriman',
                    selecting: true, //Enable selecting
                    multiselect: true, //Allow multiple selecting
                    selectingCheckboxes: true,
                    actions: {
                        listAction: 'dfaaction.php?act=list&f='+$("#slcsopir option:selected").val()
                    },
                    fields:{
                        Tgl :{
                            title :"Tgl"
                        },
                        Kodenota :{
                            title :"Kodenota",
                            key: true,
                            create: false,
                            edit: false
                        },
                        Tunai :{
                            title :"Nominal"
                        },
                        Transfer :{
                            title :"Transfer"
                        },
                        BG :{
                            title:"BG"
                        }
                    },
                    //Register to selectionChanged event to hanlde events
                    selectionChanged: function () {
                        //Get all selected rows
                        var $selectedRows = $('#TableDaftarKKPDVPerSopir').jtable('selectedRows');


                        $('#SelectedRowList').empty();
                        if ($selectedRows.length > 0) {
                            selectedData = [];
                            //Show selected rows
                            var i = 0;
                            var tunai = 0;
                            var transfer = 0;
                            var cekbg = 0;
                            $selectedRows.each(function () {
                                var record = $(this).data('record');
                                selectedData[i] = record.Kodenota;
                                if(((record.Tunai)<=0)&&((record.Transfer<=0))&&((record.BG)<=0)){
                                    $("#dialog-confirm").html("KKPDV "+record.Kodenota+" tidak mempunyai nominal tunai,transfer dan CekBG, KKPDV ini tidak akan ikut terproses");

                                    // Define the Dialog and its properties.
                                    $("#dialog-confirm").dialog({
                                        resizable: false,
                                        modal: true,
                                        title: "Info KKPDV : "+record.Kodenota,
                                        width: 400,
                                        buttons: {
                                            "OK": function () {
                                                $(this).dialog('close');
                                            }
                                        }
                                    });
                                }else{
                                    if(fin==""){
                                        fin = "'"+selectedData[i]+"'";
                                    }else{
                                        fin = fin + ", '"+selectedData[i]+"'";
                                    }
                                }

                                console.log(fin);

                                tunai = tunai + record.Tunai;
                                transfer = transfer + record.Transfer;
                                cekbg = cekbg + record.BG;
                                i++;
                            });
                            $('#tunai').text(""+accounting.formatMoney(tunai,options));
                            $('#transfer').text(""+accounting.formatMoney(transfer,options));
                            $('#cekbg').text(""+accounting.formatMoney(cekbg,options));
                        } else {
                            //No rows selected
                            fin = "";
                            $('#tunai').text("0");
                            $('#transfer').text("0");
                            $('#cekbg').text("0");
                        }
                    }
                });

                //Load person list from server
                $('#TableDaftarKKPDVPerSopir').jtable('load');

                // Hide Tombol Submit
                $('#btnSubmit').show();

            }
        });

        $('#DetailTunai').jtable({
            title : 'Detail Tunai',
            selecting: true, //Enable selecting
            multiselect: true, //Allow multiple selecting
            actions: {
                listAction: 'dfaaction.php?act=list&f='
            }
        });

        $('#DetailTunai').jtable('load');

        $('#DetailTunai').jtable('destroy');

        //Show Table Tunai Detail
        $('#btnDetailTunai').click(function(){
            if(TotalTunai>0){
                if((showTunDet)&&(showBGDet)){
                    $('#DetailTunai').jtable('destroy');
                    showTunDet = false;
                }else if((showTunDet)&&(!showBGDet)){

                }
                else{
                    $('#DetailTunai').jtable({
                        title : 'Detail Tunai',
                        selecting: true, //Enable selecting
                        multiselect: true, //Allow multiple selecting
                        selectingCheckboxes: true,
                        actions: {
                            listAction: 'dfaaction.php?act=getdetailtunai&f='+fin,
                            updateAction: function(postData) {
                                return $.Deferred(function ($dfd) {
                                    $.ajax({
                                        url: 'dfaaction.php?act=updatedetailtunai',
                                        type: 'POST',
                                        dataType: 'json',
                                        data: postData,
                                        success: function (data) {
                                            $dfd.resolve(data);
                                            $('#DetailTunai').jtable('reload');
                                            //getTotalCekGValid(fin);
                                        },
                                        error: function () {$dfd.reject();}
                                    });
                                });
                            }
                        },
                        fields:{
                            Kode : {
                                title : "Kode",
                                key : true,
                                edit : false,
                                list : false
                            },
                            Kodenota :{
                                title :"Kodenota",
                                width: '45%',
                                key: true,
                                create: false,
                                edit: false
                            },
                            Tunai :{
                                title :"Nominal",
                                width: '50%'
                            }
                        },
                        rowInserted: function (event, data) {
                            $('#DetailTunai').jtable('selectRows', data.row);
                        },
                        //Register to selectionChanged event to hanlde events
                        selectionChanged: function () {
                            //Get all selected rows
                            var $selectedRows = $('#DetailTunai').jtable('selectedRows');
                            TotalTunai = 0;
                            fin ="";
                            $('#SelectedRowList').empty();
                            if ($selectedRows.length > 0) {
                                //Show selected rows
                                $selectedRows.each(function () {
                                    var record = $(this).data('record');
                                    TotalTunai = TotalTunai + record.Tunai;
                                    $('#tunaiRp').text(accounting.formatMoney(TotalTunai,options));
                                    if(fin==""){
                                        fin = "'"+record.Kodenota+"'";
                                    }else{
                                        fin = fin + ", '"+record.Kodenota+"'";
                                    }
                                });
                            } else {
                                //No rows selected
                                $('#tunaiRp').text(accounting.formatMoney(0,options));
                            }
                            /*console.log("Fin : "+fin);*/
                            // Ubah Table CekBG
                            if(TotalCekBGXX>0){
                                $('#DetailCekBG').jtable('destroy');

                                $('#DetailCekBG').jtable({
                                    title : 'Detail BG',
                                    selecting: true, //Enable selecting
                                    multiselect: true, //Allow multiple selecting
                                    /*                    selectingCheckboxes: true,*/
                                    actions: {
                                        listAction: 'dfaaction.php?act=getdetailcekbg&f='+fin,
                                        updateAction: function(postData) {
                                            return $.Deferred(function ($dfd) {
                                                $.ajax({
                                                    url: 'dfaaction.php?act=updatecekbg',
                                                    type: 'POST',
                                                    dataType: 'json',
                                                    data: postData,
                                                    success: function (data) {
                                                        $dfd.resolve(data);
                                                        getTotalCekGValid(fin);
                                                    },
                                                    error: function () {$dfd.reject();}
                                                });
                                            });
                                        }
                                    },
                                    fields:{
                                        Kode : {
                                            title : "Kode",key : true,edit : false,list : false
                                        },
                                        KodeNota : {
                                            title : "Kode Nota",key : true,edit : false
                                        },
                                        NoCekBG :{
                                            title :"No CekBG"
                                        },
                                        Bank :{
                                            title :"Bank",
                                            inputClass: 'validate[required]',
                                            options: 'dfaaction.php?act=getnamabank'
                                        },
                                        BankBaru : {
                                            title:"Tambah Bank Baru",
                                            type: 'checkbox',
                                            values: { 'false': 'Tidak', 'true': 'Ya' },
                                            list : false,
                                            edit : true
                                        },
                                        TglJatuhTempo :{
                                            title :"Tgll Jatuh Tempo",type: 'date',displayFormat: 'yy-mm-dd',inputClass: 'validate[required,custom[date]]'
                                        },
                                        Jml :{
                                            title :"Jumlah"
                                        },
                                        BGStatus:{
                                            title:"BG Status",
                                            type: 'checkbox',
                                            values: { 'false': 'Tidak Valid', 'true': 'Valid' }
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

                                $('#DetailCekBG').jtable('load');
                            }

                            // Get Total Cek BG
                            getTotalCekGValid(fin);

                            if(TotalTransferXX>0){
                                $('#DetailTransfer').jtable('destroy');

                                $('#DetailTransfer').jtable({
                                    title : 'Detail Transfer',
                                    selecting: true, //Enable selecting
                                    multiselect: true, //Allow multiple selecting
                                    /*                    selectingCheckboxes: true,*/
                                    actions: {
                                        listAction: 'dfaaction.php?act=getdetailtransfer&f='+fin,
                                        updateAction: function(postData) {
                                            return $.Deferred(function ($dfd) {
                                                $.ajax({
                                                    url: 'dfaaction.php?act=updatetransfer',
                                                    type: 'POST',
                                                    dataType: 'json',
                                                    data: postData,
                                                    success: function (data) {
                                                        $dfd.resolve(data);
                                                        getTotalTransferValid(fin);
                                                    },
                                                    error: function () {$dfd.reject();}
                                                });
                                            });
                                        }
                                    },
                                    fields:{
                                        Kode : {
                                            title : "Kode",key: true,list:false
                                        },
                                        KodeNota : {
                                            title : "Kode Nota",key:true,edit:false
                                        },
                                        KodeBank :{
                                            title : "Kode Bank",
                                            inputClass: 'validate[required]',
                                            options: 'dfaaction.php?act=getnamabanktransfer'
                                        },
                                        TglTransfer : {
                                            title: "Tgl Transfer" ,type: 'date',displayFormat: 'yy-mm-dd',inputClass: 'validate[required,custom[date]]'
                                        },
                                        Jml : {
                                            title : "Nominal",
                                            inputClass: 'validate[required]'
                                        },
                                        TransferStatus:{
                                            title:"Transfer Status",
                                            type: 'checkbox',
                                            values: { 'false': 'Tidak Valid', 'true': 'Valid' }
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

                                $('#DetailTransfer').jtable('load');
                            }

                            getTotalTransferValid(fin);
                        }
                    });
                    $('#DetailTunai').jtable('load');
                    showTunDet = true;
                }
            }
        });

        //Show Table BG Detail
        $('#btnDetailCekBG').click(function(){
            if(TotalCekBG>0){
                if((showBGDet)&&(showTunDet)){
                    $('#DetailCekBG').jtable('destroy');
                    showBGDet = false;
                }else{
                    $('#DetailCekBG').jtable({
                        title : 'Detail BG',
                        selecting: true, //Enable selecting
                        multiselect: true, //Allow multiple selecting
                        /*                    selectingCheckboxes: true,*/
                        actions: {
                            listAction: 'dfaaction.php?act=getdetailcekbg&f='+fin,
                            updateAction: function(postData) {
                                return $.Deferred(function ($dfd) {
                                    $.ajax({
                                        url: 'dfaaction.php?act=updatecekbg',
                                        type: 'POST',
                                        dataType: 'json',
                                        data: postData,
                                        success: function (data) {
                                            $dfd.resolve(data);
                                            getTotalCekGValid(fin);
                                        },
                                        error: function () {$dfd.reject();}
                                    });
                                });
                            }
                        },
                        fields:{
                            Kode : {
                                title : "Kode",key : true,edit : false,list : false
                            },
                            KodeNota : {
                                title : "Kode Nota",key : true,edit : false
                            },
                            NoCekBG :{
                                title :"No CekBG"
                            },
                            Bank :{
                                title :"Bank",
                                inputClass: 'validate[required]',
                                options: 'dfaaction.php?act=getnamabank'
                            },
                            BankBaru : {
                                title:"Tambah Bank Baru",
                                type: 'checkbox',
                                values: { 'false': 'Tidak', 'true': 'Ya' },
                                list : false,
                                edit : true
                            },
                            TglJatuhTempo :{
                                title :"Tgl Jatuh Tempo",type: 'date',displayFormat: 'yy-mm-dd',inputClass: 'validate[required,custom[date]]'
                            },
                            Jml :{
                                title :"Nominal"
                            },
                            BGStatus:{
                                title:"BG Status",
                                type: 'checkbox',
                                values: { 'false': 'Tidak Valid', 'true': 'Valid' }
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
                    $('#DetailCekBG').jtable('load');
                    showBGDet = true;
                }

            }
        });


        //Submit Final
        $('#btnSubmitFinal').click(function(){
            if(fin == ""){
                new PNotify({
                    title: 'Kesalahan',
                    text: 'Tidak Ada KKPDV Yang di Proses!!!',
                    type : 'error'
                });
            }else{
                $.ajax({
                    url: 'dfaaction.php?act=cekinputkasirvsdelivery&f='+fin,
                    type: 'POST',
                    dataType: 'json',
                    data: {},
                    success: function(data) {
                        var JmlKasir=0,JmlDelivery=0;
                        $.each(data,function(idx,obj){
                             JmlKasir = JmlKasir + obj.JmlKasir;
                             JmlDelivery = JmlDelivery + obj.JmlDelivery;
                        });
                        console.log(JmlKasir);
                        console.log(JmlDelivery);
                        if((JmlKasir)>=(Math.floor(JmlDelivery/100)*100)){
                            fnBukaSubmitDialog(selectedNamaSopir);
                        }else{
                            new PNotify({
                                title: 'Kesalahan',
                                text: 'Total Transfer dan Tunai yang di inputkan lebih kecil dari sistem,Apakah anda ingin lanjut memproses?',
                                type : 'Info',
                                hide: false,
                                confirm: {
                                    confirm: true
                                },
                                buttons: {
                                    closer: false,
                                    sticker: false
                                },
                                history: {
                                    history: false
                                }
                            }).get().on('pnotify.confirm', function() {
                                fnBukaSubmitDialog(selectedNamaSopir);
                            }).on('pnotify.cancel', function() {});
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

        // Dialog Submit
        function fnBukaSubmitDialog(NamaSopir) {
            $("#dialog-confirm").html("Apakah anda yakin akan memproses trasanksi sopir : '"+NamaSopir+"' ?");

            // Define the Dialog and its properties.
            $("#dialog-confirm").dialog({
                resizable: false,
                modal: true,
                title: "Konfirmasi Proses",
                width: 400,
                buttons: {
                    "Ya": function () {
                        $(this).dialog('close');
                        CekValidDataCekBG(fin);
                    },
                    "Tidak": function () {
                        $(this).dialog('close');
                    }
                }
            });
        }

        $('#btnCancelProses').click(function(){
            fnBukaBatalDialog();
        });

        $("#slcsopir").select2();
    });
		
		function editbank(id) {
            if(id=='Edit-BankBaru'){
                if($('#Edit-BankBaru').val() == "false"){
                    $('#Edit-Bank').replaceWith(function(){
                        return '<input type="text" name="Bank" id="Edit-Bank" class="validate[required]" value="">';
                    });
                }else{
                    $('#Edit-Bank').replaceWith(function(){
                        return '<select id="Edit-Bank" class="validate[required]" name="Bank"></select>';
                    });
                    getListBank();
                }
            }
		};

        // Get Data For Bank Select List
        function getListBank(){
            $.ajax({
                url: 'dfaaction.php?act=getnamabanks',
                type: 'POST',
                data : {} ,
                success: function (response) {
                    $.each($.parseJSON(response), function(idx, obj) {
                        if(typeof obj.DisplayText !== "undefined"){
                            $('#Edit-Bank').append($('<option>', {
                                value: obj.Value,
                                text : obj.DisplayText
                            }));
                        }
                    });
                },
                error: function () {}
            });
        }
</script>
</body>
</html>

