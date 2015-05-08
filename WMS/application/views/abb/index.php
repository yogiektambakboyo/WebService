<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <title>Daftar Semua Outstanding Picking</title>
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
                    <h1>Daftar Semua Barang Berlebih</h1>
                    <hr />
                    <?php
                    if (isset($error)) {
                        echo "<div class='alert alert-error'><strong>" . $error . "</strong><br /></div>";
                    }
                    ?>
                    <div class="control-group">
                        <!-- div class="scroll2" -->
                        <table class="table table-bordered table-hover table-striped table-condensed tablesorter" id="tablepicklist">
                            <thead>
                                <tr>
                                    <th></th>
									<th>ERP Code</th>
                                    <th>Sku Code</th>
									<th>Nama Barang</th>
                                    <th>Jumlah Diambil</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $i = 0;
                                foreach ($listAbb as $row) {
                                    ?>

                                    <tr>
                                        <td style="text-align: center"><input type="radio" name="SKUpick" value="<?php echo $row['TransactionCode'] . '~' .$row['SKUCode']. '~' . $row['Qty'] . '~' . $i ?>" /></td>
                                        <td><?php echo $row['ERPCode'] ?></td>
                                        <td><?php echo $row['SKUCode'] ?></td>
                                        <td><?php echo $row['Keterangan'] ?></td>
                                        <td><?php echo $row['selisihKonv'] ?>
                                        
                                            <div id="dialogform<?php echo $i; ?>" style="display:none" title="ERP Code : <?php echo $row['ERPCode'] ?>" >
                                                <p>Barang : <strong><?php echo $row['Keterangan'] ?></strong></p>
                                                <p>Qty : <strong><?php echo $row['selisihKonv'] ?></strong></p>
                                                <table class="table table-bordered table-hover table-striped table-condensed">
                                                    <thead>
                                                    <th>Rack Slot</th>
                                                    <th>Level</th>
                                                    <th>Exp Date</th>
                                                    <th>Qty</th>
                                                    </thead>
                                                    <tbody>
													 <span id="ajaxLoader<?php echo $i; ?>" style="display:none"><img src="<?php echo base_url() ?>files/css/ui-lightness/images/ajax-loader2.gif"></span>
                                                    </tbody>
                                                </table>
                                                <form method="POST" action="<?php echo base_url() ?>index.php/abb/ambil_barang" >
                                                    <input type="hidden" name="TransactionCode" value="<?php echo $row['TransactionCode'] ?>" />
                                                    <input type="hidden" name="SKUCode" value="<?php echo $row['SKUCode'] ?>" />
                                                    <input type="hidden" name="Qty" value="<?php echo $row['Qty'] ?>" />
                                                    <input type="hidden" name="Erp" value="<?php echo $row['ERPCode'] ?>" />
                                                    <input type="hidden" name="binSrc" value="<?php echo $row['BinCode'] ?>" />
                                                    <input type="hidden" name="rackSrc" value="<?php echo $row['DestRackSlot'] ?>" />
                                                    <input type="hidden" name="qtyKonv" value="<?php echo $row['selisihKonv'] ?>" />
                                                    <input type="hidden" name="ExpDate" value="<?php echo $row['ExpDate'] ?>" />
													<?php //$noUrut = $row['NoUrut'] + 1 ?>
                                                    <input type="hidden" name="noUrut" value="<?php //echo $noUrut ?>" />
                                                     <input type="submit" name="btnProsesIndex" class="btn btn-small" value="Proses" />
                                                </form>
                                            </div>
                                        </td>

                                    </tr>

                                    <?php
                                    $i++;
                                }
                                ?>
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th colspan="5" class="pager form-horizontal" style="text-align: center">
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
                        <!--/div -->
                        <input type="hidden" id="refreshed" value="no">
                    </div>
                    <div class="container">
                        <div class="row">
                            <div class="span12 center">
                                <p><a href="<?php echo base_url() ?>index.php/main" class="btn btn-inverse btn-large">Kembali</a></p>
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
				var TransactionCode=arr[0];
                var SKUCode=arr[1];
                var Qty=arr[2];
                var index=arr[3];
                var postdata = {'TransactionCode':TransactionCode,'SKUCode':SKUCode,'Qty':Qty};
                var link = "<?php echo base_url() ?>index.php/abb/get_ajax_suggestion";
				$("#ajaxLoader"+index).show();
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
                            str+= '<td>'+item['name']+'</td>';
                            str+= '<td>'+item['level']+'</td>';
                            str+= '<td>'+item['ExpDate']+'</td>';
                            str+= '<td>'+item['Qty']+'</td></tr>';
                        });
						$("#ajaxLoader"+index).hide();
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
					height: 250,
                    width: 330,
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
                $("#tablepicklist").tablesorter({
                    theme : "bootstrap", // this will 

                    widthFixed: true,

                    headerTemplate : '{content} {icon}', // new in v2.7. Needed to add the bootstrap icon!

                    // widget code contained in the jquery.tablesorter.widgets.js file
                    // use the zebra stripe widget if you plan on hiding any rows (filter widget)
                    widgets : [ "uitheme", "filter", "zebra" ],
                    headers: {
                        0: { sorter: false }
                    },
                    widgetOptions : {
                        // using the default zebra striping class name, so it actually isn't included in the theme variable above
                        // this is ONLY needed for bootstrap theming if you are using the filter widget, because rows are hidden
                        zebra : ["even", "odd"],

                        // reset filters button
                        filter_reset : ".reset",
                        filter_formatter : {
                            0 : function(){return false;}
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


