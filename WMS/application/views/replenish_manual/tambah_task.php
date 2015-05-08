<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <title>Buat Task Replenish</title>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <?php
        $this->load->view('include/header');
        ?>
    </head>

    <body>
        <div class="container">
            <div class="row">
                <div class="span12 center">
                    <h1>Buat Task Replenish</h1>
                    <hr />
                    <p><a href="<?php echo base_url() ?>index.php/replenish_manual/buat_task" class="btn btn-large">Task Replenish</a></p>
                </div><!-- /.span4 -->
            </div><!-- /.row -->
        </div><!-- /.container -->
        <?php
        $this->load->view("include/footer");
        ?>
        
    </body>
</html>
