<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <title>Movement</title>
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
                    <h1>Movement</h1>
                    <hr />

                    <p><a href="<?php echo base_url() ?>index.php/admin_replenish/tambah_replenish" class="btn btn-large">Replenish</a></p>
                    <p><a href="<?php echo base_url() ?>index.php/#" class="btn btn-large">Movement</a></p>


                </div><!-- /.span4 -->
            </div><!-- /.row -->
        </div><!-- /.container -->
        <?php
        $this->load->view("include/footer");
        ?>
    </body>
</html>


