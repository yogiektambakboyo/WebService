<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <title>Pengaturan Team</title>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        
        <?php
        $this->load->view('include/header');
        ?>
    </head>

    <body>
        <div class="container">
            <div class="row">
                <div class="span12 center">
                    <h1>Action Team</h1>
                    <hr />
                    <p><a href="<?php echo base_url() ?>index.php/receiving/bpb" class="btn btn-large">Daftar Team</a></p>
                    <p><a href="#" class="btn btn-large">Tambah Team</a></p>
                    <p><a href="#" class="btn btn-large">Ubah Team</a></p>
                    <p><a href="#" class="btn btn-large">Hapus Team</a></p>
                </div><!-- /.span4 -->
            </div><!-- /.row -->
        </div><!-- /.container -->
        <?php
        $this->load->view("include/footer");
        ?>
    </body>
</html>
