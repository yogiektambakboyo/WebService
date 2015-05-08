<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <title>Storing</title>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <?php
        $this->load->view('include/header');
        ?>
    </head>

    <body>
        <div class="container">
            <div class="row">
                <div class="span12 center">
                    <h1>Storing</h1>
                    <hr />
                    <input type="hidden" id="refreshed" value="no">
                    <p><a href="<?php echo base_url() ?>index.php/storing/tambah_bpb_storing" class="btn btn-large">Storing BPB</a></p>
                    <p><a href="<?php echo base_url() ?>index.php/storing/tambah_retur_storing" class="btn btn-large">Storing Retur</a></p>
                  
                </div><!-- /.span4 -->
            </div><!-- /.row -->
        </div><!-- /.container -->
        <?php
        $this->load->view("include/footer");
        ?>
    </body>
</html>
