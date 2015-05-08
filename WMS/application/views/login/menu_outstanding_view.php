<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <title>Pilih Jenis Outstanding</title>
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
                    <h1>Pilih Jenis Outstanding</h1>
                    <hr />
                    <?php
                    if (!$menuoutstanding['statusRCV']) {
                        ?>
                        <p><a href="<?php echo base_url() ?>index.php/storing/list_my_outstanding" class="btn btn-large">Receiving & Retur</a></p>
                        <?php
                    }
                    ?>
                    <?php
                    if (!$menuoutstanding['statusPCK']) {
                        ?>
                        <p><a href="<?php echo base_url() ?>index.php/picking/refresh_all_outstanding" class="btn btn-large">Picking</a></p>
                        <?php
                    }
                    ?>
                    <?php
                    if (!$menuoutstanding['statusRPLManual']) {
                        ?>
                        <p><a href="<?php echo base_url() ?>index.php/replenish_manual" class="btn btn-large">Replenish Manual</a></p>
                        <?php
                    }
                    ?>  
					<?php
                    if (!$menuoutstanding['statusRPL']) {
                        ?>
                        <p><a href="<?php echo base_url() ?>index.php/replenish/my_outstanding" class="btn btn-large">Replenish</a></p>
                        <?php
                    }
                    ?>  
                    <input type="hidden" id="refreshed" value="no">
                </div><!-- /.span4 -->
            </div><!-- /.row -->
        </div><!-- /.container -->
        <?php
        $this->load->view("include/footer");
        ?>

    </body>
</html>
