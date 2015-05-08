<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <title>Operator</title>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <?php
        $this->load->view('include/header');
        ?>
    </head>

    <body>
        <div class="container">
            <div class="row">
                <div class="span12 center">
                    <h1>Operator</h1>
                    <hr />
                    <p><a href="<?php echo base_url() ?>index.php/operator/tambah_tim" class="btn btn-large">Tambah Tim</a></p>
                    <p><a href="<?php echo base_url() ?>index.php/operator" class="btn btn-large">Daftar Tim</a></p>
                </div><!-- /.span4 -->
            </div><!-- /.row -->
        </div><!-- /.container -->
        <?php
        $this->load->view("include/footer");
        ?>
    </body>
</html>
