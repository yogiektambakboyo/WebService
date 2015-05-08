<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <title>Bin Shipping</title>
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
                    <h1>Bin Shipping</h1>
                    <hr />
                    <?php
                    echo validation_errors();
                    ?>
                    <br />
                    <div class="control-group">
                        <input type="text" class="input-large" readonly="readonly" placeholder="Masukkan PickList" name="picklist" value="<?php echo $this->session->userdata('ERPCode'); ?>" />
                        <div class="controls">

                        </div>
                    </div>
                    <br />
                    <table class="table table-bordered table-striped">
                        <tr>
                            <th>Kode Bin Outstanding</th>
                        </tr>
                        <?php
                        foreach ($bin as $row) {
                            ?>
                            <tr>
                                <td><?php echo $row['BinCode'] ?></td>
                            </tr>
                            <?php
                        }
                        ?>
                    </table>

                    <br />
                    <form class="form-horizontal" method="POST" action="<?php echo base_url() ?>index.php/shipping/scan_bin_kendaraan">
                        <div class="control-group">
                            <label id="label"><strong>Bin</strong></label> : <input type="text" class="input-medium" placeholder="Masukkan Kode Bin" name="kodebin"/>
                            <div class="controls">

                            </div>
                        </div>
                        <div class="control-group">
                            <label id="label"><strong>Kendaraan</strong></label> : <input type="text" class="input-medium" placeholder="Masukkan Kode Bin kendaraan" name="kodebindest"/>
                            <div class="controls">

                            </div>
                        </div>

                        <div class="control-group">
                            <input type="hidden" id="refreshed" value="no">
                            <input type="submit" name="btnProses" class="btn btn-large" value="Proses"/>
                            <div class="controls">

                            </div>
                        </div>
                    </form>
                    <div class="container">
                        <div class="row">
                            <div class="span12 center">
                                <p><a href="<?php echo base_url() ?>index.php/shipping/gotomainmenu" class="btn btn-large btn-inverse">Kembali</a></p>
                            </div><!-- /.span4 -->
                        </div><!-- /.row -->
                    </div><!-- /.container -->
                </div><!-- /.span4 -->
            </div><!-- /.row -->
        </div><!-- /.container -->
        <?php
        $this->load->view("include/footer");
        ?>

    </body>
</html>
