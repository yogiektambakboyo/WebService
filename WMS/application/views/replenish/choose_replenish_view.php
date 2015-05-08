<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <title>Replenish</title>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <?php
        $this->load->view('include/header');
        ?>
    </head>

    <body>
        <div class="container">
            <div class="row">
                <div class="span12 center">
                    <h1>Replenish</h1>
                    <hr />
                    <p><a href="<?php echo base_url(); ?>index.php/replenish" class="btn btn-large">Replenish Admin</a></p>
                    <p><a href="<?php echo base_url(); ?>index.php/replenish_manual" class="btn btn-large">Replenish Manual</a></p>
                        
                </div><!-- /.span4 -->
            </div><!-- /.row -->
        </div><!-- /.container -->
        <?php
        $this->load->view("include/footer");
        ?>
    </body>
</html>
