<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <title>Shipping</title>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <?php
        $this->load->view('include/header');
        ?>
    </head>

    <body>
        <div class="container">
            <div class="row">
                <div class="span12 center">
                    <h1>Shipping <?php echo $this->session->userdata('ERPCode'); ?></h1>
                    <hr/>
                    <p><a href="<?php echo base_url() ?>index.php/shipping/scan_bin_kendaraan" class="btn btn-large">Masukkan Barang</a></p>
                    <p><a href="<?php echo base_url() ?>index.php/shipping/list_kembali" class="btn btn-large">Kembalikan Barang</a></p>
                    <p><a href="<?php echo base_url() ?>index.php/shipping/list_bermasalah" class="btn btn-large">Bermasalah</a></p>
                    <p><a href="<?php echo base_url() ?>index.php/shipping" class="btn btn-large btn-inverse">Kembali</a></p>
                </div><!-- /.span4 -->
            </div><!-- /.row -->
        </div><!-- /.container -->
        <?php
        $this->load->view("include/footer");
        ?>
    </body>
</html>
