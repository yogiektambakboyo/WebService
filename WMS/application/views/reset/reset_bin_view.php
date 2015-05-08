<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <title>Reset Bin</title>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <?php
        $this->load->view('include/header');
        ?>
    </head>

    <body>
        <div class="container">
            <div class="row">
                <div class="span12 center">
                    <h1>Reset Bin</h1>
                    <hr />
                    <br />
                    <?php
                    echo validation_errors();
                    if ($this->session->flashdata('pesan')) {
                        ?>
                        <div class="alert alert-success">
                            <?php
                            echo $this->session->flashdata('pesan');
                            ?>
                        </div>
                        <?php
                    }
                    ?>
                    <form class="form-horizontal" method="POST" action="<?php echo base_url() ?>index.php/reset/bin">
                        <div class="control-group input-append">
                            <input type="text" class="input-large" placeholder="Scan Kode Bin" name="bin" value="<?php echo set_value('bin') ?>"/>
                        </div>
                        <div class="control-group">
                            <input type="submit" name="btnReset" class="btn btn-large" value="Reset"/>
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
