<!--
To change this template, choose Tools | Templates
and open the template in the editor.
-->
<!DOCTYPE html>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <title>Transaksi Tolakan</title>
        <mata name="viewport" content="width=device-width, intial-scale=1.0">
        <?php 
            $this->load->view("include/header");
        ?>
    </head>
    <body>
        <div class="container">
            <div class="row">
                <div class="span12 center">
                    <h1>Transaksi Tolakan</h1>
                    <hr />
                    <?php 
                    echo validation_errors();
                    if (isset($error)) {
                        ?>
                        <div class="alert alert-error">
                            <strong>
                                <?php echo $error;
                                ?>
                            </strong>
                        </div>
                    <?php
                    } 
                    ?>
                    <form class="form-horizontal" method="POST" action="<?php echo base_url() ?>index.php/receive/penerimaan_tolakan">
                        <div class="control-group">
                            <h4>Pilih Nota Tolakan:</h4>
                            <table class="table table-bordered table-hover table-striped">
                                <tr>
                                    <td></td>
                                    <td>Kode Nota</td>
                                    <td>Tanggal</td>
                                </tr>
                                <?php
                                    foreach ($tolakan as $row) {
                                        ?>
                                        <tr>
                                            <td style="text-align: center">
                                                <input type='radio' name="transactionCode" value="<?php echo $row['TransactionCode'] ?>" />
                                            </td>
                                            <td><?php echo $row['ERPCode']?></td>
                                            <td><?php echo date('d-M-Y', strtotime($row['TransactionDate'])); ?></td>
                                        </tr>
                                <?php
                                    }
                                ?>
                            </table>
                        </div>
                        <div class="control-group">
                            <label id="label"><strong>Rack</strong></label> : <input type="text" class="input-medium" placeholder="kode Rack Slot" name="kodeRack" value="<?php echo set_value('koderack') ?>" />
                        </div>
                        <div class="control-group">
                            <input type="hidden" id="refreshed" value="no">
                            <input type="submit" name="btnProses" class="btn btn-large" value="Proses" />
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <?php
            $this->load->view("include/footer");
        ?>
        <script type="text/javascript">
            $('tr').click(
            function() {
                $('input[type=radio]',this).attr('checked','checked');
            }
        );    
        </script>
    </body>
</html>
