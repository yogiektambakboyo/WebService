<?php
session_start();
include_once "../taskoutbound/outbound_gettgltransaksi.php";
include_once "../script/connectbon.php";
?>
<div class="header">
    <h1 class="page-title">Task Outbound - Retur Beli</h1>
</div>
<div class="main-content" style="height:100%">
    <div class="row">
        <form class="form-inline" action="#">
            <div class="form-group"></div>
            <div class="form-group"></div>
            <div class="form-group"></div>
            <div class="form-group"></div>
            <div class="form-group"></div>
            <div class="form-group">
                <label for="outbound_tglawal">Tgl Awal :</label>
                <input type="text" class="form-control" id="outbound_tglawal" name="outbound_tglawal" value="<?php echo $_SESSION["TglTransaksiAwal"];?>">
            </div>
            <div class="form-group"></div>
            <div class="form-group">
                <label for="outbound_tglakhir">Tgl Akhir :</label>
                <input type="text" class="form-control" id="outbound_tglakhir" name="outbound_tglakhir" value="<?php echo $_SESSION["TglTransaksiAkhir"];?>">
            </div>
            <div class="form-group"></div>
            <div class="form-group">
                <label for="outbound_status">Status :</label>
                <select id="outbound_status" name="outbound_status" class="form-control">
                    <option value="2">All</option>
                    <option value="0">Open</option>
                    <option value="1">Closed</option>
                </select>
            </div>
            <div class="form-group"></div>
            <div class="form-group"></div>
            <button type="button" class="btn btn-primary" id="updateTableOutbound"><span class="glyphicon glyphicon-refresh" aria-hidden="true"></span>  Refresh</button>
        </form>
    </div>
    <br>
    <div class="scrollable-area" style="max-height:350px;overflow:scroll;font-size: 12px;">
        <div class="row" id="outbound_returbeli">
            <div class="table-responsive">
                <table class="table table-hover table-bordered tablesorter" style="white-space: nowrap;margin-left: 2%;" id="outbound_returbeli_table">
                    <thead>
                    <tr>
                        <th>No</th>
                        <th>Kodenota</th>
                        <th>Tanggal</th>
                        <th>Tipe Transaksi</th>
                        <th>Keterangan</th>
                        <th>isReceipt</th>
                        <th>isClosed</th>
                        <th>Sopir</th>
                        <th>DispatchNo</th>
                        <th colspan="2">Action</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php
                    $sql = "select Kodenota,CONVERT(CHAR(10), Tgl, 120) as Tgl,TransactionType,Keterangan,isHaveRcpt,isClosed,DriverId,DispatchNo from dbo.OutBound_Master WHERE isClosed=:isClose and (Tgl between :tglAwal and :tglAkhir)  and TransactionType='ReturBeli' and LEFT(Kodenota,5)=:cabang order by tgl";
                    $sth = $dbh->prepare($sql);
                    $isClosed = 0;
                    $tglAwal = $_SESSION["TglTransaksiAwal"];
                    $tglAkhir = $_SESSION["TglTransaksiAkhir"];
                    $sth->bindParam(":isClose",$isClosed,PDO::PARAM_INT);
                    $sth->bindParam(':tglAwal',$tglAwal,PDO::PARAM_STR);
                    $sth->bindParam(':tglAkhir',$tglAkhir,PDO::PARAM_STR);
                    $sth->bindParam(':cabang',$_SESSION["divisi"],PDO::PARAM_STR);

                    $result = $sth->execute();
                    $result=$sth->fetchAll();
                    $counter = 0;
                    foreach ($result as $row) {
                        $counter++;
                        echo '<tr>';
                        echo '<td>'.$counter.'</td>';
                        echo '<td>'.$row['Kodenota'].'</td>';
                        echo '<td>'.$row['Tgl'].'</td>';
                        echo '<td>'.$row['TransactionType'].'</td>';
                        echo '<td>'.$row['Keterangan'].'</td>';
                        echo '<td>'.$row['isHaveRcpt'].'</td>';
                        echo '<td>'.$row['isClosed'].'</td>';
                        echo '<td>'.$row['DriverId'].'</td>';
                        echo '<td>'.$row['DispatchNo'].'</td>';
                        echo '<td><a href="#" onclick="showDetail(\''.$row['Kodenota'].'\')" id="outbound_returbeli_modal"><span class="glyphicon glyphicon-eye-open"></span> Detail</a></td>';
                        if(($row['isHaveRcpt'] == 1)&&($row['isClosed']== 0)){
                            echo '<td><a href="#" onclick="outboundClose(\''.$row['isHaveRcpt'].'\',\''.$row['isClosed'].'\',\''.$row['Kodenota'].'\')" id="outbound_returbeli_modal_close"><span class="glyphicon glyphicon-saved"></span> Close</a></td>';
                        }else{
                            echo '<td></td>';
                        }
                        echo '</tr>';
                    }
                    ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<div id="outbound_returbeli_confirm" style="display:none; cursor: default" title="Konfirmasi Task Retur Beli">
    <p>Apakah anda yakin akan memproses transaksi ini?</p>
</div>


<!-- Modal -->
<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">Detail Retur Beli : <label id="outbound_returbeli_modal_lbl_so"></label> </h4>
            </div>
            <div class="modal-body">
                <div class="bs-example bs-example-tabs" role="tabpanel" data-example-id="togglable-tabs">
                    <div class="form-group"></div>
                    <ul class="nav nav-tabs" role="tablist">
                        <li role="presentation"  class="active"><a href="#send" role="tab" id="send-tab" data-toggle="tab" aria-controls="send"  aria-expanded="true">Send (<label id="outbound_modal_csend"></label>)</a></li>
                        <li role="presentation"><a href="#receipt" id="receipt-tab" role="tab" data-toggle="tab" aria-controls="receipt">Receipt  (<label id="outbound_modal_crcpt"></label>)</a></li>
                    </ul>
                    <div class="form-group"></div>
                    <div id="myTabContent" class="tab-content">
                        <div role="tabpanel" class="tab-pane fade in" id="receipt" aria-labelledby="receipt-tab">
                            <div class="table-responsive">
                                <table class="table table-hover table-bordered tablesorter" style="white-space: nowrap;" id="outbound_modal_receipt">
                                    <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>LineNumber</th>
                                        <th>SKU</th>
                                        <th>Keterangan</th>
                                        <th>LotNumber</th>
                                        <th>ExpDate</th>
                                        <th>QtyShipped</th>
                                    </tr>
                                    </thead>
                                    <tbody></tbody>
                                </table>
                            </div>

                        </div>
                        <div role="tabpanel" class="tab-pane fade in active" id="send" aria-labelledby="send-tab">
                            <div class="table-responsive">

                                <table class="table table-hover table-bordered tablesorter" id="outbound_modal_send" style="white-space: nowrap;">
                                    <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>LineNumber</th>
                                        <th>SKU</th>
                                        <th>Keterangan</th>
                                        <th>QtyOrder</th>
                                        <th>StockStatusCode</th>
                                    </tr>
                                    </thead>
                                    <tbody></tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer"></div>
        </div>
    </div>
</div>
<!-- End Modal -->
<!-- Close Modal -->
<div class="modal fade" id="myCloseModal" tabindex="-1" role="dialog" aria-labelledby="myCloseModalLabel" aria-hidden="true">
    <div class="modal-dialog" style="width: 95%">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myCloseModalLabel">Task Close Transfer Keluar : <label id="outbound_transfer_modal_lbl_close"></label> </h4>
            </div>
            <div class="modal-body">
                <p> <span class="glyphicon glyphicon-warning-sign"></span> Opps!! Tampaknya transaksi ini belum bisa diclosing karena ada selisih. mari kita lihat sejenak</p>
                <div class="form-group"></div>
                <div class="row">
                    <div class="col-xs-10 col-md-8">
                        <p>Daftar Barang Transaksi di GBS</p>
                        <div class="table-responsive">
                            <table class="table table-hover table-bordered tablesorter" style="white-space: nowrap;" id="outbound_modal_close">
                                <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Product Code</th>
                                    <th>Product Name</th>
                                    <th>Qty GBS</th>
                                    <th>Qty WMS</th>
                                    <th>Qty Diff</th>
                                    <th>Act</th>
                                </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                        </div>
                    </div>
                    <div class="col-xs-8 col-md-4">
                        <p>Daftar Barang Yang akan diproses</p>
                        <div class="table-responsive">
                            <table class="table table-hover table-bordered tablesorter" style="white-space: nowrap;" id="outbound_modal_close_adj">
                                <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Product Code</th>
                                    <th>Qty</th>
                                    <th>Act</th>
                                </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <form class="form-inline">
                    <div class="form-group">
                        <label for="outbount_close_option">Pilihan Task Closing</label>
                        <select class="form-control" id="outbount_close_option">
                            <option>Option A</option>
                            <option>Option B</option>
                            <option>Option C</option>
                        </select>
                    </div>
                    <div class="form-group"></div>
                    <div class="form-group"></div>
                    <div class="form-group">
                        <label for="outbount_close_option">Customer</label>
                        <select class="form-control" id="outbount_close_customer">
                            <option>Customer A</option>
                            <option>Customer B</option>
                            <option>Customer C</option>
                        </select>
                    </div>
                    <div class="form-group"></div>
                    <div class="form-group"></div>
                    <button class="btn btn-danger" id="outbound_close_batal">Batal</button>
                    <div class="form-group"></div>
                    <button class="btn btn-success"  id="outbound_close_pr">Proses</button>
                </form>
            </div>
        </div>
    </div>
</div>
<!-- End Modal -->

<!-- Form Close Qty-->
<div class="form-group" id="outbound_close_form" style="display: none" title="Input Qty">
    <label for="outbound_close_qty">Qty</label>
    <input type="text" id="outbound_close_qty" class="form-control">
    <button class="btn btn-success">
</div>
<!-- End Form -->

<script type="text/javascript">
    $(function() {
        $("#outbound_tglawal").datepicker({
            dateFormat: 'yy-mm-dd'
        });
        $("#outbound_tglakhir").datepicker({
            dateFormat: 'yy-mm-dd',
            minDate:$("#outbound_tglawal").val()
        });
        $("#outbound_status").val(0);
    });

    $("#outbound_returbeli_table").tablesorter({
        theme : "blue", // this will

        headerTemplate : '{content} {icon}', // new in v2.7. Needed to add the bootstrap icon!

        // widget code contained in the jquery.tablesorter.widgets.js file
        // use the zebra stripe widget if you plan on hiding any rows (filter widget)
        widgets : [ "zebra", "filter", "resizable", "stickyHeaders"],
        headers: { },
        widgetOptions : {
            // using the default zebra striping class name, so it actually isn't included in the theme variable above
            // this is ONLY needed for bootstrap theming if you are using the filter widget, because rows are hidden
            zebra : ["even", "odd"],
            resizable: true,

            // reset filters button
            filter_reset : ".reset",
            filter_formatter : {
                0 : function(){return false;},
                9 : function(){return false;},
                10 : function(){return false;}
            }

            // set the uitheme widget to use the bootstrap theme class names
            // uitheme : "bootstrap"
        }
    });

    $("#updateTableOutbound").click(function(){
        $.blockUI({
            message : "<br><p>" +
                "<img src='./images/loader.gif' width='32px' height='32px'>&nbsp;&nbsp;&nbsp; Mohon Tunggu. . ." +
                "</p>"
        });
        if (($("#outbound_returbeli_table tr").length)>3) {
            $.tablesorter.clearTableBody($("#outbound_returbeli_table")[0]);
        }
        //$("#outbound_loading").dialog({ dialogClass: 'noTitleStuff' });
        //$(".ui-dialog-titlebar").hide();
        $.get("./taskoutbound/outbound_ajax_returbeli.php?isClosed="+$("#outbound_status option:selected").val()+"&tglAwal="+$("#outbound_tglawal").val()+"&tglAkhir="+$("#outbound_tglakhir").val(), function(html) {
            //$("#outbound_loading").dialog("close");
            // append the "ajax'd" data to the table body
            $("#outbound_returbeli_table tbody").append(html);
            // let the plugin know that we made a update
            $("#outbound_returbeli_table").trigger("update");
            $.unblockUI();
        });
        return false;
    });
    $("#outbound_modal_send").tablesorter({
        theme : "blue", // this will

        headerTemplate : '{content} {icon}', // new in v2.7. Needed to add the bootstrap icon!

        // widget code contained in the jquery.tablesorter.widgets.js file
        // use the zebra stripe widget if you plan on hiding any rows (filter widget)
        widgets : [ "zebra", "resizable", "stickyHeaders"],
        headers: { },
        widgetOptions : {
            // using the default zebra striping class name, so it actually isn't included in the theme variable above
            // this is ONLY needed for bootstrap theming if you are using the filter widget, because rows are hidden
            zebra : ["even", "odd"],
            resizable: true

            // set the uitheme widget to use the bootstrap theme class names
            // uitheme : "bootstrap"
        }
    });

    $("#outbound_modal_receipt").tablesorter({
        theme : "blue", // this will

        headerTemplate : '{content} {icon}', // new in v2.7. Needed to add the bootstrap icon!

        // widget code contained in the jquery.tablesorter.widgets.js file
        // use the zebra stripe widget if you plan on hiding any rows (filter widget)
        widgets : [ "zebra", "resizable", "stickyHeaders"],
        headers: { },
        widgetOptions : {
            // using the default zebra striping class name, so it actually isn't included in the theme variable above
            // this is ONLY needed for bootstrap theming if you are using the filter widget, because rows are hidden
            zebra : ["even", "odd"],
            resizable: true

            // set the uitheme widget to use the bootstrap theme class names
            // uitheme : "bootstrap"
        }
    });

    $("#outbound_modal_close").tablesorter({
        theme : "blue", // this will

        headerTemplate : '{content} {icon}', // new in v2.7. Needed to add the bootstrap icon!

        // widget code contained in the jquery.tablesorter.widgets.js file
        // use the zebra stripe widget if you plan on hiding any rows (filter widget)
        widgets : [ "zebra", "resizable", "stickyHeaders"],
        headers: { },
        widgetOptions : {
            // using the default zebra striping class name, so it actually isn't included in the theme variable above
            // this is ONLY needed for bootstrap theming if you are using the filter widget, because rows are hidden
            zebra : ["even", "odd"],
            resizable: true

            // set the uitheme widget to use the bootstrap theme class names
            // uitheme : "bootstrap"
        }
    });

    $("#outbound_modal_close_adj").tablesorter({
        theme : "blue", // this will

        headerTemplate : '{content} {icon}', // new in v2.7. Needed to add the bootstrap icon!

        // widget code contained in the jquery.tablesorter.widgets.js file
        // use the zebra stripe widget if you plan on hiding any rows (filter widget)
        widgets : [ "zebra", "resizable", "stickyHeaders"],
        headers: { },
        widgetOptions : {
            // using the default zebra striping class name, so it actually isn't included in the theme variable above
            // this is ONLY needed for bootstrap theming if you are using the filter widget, because rows are hidden
            zebra : ["even", "odd"],
            resizable: true

            // set the uitheme widget to use the bootstrap theme class names
            // uitheme : "bootstrap"
        }
    });

    // Modal detail Retur Beli
    function showDetail(Kodenota){
        $("#myModal").modal("show");
        var IdSO = Kodenota;
        $("#outbound_returbeli_modal_lbl_so").text(IdSO);

        $.ajax({
            url: "./taskoutbound/outbound_ajax_detail.php?id="+IdSO,
            type: "GET",
            data: {},
            dataType: 'json',
            success: function(data){
                if (($("#outbound_modal_send tr").length)>2) {
                    $.tablesorter.clearTableBody($("#outbound_modal_send")[0]);
                }
                if (($("#outbound_modal_receipt tr").length)>2) {
                    $.tablesorter.clearTableBody($("#outbound_modal_receipt")[0]);
                }
                $(".modal-body #outbound_modal_csend").text(data.csend);
                $(".modal-body #outbound_modal_crcpt").text(data.crcpt);
                var htmlsend = "",htmlrcpt="";
                if(data.send != null){
                    $.each(data.send, function (index, value) {
                        htmlsend += "<tr>"+"<td>"+value.No+"</td>"+"<td>"+value.LineNumber+"</td>"+"<td>"+value.ProductCode+"</td>"+"<td>"+value.Keterangan+"</td>"+"<td>"+value.QtyOrder+"</td>"+"<td>"+value.StockStatusCode+"</td>"+"</tr>";
                    });
                    $(".modal-body #outbound_modal_send tbody").append(htmlsend);
                }
                if(data.rcpt != null){
                    $.each(data.rcpt, function (index, value) {
                        htmlrcpt += "<tr>"+"<td>"+value.No+"</td>"+"<td>"+value.LineNumber+"</td>"+"<td>"+value.ProductCode+"</td>"+"<td>"+value.Keterangan+"</td>"+"<td>"+value.LotNumber+"</td>"+"<td>"+value.ExpDate+"</td>"+"<td>"+value.QtyShipped+"</td>"+"</tr>";
                    });
                    $(".modal-body #outbound_modal_receipt tbody").append(htmlrcpt);
                }
                $("#outbound_modal_send").trigger("update");
                $("#outbound_modal_receipt").trigger("update");
            },
            error:function(){
                alert("Error : Koneksi Server Terputus");
            }
        });
    }
    var masterClose = {
        data: []
    };
    var masterCloseTemp = {
        data: []
    };

    function outboundClose(isReceipt,isClosed,kodenota){
        if((isReceipt==1)&&(isClosed==0)){
            $.ajax({
                url: "./taskoutbound/outbound_ajax_returbeli_close.php?id="+kodenota,
                type: "GET",
                data: {},
                dataType: 'json',
                success: function(data){
                    if (($("#outbound_modal_close tr").length)>2) {
                        $.tablesorter.clearTableBody($("#outbound_modal_close")[0]);
                    }
                    if(data.counter>0){
                        $("#myCloseModal").modal("show");
                        $("#outbound_transfer_modal_lbl_close").text(kodenota);
                        if(data.data != null){
                            var htmldiff ='';
                            $.each(data.data, function (index, value) {
                                masterClose.data.push({
                                    "No" : value.No,
                                    "Brg"  : value.Brg,
                                    "Keterangan" : value.Keterangan,
                                    "QtyGBS" : value.QtyGBS,
                                    "QtyWMS" : value.QtyWMS,
                                    "QtyDiff" : value.QtyDiff
                                });
                                htmldiff += '<tr>'+'<td>'+value.No+'</td>'+'<td>'+value.Brg+'</td>'+'<td>'+value.Keterangan+'</td>'+'<td>'+value.QtyGBS+'</td>'+'<td>'+value.QtyWMS+'</td>'+'<td>'+value.QtyDiff+'</td>'+'<td><button onclick="outbound_close_add(\''+value.Kodenota+'\',\''+value.Brg+'\',\''+value.Keterangan+'\','+value.QtyDiff+')"><span class="glyphicon glyphicon-plus-sign"></span></button></td></tr>';
                            });
                            $(".modal-body #outbound_modal_close tbody").append(htmldiff);
                        }
                    }
                    $("#outbound_modal_close").trigger("update");
                },
                error:function(){
                    alert("Error : Koneksi Server Terputus");
                }
            });
        }else{
            new PNotify({
                title :'Info',
                text : 'Tidak ada transaksi yang bisa close',
                type : 'error'
            });
        }
    }

    function setQtyClose(Brg, Jml) {
        for (var i=0; i<(masterClose.data).length; i++) {
            if (masterClose.data[i].Brg === Brg) {
                masterClose.data[i].QtyDiff = Jml;
                if (($("#outbound_modal_close tr").length)>2) {
                    $.tablesorter.clearTableBody($("#outbound_modal_close")[0]);
                }
                var htmldiff ='';
                var i =0;
                $.each(masterClose.data, function (index, value) {
                    i++;
                    htmldiff += '<tr>'+'<td>'+i+'</td>'+'<td>'+value.Brg+'</td>'+'<td>'+value.Keterangan+'</td>'+'<td>'+value.QtyGBS+'</td>'+'<td>'+value.QtyWMS+'</td>'+'<td>'+value.QtyDiff+'</td>'+'<td><button onclick="outbound_close_add(\''+value.Kodenota+'\',\''+value.Brg+'\',\''+value.Keterangan+'\','+value.QtyDiff+')"><span class="glyphicon glyphicon-plus-sign"></span></button></td></tr>';
                });
                $(".modal-body #outbound_modal_close tbody").append(htmldiff);
                $("#outbound_modal_close").trigger("update");
            }
        }
    }

    function setQtyCloseAdd(Brg, Jml) {
        for (var i=0; i<(masterClose.data).length; i++) {
            if (masterClose.data[i].Brg === Brg) {
                var a = parseInt(masterClose.data[i].QtyDiff)+Jml;
                masterClose.data[i].QtyDiff = a;
                if (($("#outbound_modal_close tr").length)>2) {
                    $.tablesorter.clearTableBody($("#outbound_modal_close")[0]);
                }
                var htmldiff ='';
                var i =0;
                $.each(masterClose.data, function (index, value) {
                    i++;
                    htmldiff += '<tr>'+'<td>'+i+'</td>'+'<td>'+value.Brg+'</td>'+'<td>'+value.Keterangan+'</td>'+'<td>'+value.QtyGBS+'</td>'+'<td>'+value.QtyWMS+'</td>'+'<td>'+value.QtyDiff+'</td>'+'<td><button onclick="outbound_close_add(\''+value.Kodenota+'\',\''+value.Brg+'\',\''+value.Keterangan+'\','+value.QtyDiff+')"><span class="glyphicon glyphicon-plus-sign"></span></button></td></tr>';
                });
                $(".modal-body #outbound_modal_close tbody").append(htmldiff);
                $("#outbound_modal_close").trigger("update");
            }
        }
    }

    function outbound_close_add(kodenota,brg,keterangan,qty){
        var person = prompt("Masukkan Jumlah untuk : "+keterangan+"/"+qty+" pcs", "0");
        if (person != null) {
            person = parseInt(person);
            if(((qty-person)>=0)&&(person>0)&&(parseInt(qty)>0)){
                setQtyClose(brg,(qty-person));
                outbound_close_add_mastertemp(kodenota,brg,keterangan,person);
            }else{
                alert("Jumlah yang anda masukkan tidak boleh nol dan melebihi jumlah barang!!");
            }
        }
    }

    function outbound_close_minus(kodenota,brg,keterangan,qty){
        var person = prompt("Masukkan Jumlah untuk : "+keterangan+"/"+qty+" pcs", "0");
        if (person != null) {
            person = parseInt(person);
            if(((qty-person)>=0)&&(person>0)&&(parseInt(qty)>0)){
                setQtyCloseAdd(brg,person);
                outbound_close_minus_mastertemp(kodenota,brg,keterangan,person);
            }else{
                alert("Jumlah yang anda masukkan tidak boleh nol dan melebihi jumlah barang!!");
            }
        }
    }
    function outbound_close_add_mastertemp(kodenota,brg,keterangan,qty){
        var isSame = 0;
        for (var i=0; i<(masterCloseTemp.data).length; i++) {
            if (masterCloseTemp.data[i].Brg === brg) {
                var a = masterCloseTemp.data[i].Jml + parseInt(qty);
                masterCloseTemp.data[i].Jml = a;
                isSame = 1;
            }
        }

        if(isSame==0){
            masterCloseTemp.data.push({
                "Kodenota"  : kodenota,
                "Brg"  : brg,
                "Keterangan" : keterangan,
                "Jml" : qty
            });
        }

        if (($("#outbound_modal_close_adj tr").length)>2) {
            $.tablesorter.clearTableBody($("#outbound_modal_close_adj")[0]);
        }
        var htmldiff ='';
        var i = 0;
        $.each(masterCloseTemp.data, function (index, value) {
            i++;
            if(parseInt(value.Jml)>0){
                htmldiff += '<tr>'+'<td>'+i+'</td>'+'<td>'+value.Brg+'</td>'+'<td>'+value.Jml+'</td>'+'<td><button onclick="outbound_close_minus(\''+value.Kodenota+'\',\''+value.Brg+'\',\''+value.Keterangan+'\','+value.Jml+')"><span class="glyphicon glyphicon-minus-sign"></span></button></td></tr>';
            }
        });
        $(".modal-body #outbound_modal_close_adj tbody").append(htmldiff);
        $("#outbound_modal_close_adj").trigger("update");
    }

    function outbound_close_minus_mastertemp(kodenota,brg,keterangan,qty){
        for (var i=0; i<(masterCloseTemp.data).length; i++) {
            if (masterCloseTemp.data[i].Brg === brg) {
                var a = masterCloseTemp.data[i].Jml - parseInt(qty);
                masterCloseTemp.data[i].Jml = a;
            }
        }

        if (($("#outbound_modal_close_adj tr").length)>2) {
            $.tablesorter.clearTableBody($("#outbound_modal_close_adj")[0]);
        }
        var htmldiff ='';
        var i = 0;
        $.each(masterCloseTemp.data, function (index, value) {
            i++;
            if(parseInt(value.Jml)>0){
                htmldiff += '<tr>'+'<td>'+i+'</td>'+'<td>'+value.Brg+'</td>'+'<td>'+value.Jml+'</td>'+'<td><button onclick="outbound_close_minus(\''+value.Kodenota+'\',\''+value.Brg+'\',\''+value.Keterangan+'\','+value.Jml+')"><span class="glyphicon glyphicon-minus-sign"></span></button></td></tr>';
            }
        });
        $(".modal-body #outbound_modal_close_adj tbody").append(htmldiff);
        $("#outbound_modal_close_adj").trigger("update");
    }
</script>