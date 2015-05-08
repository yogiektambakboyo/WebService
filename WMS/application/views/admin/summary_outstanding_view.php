<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <title>Summary Outstanding</title>
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
                    <h1>Summary Outstanding</h1>
                    <hr />

                    <div class="control-group">
                        <!-- div class="scroll2" -->
                        <table class="table table-bordered table-hover table-striped table-condensed">
                            <thead>
                                <tr>
                                    <th>Project Transaksi</th>
                                    <th>Jumlah Outstanding</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                foreach ($summary as $row) {
                                    ?>
                                    <tr>
                                        <th>
                                            <?php
                                            if ($row['ProjectCode'] == 'BPB') {
                                                echo 'Receiving BPB';
                                            } else if ($row['ProjectCode'] == 'RJT') {
                                                echo 'Receiving Retur/Tolakan';
                                            } else if ($row['ProjectCode'] == 'PCK') {
                                                echo 'Picking/Shipping';
                                            } else if ($row['ProjectCode'] == 'RPL') {
                                                echo 'Replenish/Movement';
                                            }else if ($row['ProjectCode'] == 'RTS') {
                                                echo 'Transfer Stok Masuk';
                                            }
                                            else if ($row['ProjectCode'] == 'PTS') {
                                                echo 'Transfer Stok Keluar';
                                            }
                                            ?>
                                        </th>
                                        <th><?php echo $row['outstanding'] ?></th>
                                        <th>
                                            <a href="
                                            <?php
                                            if ($row['ProjectCode'] == 'BPB') {
                                                echo base_url() . 'index.php/transaksi/daftar_transaksi_bpb';
                                            } else if ($row['ProjectCode'] == 'RJT') {
                                                echo base_url() . 'index.php/admin_retur/daftar_transaksi_retur';
                                            } else if ($row['ProjectCode'] == 'PCK') {
                                                echo base_url() . 'index.php/admin_picking/daftar_task_picking';
                                            } else if ($row['ProjectCode'] == 'RPL') {
                                                echo base_url() . 'index.php/admin_replenish/daftar_transaksi_rpl';
                                            }else if ($row['ProjectCode'] == 'RTS') {
                                                echo base_url() . 'index.php/admin_transfermasuk/daftar_admin_transfermasuk';
                                            }else if ($row['ProjectCode'] == 'PTS') {
                                                echo base_url() . 'index.php/admin_transferkeluar/daftar_task_picking';
                                            }
                                            ?>"
                                               class="btn btn-info btn-small">Detail</a> 
                                        </th>
                                    </tr>
                                    <?php
                                }
                                ?>
                            </tbody>
                        </table>
                        <!--/div -->
                    </div>
                   

                </div><!-- /.span4 -->
            </div><!-- /.row -->
        </div><!-- /.container -->
        <?php
        $this->load->view("include/footer");
        ?>
        <script type="text/javascript" >
        
            function gotolink(l)
            {
                if (l == 'BPBPC') {
                    url('<?php echo base_url() ?>index.php/transaksi/daftar_transaksi_bpb');
                } else if (l == 'RTN') {
                    url('<?php echo base_url() ?>');
                } else if (l == 'RJT') {
                    url('<?php echo base_url() ?>');
                } else if (l == 'PCK') {
                    url('<?php echo base_url() ?>');
                } else if (l == 'SPG') {
                    url('<?php echo base_url() ?>');
                }
            }
        </script>
    </body>
</html>


