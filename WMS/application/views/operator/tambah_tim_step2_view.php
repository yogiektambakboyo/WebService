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
                        <div class="control-group">
                            <select name="tugas">
                                <option value="0">- Pilih Tugas -</option>
                                <?php
                                foreach ($tugas as $row) {
                                    ?>
                                    <option value="<?php echo $row['id'] ?>" <?php echo set_select('tugas', $row['id']); ?>><?php echo $row['whRoleName'] ?></option>
                                    <?php
                                }
                                ?>
                            </select>
                        </div>
                        <div class="control-group">
                            <select name="zone">
                                <option value="0">- Pilih Zone -</option>
                                <?php
                                foreach ($zone as $row) {
                                    ?>
                                    <option value="<?php echo $row['id'] ?>" <?php echo set_select('zone', $row['id']); ?>><?php echo $row['zoneName'] ?></option>
                                    <?php
                                }
                                ?>
                            </select>
                        </div>
                        <div class="control-group">
                            <input type="hidden" name="tanggal" value="<?php echo $tanggal ?>"/>
                            <input type="hidden" name="shift" value="<?php echo $shift ?>"/>
                            <input type="submit" name="btnProsesStep2" class="btn btn-large" value="Proses"/>
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
