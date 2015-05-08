<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <title>Transaksi BPB</title>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <?php
        $this->load->view('include/header');
        ?>
    </head>

    <body>
        <div class="container">
            <div class="row">
                <div class="span12 center">
                    <h1>Transaksi BPB</h1>
                    <hr />
                    <?php
                    if ($this->session->flashdata('pesan')) {
                        ?>
                        <div class="alert alert-success">
                            <strong>
                                <?php
                                echo $this->session->flashdata('pesan');
                                ?>
                            </strong>
                        </div>
                        <?php
                    }
                    if ($this->session->flashdata('error')) {
                        ?>
                        <div class="alert alert-error">
                            <strong>
                                <?php
                                echo $this->session->flashdata('error');
                                ?>
                            </strong>
                        </div>
                        <?php
                    }
                    ?>
                    <p><a href="<?php echo base_url() ?>index.php/transaksi/tambah_transaksi_bpb" class="btn btn-large">Tambah Task BPB</a></p>
                    <p><a href="<?php echo base_url() ?>index.php/transaksi/daftar_transaksi_bpb" class="btn btn-large">Daftar Task BPB</a></p>
                </div><!-- /.span4 -->
            </div><!-- /.row -->
        </div><!-- /.container -->
        <?php
        $this->load->view("include/footer");
        ?>
    </body>
</html>
