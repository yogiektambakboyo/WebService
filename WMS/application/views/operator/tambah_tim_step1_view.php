<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <title>Tambah Tim</title>
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
                    <h1>Tambah Tim</h1>
                    <hr />
                    <?php
                    echo validation_errors();
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
                    ?>
                    <?php
                    $today = mktime(0, 0, 0, date("m"), date("d"), date("Y"));
                    $tomorrow = mktime(0, 0, 0, date("m"), date("d") + 1, date("Y"));
                    ?>
                    <form class="form-horizontal" method="POST" action="<?php echo base_url() ?>index.php/operator/tambah_tim">
                        <div class="control-group">
                            <input size="16" type="text" name="tanggal" value="<?php echo date("Y-m-d", $today) ?>" placeholder="Tanggal Aktif" readonly="readonly" />
                        </div>
                        <div class="control-group">
                            <select name="shift">
                                <option value="0">- Pilih Shift -</option>
                                <option value="1" <?php echo set_select('shift', '1'); ?>>1</option>
                                <option value="2" <?php echo set_select('shift', '2'); ?>>2</option>
                                <option value="3" <?php echo set_select('shift', '3'); ?>>3</option>
                            </select>
                        </div>
                        <div class="control-group">
                            <input type="submit" name="btnProsesStep1" class="btn btn-large" value="Proses"/>
                        </div>
                    </form>
                </div><!-- /.span4 -->
            </div><!-- /.row -->
        </div><!-- /.container -->
        <?php
        $this->load->view("include/footer");
        ?>
        <script type="text/javascript">
            $(".tanggal").datepicker({
                format: "yyyy/mm/dd"
            });
            $('.tanggal').datepicker('setStartDate', "<?php echo date("Y-m-d", $today) ?>");
            $('.tanggal').datepicker('setEndDate', "<?php echo date("Y-m-d", $tomorrow) ?>");
        </script> 
    </body>
</html>
