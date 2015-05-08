<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <title>Print Barcode</title>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <?php
        $this->load->view('include/header');
        ?>
    </head>

    <body>
        <div class="container">
            <div class="row">
                <div class="span12 center">
                    <h1>Print barcode</h1>
                    <hr />
                    <p><a href="<?php echo base_url(); ?>index.php/barcode/bin_barcode" class="btn btn-large">Bin Barcode</a></p>
                    <p><a href="<?php echo base_url(); ?>index.php/barcode/rack_barcode" class="btn btn-large">Rak Barcode</a></p>
                    <p><a href="<?php echo base_url(); ?>index.php/barcode/rack_barcode2" class="btn btn-large">Rak Barcode Nama</a></p>
                </div><!-- /.span4 -->
            </div><!-- /.row -->
        </div><!-- /.container -->
        <?php
        $this->load->view("include/footer");
        ?>
    </body>
</html>
