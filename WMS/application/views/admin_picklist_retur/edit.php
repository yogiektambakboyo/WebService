<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <title>Edit Pick List Retur</title>
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
                    <h1>Edit Pick List Retur</h1>
                    <hr />
                    <?php
                    if ($this->session->flashdata('success')) {
                        ?>
                        <div class="alert alert-success">
                            <strong>
                                <?php
                                echo $this->session->flashdata('success');
                                ?>
                            </strong>
                        </div>
                        <?php
                    };
                   
					echo validation_errors('<div class="alert alert-error"><h5>', '</h5></div>') ;
					?>
					<?php //var_dump($cabang); ?>
					<form class="form-horizontal" method="post">
						<table>
							<tbody>
								<tr>
									<td><strong>Cabang :</strong></td>
									<td>
										<select name="cabang" >
											<?php  
												
												foreach ($cabang as $cabang) { ?>
												<option <?php if($cabang['Cabang'] == $this->input->post('cabang') ) { ?> 
												selected="<?php echo $cabang['Cabang']; ?>" 
												<?php } ?> 
												value="<?php echo $cabang['Cabang']; ?>"><?php echo $cabang['NamaCabang']; ?></option>
											<?php } ?>
										</select>									
									 </td>
									<td align="right"><input type="submit" name="btnTampil" value="Tampilkan" class="btn btn-medium" ></td>
								<tr>
							</tbody>
						</table>
					</form>
					<table class="tablesorter">
						<thead>
							<tr>
								<th>No</th>
								<th>No Pick List</th>
								<th>Jumlah Retur</th>
								<th>Jumlah Tolakan</th>
								<th>Tanggal</th>
								<th></th>
							</tr>
						</thead>
						<tfoot>
							<tr>
								<th>No</th>
								<th>No Pick List</th>
								<th>Jumlah Retur</th>
								<th>Jumlah Tolakan</th>
								<th>Tanggal</th>
								<th></th>
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
								$i = 1;
								foreach ($edit as $row) {
									?>
									<tr>
										<td width="50px"><?php echo $i++ ?></td>
										<td><?php echo $row['NoPicklist'] ?></td>
										<td><?php echo $row['Retur'] ?></td>
										<td><?php echo $row['Tolakan'] ?></td>
										<td><?php echo date("d-m-Y",strtotime($row['tglPicklist'])) ?></td>
										
										<td style="text-align: center" width="50px">
											<a href="<?php echo base_url() ?>index.php/admin_picklist_retur/edit/<?php echo str_replace('=', '-', str_replace('/', '_', base64_encode($row['NoPicklist']))) ?>" class="btn btn-info">Edit</a>
										</td>
									</tr>
									<?php
								} 
							?>
						</tbody>
					</table>
                </div><!-- /.span4 -->
            </div><!-- /.row -->
        </div><!-- /.container -->
        <?php
        $this->load->view("include/footer");
        ?>
		<script type="text/javascript">
		
			$('tr').click(function(event) {
                if (event.target.type !== 'checkbox') {
                    $(':checkbox', this).trigger('click');
                }
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
                        // 4: { sorter: false }
                    },
                    widgetOptions : {
                        // using the default zebra striping class name, so it actually isn't included in the theme variable above
                        // this is ONLY needed for bootstrap theming if you are using the filter widget, because rows are hidden
                        zebra : ["even", "odd"],

                        // reset filters button
                        filter_reset : ".reset",
                        filter_formatter : {
                            // 4 : function(){return false;}
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
