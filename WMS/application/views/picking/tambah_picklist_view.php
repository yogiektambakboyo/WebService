<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <title>Tambah PickList</title>
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
                    <h1>Tambah PickList</h1>
                    <hr />
                    <?php
                    echo validation_errors();
                    if ($this->session->flashdata('pesan')) {
                        ?>
                        <div class="alert alert-success">
                            <strong>
                                <?php
                                echo $this->session->flashdata('pesan');
                                ?>
                            </strong>
                        </div>
                        <?php
                    }
                    if (isset($error)) {
                        ?>
                        <div class="alert alert-error">
                            <strong>
                                <?php
                                echo $error;
                                ?>
                            </strong>
                        </div>
                        <?php
                    }
                    ?>

                    <form class="form-horizontal" method="POST" action="<?php echo base_url() ?>index.php/picking/tambah_picklist">
                        <div class="control-group">
                            <h4>Pilih PickList:</h4>
                            <table class="table table-bordered tablesorter">
                                <thead>
                                    <tr>
                                        <th>&nbsp;</th>
                                        <th>Kode Transaksi</th>
                                        <th>Kode PickList</th>
                                        <th>Tanggal</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    foreach ($picklist as $row) {
                                        ?>
                                        <tr
                                        <?php
                                        if ($row['Assigned'] == 0) {
                                            ?>
                                                bgcolor="#00FFFF"
                                                <?php
                                            }else if ($row['Assigned'] == 1) {
                                            ?>
                                                bgcolor="#FFFF00"
                                                
                                                <?php
                                            }
                                            ?>
                                            >
                                            <td style="text-align: center"><input type="radio" name="picklist" value="<?php echo $row['TransactionCode'] . "~" . $row['ERPCode'] . "~" . $row['WHCode'] . "~" . $row['EDPanjang'] ?>" /></td>
                                            <td><?php echo $row['TransactionCode'] ?></td>
                                            <td><?php echo $row['ERPCode'] ?></td>
                                            <td><?php echo date("d-m-Y", strtotime($row['TransactionDate'])) ?></td>
                                        </tr>
                                        <?php
                                    }
                                    ?>
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <th colspan="4" class="pager form-horizontal" style="text-align: center">
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
                        <div class="control-group">
                            <input type="submit" name="btnTambahPickList" class="btn btn-large" value="Proses"/>
                            <input type="hidden" id="refreshed" value="no">
                        </div>
                    </form>
                </div><!-- /.span4 -->
            </div><!-- /.row -->
        </div><!-- /.container -->
        <?php
        $this->load->view("include/footer");
        ?>
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
                $(".tablesorter").tablesorter({
                    theme : "bootstrap", // this will 

                    widthFixed: true,

                    headerTemplate : '{content} {icon}', // new in v2.7. Needed to add the bootstrap icon!

                    // widget code contained in the jquery.tablesorter.widgets.js file
                    // use the zebra stripe widget if you plan on hiding any rows (filter widget)
                    widgets : [ "uitheme", "filter" ],
                    headers: {
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
    </body>
</html>

