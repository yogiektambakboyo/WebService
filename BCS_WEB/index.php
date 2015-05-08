<?php
session_start();
if ((!isset($_SESSION["usernamebcs"]))||(!isset($_SESSION["jabatanbcs"]))) {
    header("location:login.php");
}
include "setting/include.php";

$db = new DB();
$koneksi = $db->connectDB("30");
$err = array();
$result;

if ($koneksi["status"]) {
if (isset($_POST["csvexport"])) {
    $kode = $_POST["kodedivisi"];
    if(strlen($kode)>4){
        $title = "CustomerSurvey_" . $_SESSION["cabangbcs"]."_".date('Ymd_Hi').".csv";
        header("Content-disposition: attachment; filename=$title");
        header("Content-Type: text/csv");
        $handle = fopen("php://output", "w");
        fwrite($handle, "KodeCabang;KodeDivisi;Perusahaan;Pemilik;Penghubung;Segment;SubSegment;Alamat;Kota;KodePos;Kecamatan;Kelurahan;NoTelp;NoHP;Longitude;Latitude;CreateBy;TglEntry;CreateDate\n");
        $sql = "select * from bcs_pelanggancabang where kodedivisi in (".$kode.")";
        $resultorder = $db->queryDB($sql);
        if ($resultorder["jumdata"] == 0) {
            $err[] = "Gagal Create File,Silahkan Coba Ulang";
        } else {
            while ($row = mssql_fetch_assoc($resultorder["result"])) {
                fwrite($handle,$row["KodeCabang"]. ";" . $row["KodeDivisi"] . ";" . $row["Perusahaan"] .";". $row["Pemilik"] .";". $row["Penghubung"] .";". $row["Segment"] .";". $row["SubSegment"] .";". $row["Alamat"] .";". $row["Kota"] .";". $row["KodePos"] .";". $row["Kecamatan"] .";". $row["Kelurahan"] .";". $row["NoTelp"] .";". $row["NoHP"] .";". $row["Longitude"] .";". $row["Latitude"] .";". $row["CreateBy"] .";". date("m/d/Y", strtotime($row["TglEntry"])) . ";" . date("m/d/Y", strtotime($row["CreateDate"])) . ";". "\n");
            }
        }
        fclose($handle);exit;
    }else{
        $err = "Pilih dahulu Pelanggan yang akan di eksport";
    }
}
} else {
    $err[] = "Koneksi Pusat Putus";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>BCS - Borwita Customer Service</title>
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
            <a class="navbar-brand" href="#">Borwita Customer Survey</a>
        </div>
        <div class="navbar-collapse collapse">
            <ul class="nav navbar-nav">
                <li class="active"><a href="index.php"><span class="glyphicon glyphicon-home"></span> Home</a></li>
                <li><a href="logout.php"><span class="glyphicon glyphicon-log-out"></span> Log Out</a></li>
            </ul>
            <ul class="nav navbar-nav navbar-right">
                <li><a href="#"><span class="glyphicon glyphicon-user"></span> User : <?php echo $_SESSION["usernamebcs"];?></a></li>
            </ul>
        </div>
    </div>
</div>

<div class="container theme-showcase" role="main">

    <div class="divider-vertical-up"></div>
    <h3><span class="glyphicon glyphicon-list"></span> Data Pelanggan ( <label id="LblCbg"><? echo $_SESSION["cabangbcs"];?></label> )</h3>
    <div class="divider-vertical-down"></div>

    <div class="row">
        <div class="col-md-8">
            <form>
                Perusahaan : <input type="text" name="name" id="name"/>
                Tgl Awal : <input type="text" name="tglawal" id="tglawal"/>
                Tgl Akhir : <input type="text" name="tglakhir" id="tglakhir"/>
                <button type="submit" id="LoadRecordsButton" class="btn btn-primary">Cari</button>
            </form>
        </div>
        <div class="col-md-4">
            <form method="POST" action="index.php">
                <input type="submit" name="csvexport" class="btn btn-success" value="Export CSV">
                <input type="hidden" name="kodedivisi" id="kodedivisi">
            </form>
        </div>
    </div>
    <br>
    <?php
        if (isset($_POST["csvexport"])) {
            echo   "<div class='alert alert-danger'><h5>".$err."</h5></div><br>";
        }
    ?>


    <div class="row" id="homeP">
        <div id="TableDaftarPelanggan"></div>
        <div class="form-group">
            <div class="divider-vertical-up"></div>
            <div class="divider-vertical-down"></div>
        </div>
    </div>

</div>

<script type="text/javascript">

    $(function() {
        $('#tglawal').datepicker({
            dateFormat: 'yy-mm-dd'
        });
        $('#tglakhir').datepicker({
            dateFormat: 'yy-mm-dd'
        });
    });

    $(document).ready(function () {
        //Prepare jTable
        $('#TableDaftarPelanggan').jtable({
            title : 'Daftar Pelanggan Cabang',
            paging: true,
            pageSize: '10',
            sorting: true,
            selecting: true, //Enable selecting
            multiselect: true, //Allow multiple selecting
            selectingCheckboxes: true, //Show checkboxes on first column
            selectOnRowClick: false, //Enable this to only select using checkboxes
            defaultSorting: 'KodeCabang ASC',
            /*toolbar: {
                items: [{
                    tooltip: 'Click here to export this table to excel',
                    icon: '/BCS/img/excel.png',
                    text: 'Export to Excel',
                    click: function () {
                        $.ajax({
                            url: 'index.php',
                            type: 'POST',
                            data : { 'submit' : true} ,
                            success: function (response) {
                            },
                            error: function () {}
                        });
                    }
                }]
            },*/
            actions: {
                listAction: 'bcs_rest.php?act=list'
            },
            fields:{
                Expand : {
                    title:"",
                    width: '3%',
                    sorting: false,
                    edit: false,
                    create: false,
                    display: function (DataPelangganLama) {
                        //Create an image that will be used to open child table
                        var $img = $('<img src="img/list_metro.png" title="Lihat data pelanggan lama" />');
                        //Open child table when user clicks the image
                        $img.click(function () {
                            $('#TableDaftarPelanggan').jtable('openChildTable',
                                $img.closest('tr'),
                                {
                                    title: 'Data Pelanggan Lama',
                                    actions: {
                                        listAction: 'bcs_rest.php?act=listpelangganlama&kode=' + DataPelangganLama.record.KodeDivisi
                                    },
                                    fields: {
                                        Kode :{
                                            title :"Kode Divisi",
                                            key : true
                                        },
                                        Perusahaan :{
                                            title :"Perusahaan"
                                        },
                                        Penghubung :{
                                         title :"Penghubung"
                                        },
                                        Segment :{
                                            title :"Segment"
                                        },
                                        Alamat :{
                                            title :"Alamat"
                                        },
                                        Longitude :{
                                            title :"Longitude"
                                        },
                                        Latitude :{
                                            title :"Latitude"
                                        }
                                    }
                                }, function (data) { //opened handler
                                    data.childTable.jtable('load');
                                });
                        });
                        //Return image to show on the person row
                        return $img;
                    }
                },
                KodeCabang :{
                    title :"Kode Cabang"
                },
                KodeDivisi :{
                    title :"Kode Divisi",
                    key : true
                },
                Perusahaan :{
                    title :"Perusahaan"
                },
                Pemilik :{
                    title :"Pemilik"
                },
/*                Penghubung :{
                    title :"Penghubung"
                },*/
                Segment :{
                    title :"Segment"
                },
                SubSegment :{
                    title :"Sub Segment"
                },
                Alamat :{
                    title :"Alamat"
                },
/*                Kota :{
                    title :"Kota"
                },
                KodePos :{
                    title :"Kode Pos"
                },
                Kecamatan :{
                    title :"Kecamatan"
                },
                Kelurahan :{
                    title :"Kelurahan"
                },
                NoTelp :{
                    title :"No Telp"
                },
                NoHP :{
                    title :"Hand Phone"
                },*/
                Longitude :{
                    title :"Longitude"
                },
                Latitude :{
                    title :"Latitude"
                },
                CreateDate : {
                    title : "CreateDate"
                }
            },
            //Register to selectionChanged event to hanlde events
            selectionChanged: function () {
                //Get all selected rows
                var $selectedRows = $('#TableDaftarPelanggan').jtable('selectedRows');

                $('#SelectedRowList').empty();
                if ($selectedRows.length > 0) {
                    //Show selected rows
                    var kode ='';
                    $selectedRows.each(function () {
                        var record = $(this).data('record');
                        if(kode.length<3){
                            kode = "'"+record.KodeDivisi+"'";
                        }else{
                            kode = kode +",'"+ record.KodeDivisi+"'";
                        }
                    });
                    $('#kodedivisi').val(kode);
                } else {
                    $('#kodedivisi').val('');
                }
            }
        });

        //Re-load records when user click 'load records' button.
        $('#LoadRecordsButton').click(function (e) {
            e.preventDefault();
            if(($('#tglawal').val().length<1)||($('#tglakhir').val().length<1)){
                new PNotify({
                    title : 'Info',
                    text : 'Pilih dahulu tgl'
                });
            }else{
                $('#TableDaftarPelanggan').jtable('load', {
                    Perusahaan : $('#name').val(),
                    CreateDateAwal : $('#tglawal').val(),
                    CreateDateAkhir : $('#tglakhir').val()
                });
            }
        });

        //Load all records when page is first shown
        $('#LoadRecordsButton').click();

        //Load person list from server
        $('#TableDaftarPelanggan').jtable('load');
    });
</script>
</body>
</html>

