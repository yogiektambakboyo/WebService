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
                    ?>
                    <form class="form-horizontal" method="POST" action="<?php echo base_url() ?>index.php/operator/tambah_tim">                        
                        <h4>Pilih Anggota</h4>
                        <?php
                        foreach ($operator as $row) {
                            ?>
                            <div class="control-group">
                                <label class="checkbox inline">
                                    <input type="checkbox" name="operator[]" value="<?php echo $row['staffCode'] ?>" <?php echo set_checkbox('operator[]', $row['staffCode']); ?>/> <?php echo $row['staffCode'] ?>
                                </label>
                            </div>
                            <?php
                        }
                        ?>
                        <h4>Keterangan</h4>
                        <div class="control-group">
                            <textarea rows="3" name="keterangan" style="resize: none;"><?php echo set_value('keterangan') ?></textarea>
                        </div>
                        <div class="control-group">
                            <input type="hidden" name="tanggal" value="<?php echo $tanggal ?>"/>
                            <input type="hidden" name="shift" value="<?php echo $shift ?>"/>
                            <input type="hidden" name="tugas" value="<?php echo $tugas ?>"/>
                            <input type="hidden" name="zone" value="<?php echo $zone ?>"/>
                            <input type="submit" name="btnProsesStep3" class="btn btn-large" value="Tambah"/>
                        </div>
                    </form>
                </div><!-- /.span4 -->
            </div><!-- /.row -->
        </div><!-- /.container -->
        <?php
        $this->load->view("include/footer");
        ?>
        <?php
        $today = mktime(0, 0, 0, date("m"), date("d"), date("Y"));
        $tomorrow = mktime(0, 0, 0, date("m"), date("d") + 1, date("Y"));
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
