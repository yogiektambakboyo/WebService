<!--
To change this template, choose Tools | Templates
and open the template in the editor.
-->
<!DOCTYPE html>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Ambil Bin</title>
        <?php $this->load->view('include/header'); ?>
    </head>
    <body> 
        <div class="container">
            <div class="row">
                <div class="span12 center">
                    <h1>All Outstanding</h1>
                    <hr />
                    <div class="container">
                        <div class="row">
                            <div class='span12 center'>
                            <?php
                            if ($this->session->flashdata('succes')){
                                ?>
                                <div class="alert alert-success">
                                    <strong><?php echo $this->session->flashdata('succes'); ?></strong>
                                </div>
                            <?php
                            }
                            ?>
							<?php 
                            //var_dump($keterangan);
								echo validation_errors();
								if (isset($error)){
									echo $error;
								}
								 if ($this->session->flashdata('error')){
										?>
										<div class="alert alert-error">
											<strong><?php echo $this->session->flashdata('error'); ?></strong>
										</div>
									<?php
									
								}
							?>
                            </div>
                        </div>
                    </div>
                 
                        <table class="tablesorter">
						<thead>
                            <tr>
                                <th>Transaction Code</th>
                                <th>Nama Barang</th>
                                <th>Bin Awal</th>
                                <th>Rack Awal</th>
                                <th>Rack Tujuan</th>
                            </tr>
						</thead>
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
						<tbody>
                          
                                <?php 
								
                                    $i = 0;
									
                                    foreach ($ambilBin as $row){
									// var_dump($row);
									if (!empty($row)) :
                                        ?>
									
                                        <tr onclick="document.getElementById('myForm<?php echo $i?>').submit();">
											<td><?php echo $row['TransactionCode'] ?></td>
                                            <td><?php echo $row['Keterangan'] ?></td>
                                            <td><?php echo $row['BinCode'] ?></td>
                                            <td><?php echo $row['SrcRackName'] ?></td>
                                            <td><?php echo $row['DestRackName'] ?></td>
										<form class="form-horizontal" method="post" action="" id="myForm<?php echo $i ?>">
											<input type="hidden" name="trCode" value="<?php echo $row['TransactionCode']?>" />
											<input type="hidden" name="ket" value="<?php echo $row['Keterangan']?>" />
											<input type="hidden" name="sku" value="<?php echo $row['SKUCode']?>" />
											<input type="hidden" name="binAwal" value="<?php echo $row['BinCode']?>" />
											<input type="hidden" name="binTjn" value="<?php echo $row['DestBin']?>" />
                                                                                        <input type="hidden" name="rackAwalKode" value="<?php echo $row['SrcRackSlot']?>" />
											<input type="hidden" name="rackAwal" value="<?php echo $row['SrcRackName']?>" />
											<input type="hidden" name="rackTjnNama" value="<?php echo $row['DestRackName']?>" />
											<input type="hidden" name="rackTjnKode" value="<?php echo $row['DestRackSlot']?>" />
											<input type="hidden" name="qtyKonv" value="<?php echo $row['QtyKonversi']?>" />
											<input type="hidden" name="qty" value="<?php echo $row['QtyNeedNow']?>" />
											<input type="hidden" name="NoUrut" value="<?php echo $row['NoUrut']?>" />
											<input type="hidden" name="QueueNumber" value="<?php echo $row['QueueNumber']?>" />
										</form>
                                        </tr> 
									
                                <?php
                                    $i++;
									endif;
                                    }
                                ?>
                            
						</tbody>
                        </table>
                    
                    <div class="container">
                        <div class="row">
                            <div class="span12 center">
                                <p><a href="<?php echo base_url() ?>index.php/replenish/my_outstanding" class="btn btn-large">My Outstanding</a></p>
                                <p><a href="<?php echo base_url() ?>index.php/replenish" class="btn btn-inverse btn-large">Kembali</a></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php
        // put your code here
        $this->load->view('include/footer');
        ?>
    </body>
		<script type="text/javascript">
          $('tr').click(
            function() {
                $('input[type=radio]',this).prop('checked', true);
            }
        );   
			
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
                        5: { sorter: false }
                    },
                    widgetOptions : {
                        // using the default zebra striping class name, so it actually isn't included in the theme variable above
                        // this is ONLY needed for bootstrap theming if you are using the filter widget, because rows are hidden
                        zebra : ["even", "odd"],

                        // reset filters button
                        filter_reset : ".reset",
                        filter_formatter : {
                            5 : function(){return false;}
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
</html>
