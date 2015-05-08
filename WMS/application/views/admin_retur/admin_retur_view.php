<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <title>Transaksi Retur</title>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <?php
        $this->load->view('include/header');
        ?>
    </head>

    <body>
        <div class="container">
            <div class="row">
                <div class="span12 center">
                    <h1>Transaksi Retur</h1>
                    <hr />
                    <p><a href="<?php echo base_url() ?>index.php/transaksi/tambah_transaksi_bpb" class="btn btn-large">Tambah Transaksi Retur</a></p>
                    <p><a href="<?php echo base_url() ?>index.php/admin_retur/daftar_transaksi_bpb" class="btn btn-large">Daftar Transaksi Retur</a></p>
                </div><!-- /.span4 -->
            </div><!-- /.row -->
        </div><!-- /.container -->
        <?php
        $this->load->view("include/footer");
        ?>
    </body>
</html>
