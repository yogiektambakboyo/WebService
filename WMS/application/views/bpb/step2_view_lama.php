<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <title>Terima BPB</title>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <?php
        $this->load->view('include/header');
        ?>
    </head>

    <body>
        <div class="container">
            <div class="row">
                <div class="span12 center">
                    <h1>Terima BPB</h1>
                    <hr />
                    <br />
                    <?php
                    echo validation_errors();
                    if (isset($error)) {
                        ?>
                        <div class="alert alert-error">
                            <?php
                            echo $error;
                            ?>
                        </div>
                        <?php
                    }
                    if ($this->session->flashdata('pesan')) {
                        ?>
                        <div class="alert alert-success">
                            <?php
                            echo $this->session->flashdata('pesan');
                            ?>
                        </div>
                        <?php
                    }
                    if ($this->session->flashdata('error')) {
                        ?>
                        <div class="alert alert-error">
                            <?php
                            echo $this->session->flashdata('error');
                            ?>
                        </div>
                        <?php
                    }
                    ?>
                    <form class="form-horizontal" method="POST" action="<?php echo base_url() ?>index.php/bpb/step2">
                        <div class="control-group">
                            <input type="text" class="input-large" placeholder="Masukkan palet" name="palet" value="<?php echo set_value('palet') ?>"/>
                        </div>
                        <div class="control-group">
                            <input type="text" class="input-large" placeholder="Masukkan rackslot" name="rackslot" value="<?php echo set_value('rackslot') ?>"/>
                        </div>
                        <div class="control-group">
                            <input type="submit" name="btnTambah" class="btn btn-large" value="Tambah"/>
                        </div>
                    </form>
                </div><!-- /.span4 -->
            </div><!-- /.row -->
        </div><!-- /.container -->
        <?php
        $this->load->view("include/footer");
        $today = mktime(0, 0, 0, date("m"), date("d"), date("Y"));
        ?>
        <script type="text/javascript">
            $(".tanggal").datepicker({
                format: "yyyy/mm/dd"
            });
            $('.tanggal').datepicker('setStartDate', "<?php echo date("Y-m-d", $today) ?>");
        </script>
    </body>
</html>
