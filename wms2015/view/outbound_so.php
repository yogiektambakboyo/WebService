<?php
session_start();
include_once "../taskoutbound/outbound_gettgltransaksi.php";
include_once "../script/connectbon.php";
?>
<div class="header">
    <h1 class="page-title">Task Outbound - Sales Order</h1>
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
                <input type="text" class="form-control" id="outbound_tglawal" name="outbound_tglawal" value="<?php echo $_SESSION["TglTransaksi"];?>">
            </div>
            <div class="form-group"></div>
            <div class="form-group">
                <label for="outbound_tglakhir">Tgl Akhir :</label>
                <input type="text" class="form-control" id="outbound_tglakhir" name="outbound_tglakhir" value="<?php echo $_SESSION["TglTransaksi"];?>">
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
            <div class="form-group"></div>
            <div class="form-group"></div>
            <div class="form-group"></div>
            <button type="button" class="btn btn-success" id="outbound_so_submit"><span class="glyphicon glyphicon-saved" aria-hidden="true"></span>  Close Task</button>
        </form>
    </div>
    <br>
    <div class="scrollable-area" style="max-height:350px;overflow:scroll;font-size: 12px;">
        <div class="row" id="outbound_so">
            <div class="table-responsive">
                <table class="table table-hover table-bordered tablesorter" style="white-space: nowrap;margin-left: 2%;" id="outbound_so_table">
                    <thead>
                    <tr>
                        <th>No</th>
                        <th>No. SO</th>
                        <th>Tanggal</th>
                        <th>Tipe Transaksi</th>
                        <th>Keterangan</th>
                        <th>isReceipt</th>
                        <th>isClosed</th>
                        <th>Sopir</th>
                        <th>DispatchNo</th>
                        <th>Action</th>
                    </tr>
                    </thead>
                    <tbody>
                        <?php
                            $sql = "select Kodenota,CONVERT(CHAR(10), Tgl, 120) as Tgl,TransactionType,Keterangan,isHaveRcpt,isClosed,DriverId,DispatchNo from dbo.OutBound_Master WHERE isClosed=:isClose and (Tgl between :tglAwal and :tglAkhir)  and TransactionType='SalesOrder' and LEFT(Kodenota,5)=:cabang order by tgl";
                            $sth = $dbh->prepare($sql);
                            $isClosed = 0;
                            $tglAwal = $_SESSION["TglTransaksi"];
                            $tglAkhir = $_SESSION["TglTransaksi"];
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
                                    echo '<td><a href="#" onclick="showDetail(\''.$row['Kodenota'].'\')" id="outbound_so_modal"><span class="glyphicon glyphicon-eye-open"></span> Detail</a></td>';
                                echo '</tr>';
                            }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Dialog konfirmasi task close -->
<div id="outbound_so_confirm" style="display:none; cursor: default" title="Konfirmasi Task Sales Order">
    <p>Apakah anda yakin akan memproses transaksi ini?</p>
</div>

<!-- Modal -->
<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">Detail Sales Order : <label id="outbound_so_modal_lbl_so"></label> </h4>
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

<script type="text/javascript">
    $(function() {
        $("#outbound_tglawal").datepicker({
            dateFormat: 'yy-mm-dd'
        });
        $("#outbound_tglawal").change(function () {
            $("#outbound_tglakhir").datepicker({
                minDate:$("#outbound_tglawal").val()
            });
        });
        $("#outbound_tglakhir").datepicker({
            dateFormat: 'yy-mm-dd',
            minDate:$("#outbound_tglawal").val()
        });
        $("#outbound_status").val(0);
    });

    $("#outbound_so_table").tablesorter({
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
                    9 : function(){return false;}
                }

                // set the uitheme widget to use the bootstrap theme class names
                // uitheme : "bootstrap"
            }
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

    $("#updateTableOutbound").click(function(){
        $.blockUI({
            message : "<br><p>" +
                "<img src='./images/loader.gif' width='32px' height='32px'>&nbsp;&nbsp;&nbsp; Mohon Tunggu. . ." +
                "</p>"
        });
        if (($("#outbound_so_table tr").length)>3) {
            $.tablesorter.clearTableBody($("#outbound_so_table")[0]);
        }
        //$("#outbound_loading").dialog({ dialogClass: 'noTitleStuff' });
        //$(".ui-dialog-titlebar").hide();
        $.get("./taskoutbound/outbound_ajax_so.php?isClosed="+$("#outbound_status option:selected").val()+"&tglAwal="+$("#outbound_tglawal").val()+"&tglAkhir="+$("#outbound_tglakhir").val(), function(html) {
            //$("#outbound_loading").dialog("close");
            // append the "ajax'd" data to the table body
            $("#outbound_so_table tbody").append(html);
            // let the plugin know that we made a update
            $("#outbound_so_table").trigger("update");
            $.unblockUI();
        });
        return false;
    });
    $("#outbound_so_submit").click(function() {
        $( "#outbound_so_confirm" ).dialog({
            resizable: false,
            modal: true,
            buttons : [
                { text: "Ya", click: function () {
                    $( "#outbound_so_confirm" ).dialog("close");
                    $.ajax({
                        url:"./taskoutbound/outbound_ajax_so_close.php?tglAwal="+$("#outbound_tglawal").val()+"&tglAkhir="+$("#outbound_tglakhir").val(),
                        type:"GET",
                        data :{},
                        dataType:'string',
                        success : function(data){
                            alert("Berhasil");
                        },
                        error:function(){
                            alert("Error : Gagal Close Sales Order");
                        }
                    });
                }, class:"btn btn-success"},
                { text: "Tidak", click: function () { $( "#outbound_so_confirm" ).dialog("close"); }, class:"btn btn-danger"}
            ]
        });
    });

    function showDetail(Kodenota){
        $("#myModal").modal("show");
        var IdSO = Kodenota;
        $("#outbound_so_modal_lbl_so").text(IdSO);

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
</script>