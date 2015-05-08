<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <title>Daftar Barang Bin <?php echo $this->session->userdata('kodebin'); ?></title>
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
                    <h1>Daftar Barang Bin <?php echo $this->session->userdata('kodebin'); ?></h1>
                    <hr />
                    
                    <div class="control-group">
                        <label id="label"><strong>PickList</strong></label> : <input type="text" readonly="readonly" class="input-medium" placeholder="Masukkan PickList" name="picklist" value="<?php echo $this->session->userdata('ERPCode'); ?>" />
                        <div class="controls">

                        </div>
                    </div>
                    <br />
                    <div class="control-group">
                        <label id="label"><strong>Tujuan</strong></label> : <input type="text" readonly="readonly" class="input-small" placeholder="Kode Bin Tujuan" value="<?php echo $this->session->userdata('kodebindest'); ?>" />
                        <div class="controls">

                        </div>
                    </div>
                    
                    <br />
                    <div class="control-group">
                        <!-- div class="scroll2" -->
                            <table class="table table-bordered table-hover table-striped table-condensed">
                                <thead>
                                    <tr>
                                        <th>Nama Barang</th>
                                        <!--th>Exp Date</th -->
                                        <th>Jumlah</th>
                                        <!--th>Proses</th-->
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $i = 0;
                                    foreach ($skubin as $row) {
                                      
                                            ?>

                                            <tr onclick="document.getElementById('myForm<?php echo $i; ?>').submit();">
                                                <td><?php echo $row['Keterangan'] ?></td>
                                                <!--td><?php /*echo date("d-m-Y", strtotime($row['ExpDate']))*/ ?></td-->
                                                <td><?php echo $row['QtyNeedNow'] ?>
                                                    <form method="POST" action="<?php echo base_url() ?>index.php/shipping/masuk_barang" id="myForm<?php echo $i; ?>">
                                                        <input type="hidden" name="Keterangan" value="<?php echo $row['Keterangan'] ?>" />
                                                        <input type="hidden" name="TransactionCode" value="<?php echo $row['TransactionCode'] ?>" />
                                                        <input type="hidden" name="SKUCode" value="<?php echo $row['SKUCode'] ?>" />
                                                        <input type="hidden" name="BinCode" value="<?php echo $row['BinCode'] ?>" />
                                                        <input type="hidden" name="QtyNeedNow" value="<?php echo $row['QtyNeedNow'] ?>" />
                                                        <!--input type="hidden" name="NoUrut" value="<?php /*echo $row['NoUrut']*/ ?>" /-->
                                                        <!--input type="submit" name="btnProses" class="btn btn-small" value="Proses" /-->
                                                    </form>
                                                </td>
                                                <!--td>
                                                    
                                                </td-->
                                            </tr>
                                            <?php
                                        $i++;
                                    }
                                    ?>
                                </tbody>
                            </table>
                        <!--/div -->
                    </div>
                    <div class="container">
                        <div class="row">
                            <div class="span12 center">
                                <p><a href="<?php echo base_url() ?>index.php/shipping/scan_bin_kendaraan" class="btn btn-large btn-inverse">Kembali</a></p>
                            </div><!-- /.span4 -->
                        </div><!-- /.row -->
                    </div><!-- /.container -->
                    <input type="hidden" id="refreshed" value="no">
                </div><!-- /.span4 -->
            </div><!-- /.row -->
        </div><!-- /.container -->
        <?php
        $this->load->view("include/footer");
        ?>
    </body>
</html>


