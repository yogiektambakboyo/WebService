<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <title>Daftar Outstanding Storing User</title>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="description" content="">
        <meta name="author" content="">
        <?php
        $this->load->view('include/header');
        ?>
        <link href="<?php echo base_url() ?>files/css/ui-lightness/jquery-ui-1.10.3.custom.min.css" rel="stylesheet" />
    </head>

    <body>
        <div class="container">
            <div class="row">
                <div class="span12 center">
                    <h1>Daftar Outstanding Storing User</h1>
                    <hr />
                    <div class="control-group">
                        <table id="tablepicklist">
                            <thead>
                                <tr>
                                    <th></th>
                                    <th>Kode Nota</th>
                                    <th>Kode Bin</th>
                                    <th>Nama Barang</th>
                                    <th>Rak Sekarang</th>
                                    <th>Jumlah</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $i = 0;
                                foreach ($outstanding as $row) {
                                    ?>
                                    <tr>
                                        <td><input type="radio" name="SKUpick" value="<?php echo $row['SKUCode'] . '~' . $row['TransactionCode'] . '~' .$row['NoUrut']. '~' . $i ?>" /></td>
                                        <td><?php echo $row['ERPCode'] ?></td>
                                        <td><?php echo $row['BinCode'] ?></td>
                                        <td><?php echo $row['Keterangan'] ?></td>
                                        <td><?php echo $row['CurrRackSlot'] ?>
                                            <div id="dialogform<?php echo $i; ?>" style="display:none" title="SKU : <?php echo $row['SKUCode'] ?>" >
                                                <p><strong><?php echo $row['Keterangan'] ?></strong></p>
                                                <form method="POST" action="<?php echo base_url() ?>index.php/storing/taruh_bin">
                                                    <input type="hidden" name="TransactionCode" value="<?php echo $row['TransactionCode'] ?>" />
                                                    <input type="hidden" name="QueueNumber" value="<?php echo $row['QueueNumber'] ?>" />
                                                    <input type="hidden" name="NoUrut" value="<?php echo $row['NoUrut'] ?>" />
                                                    <input type="hidden" name="Keterangan" value="<?php echo $row['Keterangan'] ?>" />
                                                    <input type="hidden" name="ERPCode" value="<?php echo $row['ERPCode'] ?>" />
                                                    <input type="hidden" name="BinCode" value="<?php echo $row['BinCode'] ?>" />
                                                    <input type="hidden" name="Ratio" value="<?php echo $row['Ratio'] ?>" />
                                                    <input type="hidden" name="RatioName" value="<?php echo $row['RatioName'] ?>" />
                                                    <input type="hidden" name="DestRackSlot" value="<?php echo $row['DestRackSlot'] ?>" />
                                                    <input type="hidden" name="SKUCode" value="<?php echo $row['SKUCode'] ?>" />
                                                    <input type="hidden" name="Qty" value="<?php echo $row['Qty'] ?>" />
                                                    <input type="submit" name="btnProses" value="Proses" class="btn" />
                                                </form>
                                                <table class="table table-bordered table-hover table-striped table-condensed">
                                                    <thead>
                                                    <th>Nama Rack</th>
                                                    <th>Level Rack</th>
                                                    </thead>
                                                    <tbody>
                                                    </tbody>
                                                </table>
                                                
                                            </div>
                                        </td>
                                        <td><?php echo $row['Qtykonversi'] ?></td>
                                    </tr>
                                    <?php
                                    $i++;
                                }
                                ?>
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th colspan="6" class="pager form-horizontal" style="text-align: center">
                                        <button class="btn first"><i class="icon-step-backward"></i></button>
                                        <button class="btn prev"><i class="icon-arrow-left"></i></button>
                                        <span class="pagedisplay"></span> <!-- this can be any element, including an input -->
                                        <button class="btn next"><i class="icon-arrow-right"></i></button>
                                        <button class="btn last"><i class="icon-step-forward"></i></button>
                                        <select class="pagesize input-mini" title="Select page size">
                                            <option selected="selected" value="10">10</option>
                                            <option value="20">20</option>
                                            <option value="30">30</option>
                                            <option value="40">40</option>
                                        </select>
                                        <select class="pagenum input-mini" title="Select page number"></select>
                                    </th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                    <div class="container">
                        <div class="row">
                            <div class="span12 center">
                                <input type="hidden" id="refreshed" value="no">
                                <?php
                                if (!$this->session->userdata("outstanding")) {
                                    ?>
                                    <p><a href="<?php echo base_url() ?>index.php/storing/back_tambah_bpb_storing" class="btn btn-large btn-inverse">Kembali</a></p>
                                    <?php
                                }
                                ?>
                            </div><!-- /.span4 -->
                        </div><!-- /.row -->
                    </div><!-- /.container -->

                </div><!-- /.span4 -->
            </div><!-- /.row -->
        </div><!-- /.container -->
        <?php
        $this->load->view("include/footer");
        ?>
        <script type="text/javascript">
            $('#tablepicklist tbody tr').click(
            function() {
                $('input[type=radio]',this).prop('checked', true);
                var temp=$('input[type=radio]',this).val();
                var arr = temp.split('~');
                var SKUCode=arr[0];
                var TransactionCode=arr[1];
                var NoUrut=arr[2];
                var index=arr[3];
                var postdata = {'SKUCode':SKUCode,'TransactionCode':TransactionCode,'NoUrut':NoUrut};
                var link = "<?php echo base_url() ?>index.php/storing/get_ajax_suggestion";
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
                            str+= '<tr>';
                            str+= '<td>'+item['RackName']+'</td>';
                            str+= '<td>'+item['RackLevel']+'</td>';
                        });
                        $("#dialogform"+index+" table tbody").html(str); 
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
                $( "#dialogform"+index ).dialog({
                    modal: true,
                    open: function() {
                        $('.ui-widget-overlay').bind('click', function() {
                            $("#dialogform"+index).dialog('close');
                        })
                    }
                });
            });
            $(function() {

                $.extend($.tablesorter.themes.bootstrap, {
                    // these classes are added to the table. To see other table classes available,
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

                    headerTemplate : '{content} {icon}', // new in v2.7. Needed to add the bootstrap icon!

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

            });
        </script>
        <script src="<?php echo base_url() ?>files/js/jquery-ui-1.10.3.custom.min.js"></script>
    </body>
</html>


