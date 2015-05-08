<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <title>Cek Barang</title>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <?php
        $this->load->view('include/header');
        ?>
    </head>

    <body>
        <div class="container">
            <div class="row">
                <div class="span12 center">
                    <h1>Cek Barang</h1>
                    <hr />
                    <?php
                    echo validation_errors();
                    ?>
                    <form class="form-horizontal" method="POST" action="<?php echo base_url() ?>index.php/umum">
                        <div class="control-group">
                            <input type="text" class="input-large" placeholder="Kode Rack" name="RackSlotCode" value="<?php echo set_value('RackSlotCode') ?>"/>
                        </div>
                        <div class="control-group">
                            <input type="submit" name="btnCek" class="btn btn-large" value="Cek"/>
                        </div>
                    </form>
                    <?php
                    if (isset($brg)) {
                        ?>
                        <h4>Daftar Barang</h4>
                        <br/>
                        <table class="table table-bordered tablesorter">
                            <thead>
                                <tr>
                                    <th>Kode Bin</th>
                                    <th>Barang</th>
                                    <th>Exp Date</th>
                                    <th>Jumlah</th>
                                </tr>
                            </thead>
                            <tbody>
                                    <?php
                                    foreach ($brg as $row) {
                                        ?>
                                        <tr>
                                            <td><?php echo $row['BinCode'] ?></td>
                                            <td><?php echo $row['Keterangan'] ?></td>
                                            <td><?php echo date("d-m-Y", strtotime($row['ExpDate'])) ?></td>
                                            <td><?php echo $row['Qty'] ?></td>
                                        </tr>
                                        <?php
                                    }
                                    ?>
                                </tbody>
                        </table>
                        <?php
                    }
                    ?>
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
                $(".tablesorter").tablesorter({
                    theme : "bootstrap", // this will 

                    widthFixed: true,

                    headerTemplate : '{content} {icon}', // new in v2.7. Needed to add the bootstrap icon!

                    // widget code contained in the jquery.tablesorter.widgets.js file
                    // use the zebra stripe widget if you plan on hiding any rows (filter widget)
                    widgets : [ "uitheme", "filter"],
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
