<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <title>Print Barcode BPB</title>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="description" content="">
        <meta name="author" content="">
        <?php
        $this->load->view('include/header');
        ?>
    </head>

    <body>
        <div class="container">
            <div class="row">
                <div class="span12 center">
                    <h1>Print Barcode BPB</h1>
                    <hr />
					<?php
                    echo validation_errors();
                    if (isset($error)) {
                        echo "<div class='alert alert-error'><strong>" . $error . "</strong><br /></div>";
                    }
                    if (isset($succses)) {
                        ?>
                        <div class="alert alert-success">
                            <strong>
                                <?php
                                echo $succses;
                                ?>
                            </strong>
                        </div>
                        <?php
                    }
                    ?>
                    <table class="tablesorter">
                        <thead>
                            <tr>
                                <th></th>
                                <th>Kode Nota</th>
                                <th>Supplier</th>
                                <th>Tanggal</th>
                                <th>Keterangan</th>
								<th>
                            </tr>
                        </thead>
                        <tfoot>
                            <tr>
                                <th></th>
                                <th>Kode Nota</th>
                                <th>Supplier</th>
                                <th>Tanggal</th>
                                <th>Keterangan</th>
								<th>
                            </tr>
                            <tr>
                                <th colspan="9" class="pager form-horizontal" style="text-align: center">
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
                            foreach ($bpb as $row) {
                                ?>
                                <tr>
                                    <td><input type="radio" name="kodeNota" id="kodeNota" value="<?php echo $row['KodeNota'] ?>"  /></td>
                                    <td width="300px"><?php echo $row['KodeNota'] ?></td>
                                    <td><?php echo $row['perusahaan'] ?></td>
                                    <td><?php echo date("d-m-Y", strtotime($row['Tgl'])) ?></td>
									<td><?php echo $row['keterangan']?></td>
									<td><button onclick="fungsi(<?php echo "'".$row['KodeNota']."','".$row['keterangan']."'" ?>)" class="btn-small btn-info"><i class="icon-white icon-print"></i></button></td>
                                </tr>
                                <?php
                            }
                            ?>
                        </tbody>
                    </table>
					<div id="popup">
						<form method="post" action="" >
							<legend>Print Barcode BPB</legend>
							<label>Note :</label>
							<textarea rows="2" cols="100" name="keterangan" id="keterangan" value="<?php echo $row['keterangan']?>" style="width: 326px; height: 45px;" /></textarea>
							<label>Jumlah :</label>
							<input type="text" name="jmlPrint" id="jml" class="input-mini"/> Lembar
							<input type="hidden" name="kodeNota" id="KodeNota" value="KodeNota"/></td>
							<div class="control-group">
								<input type="submit" name="btnPrint" class="btn-small btn-primary" value="Print" />
								<div class="controls">

							</div>
							</div>
						</form>
					</div>
                </div><!-- /.span4 -->
            </div><!-- /.row -->
        </div><!-- /.container -->
        <?php
        $this->load->view("include/footer");
        ?>
        <script type="text/javascript">
		
		// $('input:radio[name="kodeNota"]').change(
		// function(){
			// $("input:radio[name=kodeNota]").click(function() {
			// var value = $(this).val();
			// alert(value);
				// if ($(this).is(':checked') && $(this).val() == value) {
					// $('#keterangan').attr('disabled', true)
				// }
			// });	
		// });
		
		$('#popup').hide();
		
		function fungsi(KodeNota,keterangan) {
                $('#KodeNota').val(KodeNota);
                $('#keterangan').val(keterangan);
                $( "#popup" ).dialog({ 
                    height: 330,
                    width: 400,
                    modal: true});
            };
		
		$('tr').click(
            function() {
                $('input[type=radio]',this).prop('checked', true);
				// $('tbody #keterangan').attr('readonly',true);
				// $('tbody #jml').attr('readonly',true);
				// $(this).find('#keterangan').attr('readonly',false);
				// $(this).find('#jml').attr('readonly',false);
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
    </body>
</html>
