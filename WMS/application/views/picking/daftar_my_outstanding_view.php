<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <title>Daftar Outstanding Picking Belum Taruh</title>
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
                    <h1>Daftar Outstanding Picking Belum Taruh</h1>
                    <hr />
                    <?php
                    if (isset($error)) {
                        echo "<div class='alert alert-error'><strong>" . $error . "</strong><br /></div>";
                    }
                    ?>
                    <strong>No. PickList</strong> : <input type="text" readonly="readonly" class="input-large" placeholder="PickList" value="<?php echo $this->session->userdata('ERPCode'); ?>" />
                    <br />
                    <strong>Bin Sekarang</strong> : <input type="text" readonly="readonly" class="input-large" placeholder="Kode Bin Sekarang" value="<?php echo $this->session->userdata('BinNow'); ?>" />
                    <br />
                    <div class="control-group">
                        <table class="table table-bordered table-hover table-striped tablesorter">
                            <thead>
                                <tr>
                                    <th>SKU</th>
                                    <th>Nama Barang</th>
                                    <th>Asal Rack</th>
                                    <th>Asal Bin</th>
                                    <th>Temp Bin</th>
                                    <th>Jumlah</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                foreach ($outstanding as $row) {
                                    ?>
                                    <tr>
                                        <td><?php echo $row['SKUCode'] ?></td>
                                        <td><?php echo $row['Keterangan'] ?></td>
                                        <td><?php echo $row['Name'] ?></td>
                                        <td><?php echo $row['SrcBin'] ?></td>
                                        <td><?php echo $row['DestBin'] ?></td>
                                        <td><?php echo $row['Qty'] ?></td>
                                    </tr>
                                    <?php
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
                                <p><a href="<?php echo base_url() ?>index.php/picking/list_my_outstanding" class="btn btn-large btn-info"><i class="icon-white icon-refresh"></i> Refresh</a></p>
                                <p><a href="<?php echo base_url() ?>index.php/picking/taruh_bin" class="btn btn-large">Taruh Bin</a></p>
                                <p><a href="<?php echo base_url() ?>index.php/picking/refresh_all_outstanding" class="btn btn-inverse btn-large">Kembali</a></p>


                            </div><!-- /.span4 -->
                        </div><!-- /.row -->
                    </div><!-- /.container -->
                    <input type="hidden" id="refreshed" value="no">
                </div><!-- /.span4 -->
            </div><!-- /.row -->
        </div><!-- /.container -->
        <?php
        $this->load->view("include/footer");
        ?>
        <script type="text/javascript">
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
    </body>
</html>


