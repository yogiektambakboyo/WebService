<?php
session_start();
if (!isset($_SESSION["username"])) {
    header("location:index.php");
}

include 'include/include.php';

$db = new DB();
$koneksi = $db->connectDB("01");
$err = array();
$result;
$tglinput;

if ($koneksi["status"]) {
    if (isset($_POST["orderbtn"])) {
        $tgl = trim($_POST["tglorder"]);
        $tglinput=$tgl;
        if ($tgl != "") {
            $sql = "select distinct sales,ISNULL(s.nama,'') as nama,tgl,CONVERT(VARCHAR(16),dateadd(day,datediff(day,0,m.createdate),0),121) as createdate,d.keterangan as divisi from sfa_masterorder m join sfa_divisi d on d.kode=substring(sales,4,2) left join sfa_salesperson s on s.kode=m.sales where tgl='" . $tgl . "' and left(sales,2)='".$_SESSION["cabang"]."'";
            $result = $db->queryDB($sql);
            if ($result["jumdata"] == 0) {
                $arr = array("status" => 0, "data" => "Data Kosong");
            }
        } else {
            $err[] = "Tanggal Order Tidak Boleh Kosong";
        }
    }
    if (isset($_POST["uploadbtn"])) {
        $tgl = trim($_POST["tglupload"]);
        $tglinput=$tgl;
        if ($tgl != "") {
            $sql = "select distinct sales,ISNULL(s.nama,'') as nama,tgl,CONVERT(VARCHAR(16),dateadd(day,datediff(day,0,m.createdate),0),121) as createdate,d.keterangan as divisi from sfa_masterorder m join sfa_divisi d on d.kode=substring(sales,4,2) left join sfa_salesperson s on s.kode=m.sales where CONVERT(VARCHAR(10), m.createdate, 121)='" . $tgl . "' and left(sales,2)='".$_SESSION["cabang"]."'";
            $result = $db->queryDB($sql);
            if ($result["jumdata"] == 0) {
                $arr = array("status" => 0, "data" => "Data Kosong");
            }
        } else {
            $err[] = "Tanggal Upload Tidak Boleh Kosong";
        }
    }
    if (isset($_POST["csvexport"])) {
        $order = $_POST["order"];
        $divisi = $_POST["divisi"];
        $tgls = str_replace("-","",$_POST["tglinput"]);
        $validate=1;
        if (count($order) > 0) {
            $stemp = "0";
            foreach($order as $rowchecker){
                $o = explode("()",$rowchecker);
                $s = substr($o[0],3,2);
                if($stemp=="0"){
                    $stemp = $s;
                }
                if($s!=$stemp){
                    $err[] = "Pilihlah sales yang memiliki divisi yang sama!";
                    $validate = 0;
                }else{
                    $stemp = $s;
                }
            }

            if($validate==1){
                $title = "OrderEntry_" . $_SESSION["cabang"]. $stemp . "_" . $tgls .".csv";
                header("Content-disposition: attachment; filename=$title");
                header("Content-Type: text/csv");
                $handle = fopen("php://output", "w");
                fwrite($handle, "DistributorCode;BranchCode;SalesRepCode;RetailerCode;OrderNo;OrderDate;UploadDate;ChildSKUCode;OrderQty;OrderQty(cases);DeliveryDate;Keterangan\n");
                foreach ($order as $roworder) {
                    $odr = explode("()",$roworder);
                    $sales = $odr[0];
                    $tgl = $odr[1];
                    $sql = "select * from sfa_masterorder where sales='" . $sales . "' and tgl='".$tgl."'";
                    $resultorder = $db->queryDB($sql);
                    if ($resultorder["jumdata"] == 0) {
                        $err[] = "Gagal Create File,Silahkan Coba Ulang";
                        break;
                    } else {
                        while ($row = mssql_fetch_assoc($resultorder["result"])) {
                            fwrite($handle, "DB001;" . substr($row["Sales"], 0, 2) . ";" . $row["Sales"] . ";" . $row["Shipto"] . ";" . $row["NoOrder"] . ";" . date("m/d/Y", strtotime($row["Tgl"])) . ";" . date("m/d/Y", strtotime($row["CreateDate"])) . ";" . $row["Brg"] . ";" . $row["QtyPcs"] . ";" . $row["QtyCrt"] . ";" . date("m/d/Y", strtotime($row["Tgl"])) . ";" . trim($row["Keterangan"]) . "\n");
                        }
                    }
                }
                fclose($handle);exit;
            }
        } else {
            $err[] = "Pilih Order Dahulu";
        }
    }
} else {
    $err[] = "Koneksi Pusat Putus";
}
?>

<html>
<head>
    <title>SFA BI</title>
    <?php include 'include/header.php' ?>
    <style type="text/css">
        .scroll{
            overflow: scroll;
            height: 500px;
            width: auto;
        }
        .buttonselect{
            position: absolute;
            top: 140px;
            right: 250px;
        }
    </style>

</head>
<body>
<div class="navbar navbar-static-top navbar-inverse">
    <div class="navbar-inner ">

        <div class="container">
            <h2><font color="#FFFFFF">SFA Order <?php echo $_SESSION["namacabang"]; ?></font></h2>
            <div class="nav-collapse collapse">
                <ul class="nav">
                    <?php
                    if(strtoupper($_SESSION["jabatan"])=="ADMINISTRATOR"){
                        ?>
                        <li>
                            <a href="#"><i class="icon-home icon-white"></i> Home</a>
                        </li>
                        <li>
                            <a href="masterbarang.php"><i class="icon-refresh icon-white"></i> Sync Master Barang</a>
                        </li>
                    <?php
                    }
                    ?>
                    <li>
                        <a href="logout.php"><i class="icon-user icon-white"></i> LOGOUT</a>
                    </li>
                </ul>
            </div><!-- /.nav-collapse -->
        </div><!-- /.container -->
    </div><!-- /.navbar-inner -->
</div><!-- /.navbar -->
<div class="container">
    <div class="row">
        <hr>
        <form method="POST" action="salesorder.php">
            <div class="span3 center">Tanggal Order:
                <input type="text" style="height: 30px" placeholder="Tgl Order" name="tglorder" class="input-medium tanggal" value="" />
            </div>
            <div class="span1 left">
                <input type="submit" class="btn-small btn-info" value="Search" name="orderbtn">
            </div>
            <div class="span3 center">Tgl Upload:
                <input type="text" style="height: 30px" placeholder="Tgl Upload" name="tglupload" class="input-medium tanggal" value="" />
            </div>
            <div class="span1 left">
                <input type="submit" class="btn-small btn-warning" value="Search" name="uploadbtn">
            </div>
            <div class="span4 center">
                <input type="submit" class="btn-small btn-inverse" value="Export CSV" name="csvexport">
            </div>

            <input type="hidden" value="<?php echo($tglinput);?>" name="tglinput">

            <br>
            <hr>
            <!--<div class="alert alert-error"><h5>This Page Under Development, Only Use For Testing Not Operational!!!!!!!</h5></div>-->
            <!--
            For Name generate divisi
            -->
            <input type="hidden" id="divisi" name="divisi" value="01" />
            <?php
            if (count($err) > 0) {
                foreach ($err as $row) {
                    ?>
                    <div class="alert alert-error"><h5><?php echo $row; ?></h5></div>
                <?php
                }
            }
            ?>
            <br>
            <div class="span12 left" id="tableholer">
                <table class="tablesorter" id="tableorder">
                    <thead>
                    <tr>
                        <th></th>
                        <th class="filter-select filter-exact" data-placeholder="- Pilih Divisi -">Divisi</th>
                        <th>Kode Sales</th>
                        <th>Sales</th>
                        <th>Tanggal</th>
                        <th>Upload</th>
                        <th>Detail</th>
                    </tr>
                    </thead>
                    <tfoot>
                    <tr>
                        <th colspan="8" class="pager form-horizontal" style="text-align: center">
                            <button class="btn first"><i class="icon-step-backward"></i></button>
                            <button class="btn prev"><i class="icon-arrow-left"></i></button>
                            <span class="pagedisplay"></span> <!-- this can be any element, including an input -->
                            <button class="btn next"><i class="icon-arrow-right"></i></button>
                            <button class="btn last"><i class="icon-step-forward"></i></button>
                            <select class="pagesize input-mini" title="Select page size">
                                <option value="10">10</option>
                                <option selected="selected" value="20">20</option>
                                <option value="30">30</option>
                                <option value="40">40</option>
                            </select>
                            <select class="pagenum input-mini" title="Select page number"></select>
                        </th>
                    </tr>
                    </tfoot>
                    <tbody>
                    <?php
                    if ($result["jumdata"] > 0) {
                        while ($row = mssql_fetch_assoc($result["result"])) {
                            ?>
                            <tr>
                                <td><input type="checkbox" name="order[]" value="<?php echo $row['sales']."()".date("Ymd", strtotime($row["tgl"])) ?>" id="<?php echo $row['divisi'] ?>" /></td>
                                <td id="mydivisi"><?php echo $row["divisi"] ?></td>
                                <td><?php echo $row["sales"] ?></td>
                                <td><?php echo $row["nama"] ?></td>
                                <td><?php echo date("d/m/Y", strtotime($row["tgl"])) ?></td>
                                <td><?php echo date("d/m/Y H:i", strtotime($row["createdate"])) ?></td>
                                <td><input id="<?php echo "show".strrev(str_replace('/','',$row['sales'])) ?>" type="button" class="btn-small btn-info" onclick="<?php echo "appenddo('".str_replace('/','',strrev($row['sales']))."',".date('Ymd', strtotime($row['tgl'])).")"; ?>" value="Detail"><input hidden="true" id="<?php echo "hide".strrev(str_replace('/','',$row['sales'])) ?>" type="button" class="btn-small btn-warning"  onclick="<?php echo "RemoveTR('".str_replace('/','',strrev($row['sales']))."')"; ?>" value="Close"></td>
                            </tr>
                        <?php
                        }
                    }
                    ?>
                    </tbody>
                </table>
            </div>
        </form>
        <div class="buttonselect">
            <button class="btn btn-small" id="selectall">Select All</button>
        </div>
    </div><!-- /.row -->
    <div class="row">
        <div class="span6">
            <table id="invoice">
                <thead>
                <tr>
                    <th>Sales</th>
                    <th>Kode Order</th>
                    <th>Pelanggan</th>
                    <th>Action</th>
                </tr>
                </thead>
                <tbody>

                </tbody>
            </table>
        </div>
        <div class="span6">
            <table id="barang">
                <thead>
                <tr>
                    <th>Kode</th>
                    <th>Barang</th>
                    <th>CRT</th>
                    <th>PCS</th>
                </tr>
                </thead>
                <tbody>

                </tbody>
            </table>
        </div>

    </div>
</div><!-- /.container -->

<?php include 'include/footer.php' ?>
<script type="text/javascript">
$('#selectall').click(function(){
    if($('#selectall').html()=='Select All')
    {
        //$('input:checkbox').prop('checked', true);

        if($('#divisi').val()=='01'){
            $('input:checkbox[id^="Borwita Indah"]').each(function(){
                $(this).prop('checked', true);
            });
        }
        if($('#divisi').val()=='04'){
            $('input:checkbox[id^="CERES"]').each(function(){
                $(this).prop('checked', true);
            });
        }
        if($('#divisi').val()=='06'){
            $('input:checkbox[id^="SUNCO"]').each(function(){
                $(this).prop('checked', true);
            });
        }
        if($('#divisi').val()=='09'){
            $('input:checkbox[id^="ABC PRESIDENT"]').each(function(){
                $(this).prop('checked', true);
            });
        }
        if($('#divisi').val()=='10'){
            $('input:checkbox[id^="HEINZ ABC"]').each(function(){
                $(this).prop('checked', true);
            });
        }
        if($('#divisi').val()=='11'){
            $('input:checkbox[id^="MDJ"]').each(function(){
                $(this).prop('checked', true);
            });
        }
        $('#selectall').html('Deselect All');
    }
    else
    {
        $('input:checkbox').prop('checked', false);
        $('#selectall').html('Select All');
    }

    var mydiv=$('#tableorder #mydivisi').text();
    if(mydiv.indexOf("CERES") > -1){
        $('#divisi').val("04");
    }
    if(mydiv.indexOf("Borwita Indah") > -1){
        $('#divisi').val("01");
    }
    if(mydiv.indexOf("SUNCO") > -1){
        $('#divisi').val("06");
    }
    if(mydiv.indexOf("ABC PRESIDENT") > -1){
        $('#divisi').val("09");
    }
    if(mydiv.indexOf("HEINZ ABC") > -1){
        $('#divisi').val("10");
    }
    if(mydiv.indexOf("MDJ") > -1){
        $('#divisi').val("11");
    }

});
$(".tanggal").datepicker({
    format: "yyyy-mm-dd"
});

$('#tableorder tbody tr').click(function(event) {
    var mydiv=$('#tableorder #mydivisi').text();
    if(mydiv.indexOf("CERES") > -1){
        $('#divisi').val("04");
    }
    if(mydiv.indexOf("Borwita Indah") > -1){
        $('#divisi').val("01");
    }
    if(mydiv.indexOf("SUNCO") > -1){
        $('#divisi').val("06");
    }
    if(mydiv.indexOf("ABC PRESIDENT") > -1){
        $('#divisi').val("09");
    }
    if(mydiv.indexOf("HEINZ ABC") > -1){
        $('#divisi').val("10");
    }
    if(mydiv.indexOf("MDJ") > -1){
        $('#divisi').val("11");
    }

    if (event.target.type !== 'checkbox') {
        $(':checkbox', this).trigger('click');
    }
});
$(function() {

    $.extend($.tablesorter.themes.bootstrap, {
        // look here: http://twitter.github.com/bootstrap/base-css.html#tables
        table      : 'table table-bordered',
        header     : 'bootstrap-header', // give the header a gradient background
        footerRow  : '',
        footerCells: '',
        icons      : '', // add "icon-white" to make them white; this icon class is added to the <i> in the header
        sortNone   : 'bootstrap-icon-unsorted',
        sortAsc    : 'icon-chevron-up',
        sortDesc   : 'icon-chevron-down',
        active     : '', // applied when column is sorted
        hover      : '', // use custom css here - bootstrap class may not override it
        filterRow  : '', // filter row class
        even       : '', // odd row zebra striping
        odd        : ''  // even row zebra striping
    });

    // call the tablesorter plugin and apply the uitheme widget
    $("table").tablesorter({
        theme : "bootstrap", // this will

        widthFixed: true,

        headerTemplate : '{content} {icon}',

        // widget code contained in the jquery.tablesorter.widgets.js file
        // use the zebra stripe widget if you plan on hiding any rows (filter widget)
        widgets : [ "uitheme", "filter", "zebra" ],
        headers: {
        },
        widgetOptions : {
            // using the default zebra striping class name, so it actually isn't included in the theme variable above
            // this is ONLY needed for bootstrap theming if you are using the filter widget, because rows are hidden
            zebra : ["even", "odd"],

            // reset filters button
            filter_reset : ".reset",
            filter_formatter : {
            }

            // set the uitheme widget to use the bootstrap theme class names
            // uitheme : "bootstrap"

        }
    })
        .tablesorterPager({

            // target the pager markup - see the HTML block below
            container: $(".pager"),

            // target the pager page select dropdown - choose a page
            cssGoto  : ".pagenum",

            // remove rows from the table to speed up the sort of large tables.
            // setting this to false, only hides the non-visible rows; needed if you plan to add/remove rows with the pager enabled.
            removeRows: false,

            // output string - default is '{page}/{totalPages}';
            // possible variables: {page}, {totalPages}, {filteredPages}, {startRow}, {endRow}, {filteredRows} and {totalRows}
            output: '{startRow} - {endRow} / {filteredRows} ({totalRows})'

        });

    // Allow changing an input from one column (any column) to another
    $('select').change(function(){
        // modify the search input data-column value (swap "0" or "all in this demo)
        $('.selectable').attr( 'data-column', $(this).val() );

        var mydiv = $('select :selected').val();
        if(mydiv.indexOf("CERES") > -1){
            $('#divisi').val("04");
        }
        if(mydiv.indexOf("Borwita Indah") > -1){
            $('#divisi').val("01");
        }
        if(mydiv.indexOf("SUNCO") > -1){
            $('#divisi').val("06");
        }
        if(mydiv.indexOf("ABC PRESIDENT") > -1){
            $('#divisi').val("09");
        }
        if(mydiv.indexOf("HEINZ ABC") > -1){
            $('#divisi').val("10");
        }
        if(mydiv.indexOf("MDJ") > -1){
            $('#divisi').val("11");
        }
    });

});

function appenddo(sales,tgl){
    $("#show"+sales).hide();
    $("#hide"+sales).show();

    var link = "get_ajax_kodeorder.php";
    var postdata={'sales':sales,'tgl':tgl};
    $.ajax({
        type: 'POST',
        url: link,
        dataType: 'jsonp',
        data: postdata,
        jsonp: 'jsoncallback',
        timeout: 5000,
        success: function(data){
            if(data.status==1){
                var str='';
                $.each(data.data, function(i,item){
                    var stats = '';
                    if(item["stat"]=='1'){
                        stats = '*';
                    }
                    str+='<tr id="detailinv'+sales+'"><td>'+item["nama"]+' '+stats+'</td><td>'+item["noorder"]+'</td><td>'+item["shipto"]+'</td><td><input type="button" class="btn-small btn-info" onclick="getAjaxBarang(\''+item["noorder"].substring(0,8).split("").reverse().join("")+'\','+item["noorder"].substring(9,15)+',\''+item["noorder"].substring(16,19).split("").reverse().join("")+'\')" value="Detail"></td></tr>';
                });
                $("#invoice tbody").html(str);
            }
        },
        error: function(){
            $("#invoice tbody").html('');
        }
    });

    $(".tablesorter").trigger("update");
    var sorting = [[$("#tableorder").data('sorting'), 0]];
    $("#tableorder").trigger("sorton", [sorting]);
}

function getAjaxBarang(a,b,c){
    var link = "get_ajax_barang_per_invoice.php";
    var postdata={'a':a,'b':b,'c':c};
    $.ajax({
        type: 'POST',
        url: link,
        dataType: 'jsonp',
        data: postdata,
        jsonp: 'jsoncallback',
        timeout: 5000,
        success: function(data){
            if(data.status==1){
                var str='';
                $.each(data.data, function(i,item){
                    str+='<tr id="brg"><td>'+item["brg"]+'</td><td>'+item["keterangan"]+'</td><td>'+item["crt"]+'</td><td>'+item["pcs"]+'</td></tr>';
                });
                $("#barang tbody").html(str);
            }
        },
        error: function(){
            $("#barang tbody").html('');
        }
    });
}

function RemoveTR(sales){
    $("#show"+sales).show();
    $("#hide"+sales).hide();

    $("#invoice #detailinv"+sales).remove();
    $("#barang #brg").remove();
}

</script>
</body>
</html>

