<?php
session_start();
if ((!isset($_SESSION["usernamedfa"]))||(!isset($_SESSION["jabatandfa"]))||(!isset($_SESSION["cabangdfa"]))) {
    header("location:login.php");
}

if(($_SESSION["jabatandfa"]!="INKASO")&&($_SESSION["jabatandfa"]!="ADMINISTRATOR")){
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
    <title>DFA - Pembersihan</title>
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
                <li class="active"><a href="clearence.php"><span class="glyphicon glyphicon-trash"></span> Pembersihan TT/Stempel/Tak Terkirim</a></li>
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
    <h3><span class="glyphicon glyphicon-trash"></span> Pembersihan ( <label id="LblCbg"></label> )</h3>
    <div class="divider-vertical-down"></div>


    <div class="row" id="homeP">

        <div class="col-lg-12">
            <label for="slcfilter" class="col-sm-1">Pencarian</label>
            <div class="col-sm-4">
                <select id="slcfilter" name="slcfilter" class="form-control">
                    <option value="0">Semua</option>
                    <option value="1">Sopir</option>
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

    <br>
    <input type="text" hidden="true" id="Cbg" value="<?php echo($_SESSION["cabangdfa"]);?>">

</div>

<script type="text/javascript">
    $(document).ready(function (){
        $('#slcfiltersopir').hide();
        var selectedData = [];
        $('#btnProses').hide();
        $('#txttglawal').hide();
        $('#txttglakhir').hide();
        $('#lblfilter').hide();
        $('#lblfiltertglakhir').hide();
        $('#txttglawal').datepicker({ dateFormat: 'yy-mm-dd' });
        $('#txttglakhir').datepicker({ dateFormat: 'yy-mm-dd' });

        var fin="";

        var filter = '';

        //Prepare jTable
        $('#TableDaftarKKPDVNoTunai').jtable({
            actions: {
                listAction: 'dfaaction.php?act=list&f='
            }
        });

        //Load person list from server
        $('#TableDaftarKKPDVNoTunai').jtable('load');

        $('#btnCari').click(function(){
            if($('#slcfilter').val()=="1"){
                filter = ' and m.Sopir=\''+$('#slcfiltersopir').val()+'\' ';

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
                multiselect: true, //Allow multiple selecting
                selectingCheckboxes: true,
                actions: {
                    listAction: 'dfaaction.php?act=listnotunai&f='+filter
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
                    Faktur :{
                        title:"Faktur"
                    },
                    Sopir :{
                        title :"Sopir",
                        list : false
                    },
                    Nama :{
                        title :"Nama"
                    },
                    Stempel :{
                        title:"Stempel"
                    },
                    TandaTerima :{
                        title:"TT"
                    },
                    StatusKirim:{
                        title:"Status Kirim"
                    }
                },
                //Register to selectionChanged event to hanlde events
                selectionChanged: function () {
                    //Get all selected rows
                    var $selectedRows = $('#TableDaftarKKPDVNoTunai').jtable('selectedRows');
                    $('#SelectedRowList').empty();
                    if ($selectedRows.length > 0) {
                        selectedData = [];
                        //Show selected rows
                        var i = 0;
                        $selectedRows.each(function () {
                            var record = $(this).data('record');
                            selectedData[i] = record.Faktur;
                            if(fin==""){
                                fin = "'"+selectedData[i]+"'";
                            }else{
                                fin = fin + ", '"+selectedData[i]+"'";
                            }
                            i++;
                        });
                    } else {
                        //No rows selected
                        fin = "";
                    }
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
                    text: 'Tidak ada dokumen yag di proses',
                    type : 'error'
                });
            }else if(fin==""){
                new PNotify({
                    title: 'Kesalahan',
                    text: 'Pilih dulu dokumen yang akan di proses',
                    type : 'error'
                });
            }else{
                $.ajax({
                    url: 'dfaaction.php?act=updatekkpdvnotunai&f='+fin,
                    type: 'POST',
                    success: function (response) {
                        alert("Status : "+ response);
                        location.reload();
                    },
                    error: function () {
                        alert("Status : Gagal Update");
                        location.reload();
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
        function getListSopir(){
            $.ajax({
                url: 'dfaaction.php?act=getdetailsopirnotunai',
                type: 'POST',
                data : {} ,
                success: function (response) {
                    $.each($.parseJSON(response), function(idx, obj) {
                        if(typeof obj.Sopir !== "undefined"){
                            $('#slcfiltersopir').append($('<option>', {
                                value: obj.Sopir,
                                text : obj.Sopir+" - "+obj.Nama
                            }));
                        }
                    });
                },
                error: function () {}
            });
        }

        getCabangDivisi();
        getListSopir();


        $('#slcfilter').on('change', function() {
            $('#btnProses').hide();
            if($('#slcfilter').val()=="1"){
                $('#slcfiltersopir').show();
                $('#txttglawal').hide();
                $('#txttglakhir').hide();
                $('#lblfilter').show();
                $('#lblfiltertglakhir').hide();
                $('#lblfilter').text("Sopir");
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

    });


</script>
</body>
</html>

