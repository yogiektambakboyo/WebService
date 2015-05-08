<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <title>Cari Barang</title>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <?php
        $this->load->view('include/header');
        ?>
    </head>

    <body>
        <div class="container">
            <div class="row">
                <div class="span12 center">
                    <h1>Cari Barang</h1>
                    <hr />
                    <?php
                    echo validation_errors();
                    if (isset($error)) { //kode nota tidak valid
                        ?>
                        <div class="alert alert-error">
                            <strong>
                                <?php
                                echo $error;
                                ?>
                            </strong>
                        </div>
                        <?php
                    }
                    ?>
                    <form class="form-horizontal" method="POST" action="<?php echo base_url() ?>index.php/receive/cari_barang">
                        <div class="control-group">
                            <h4>Masukkan kata kunci (nama barang):</h4>
                            <input type="text" name="cari" value="<?php echo set_value('cari',isset($keyword) ? $keyword : '') ?>"/>
                        </div>
                        <div class="control-group">
                            <input type="submit" name="btnCari" class="btn btn-large" value="Cari"/>
                        </div>
                    </form>
                    <?php
                    if (isset($barang)) {
                        ?>
                        <form class="form-horizontal" method="POST" action="<?php echo base_url() ?>index.php/receive/cari_barang">
                            <div class="control-group">
                                <h4>Hasil Pencarian:</h4>
                                <table class="table table-bordered table-hover table-striped">
                                    <tr>
                                        <td>&nbsp;</td>
                                        <th>Kode Barang</th>
                                        <th>Keterangan</th>
                                    </tr>
                                    <?php
                                    foreach ($barang as $row) {
                                        ?>
                                        <tr>
                                            <td style="text-align: center"><input type="radio" name="barang" value="<?php echo $row['Kode']."~".$row['Keterangan'] ?>" /></td>
                                            <td><?php echo $row['Kode'] ?></td>
                                            <td><?php echo $row['Keterangan'] ?></td>
                                        </tr>
                                        <?php
                                    }
                                    ?>
                                </table>
                            </div>
                            <div class="control-group">
                                <input type="submit" name="btnPilih" class="btn btn-large" value="Pilih"/>
                                <input type="hidden" id="refreshed" value="no">
                            </div>
                        </form>
                        <?php
                    }
                    ?>
                </div><!-- /.span4 -->
            </div><!-- /.row -->
        </div><!-- /.container -->
        <?php
        $this->load->view("include/footer");
        ?>
        <script type="text/javascript">
            $('tr').click(
            function() {
                $('input[type=radio]',this).prop('checked', true);
            }
        );    
        </script>
    </body>
</html>
