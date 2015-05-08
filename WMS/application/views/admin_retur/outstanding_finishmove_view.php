<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <title>Outstanding Move BPB</title>
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
                    <h1>Outstanding Move BPB</h1>
                    <hr />
                    <?php
                    echo validation_errors();
                    if (isset($error)) {
                        echo "<div class='alert alert-error'><strong>" . $error . "</strong><br /></div>";
                    }
                    if (isset($pesan)) {
                        ?>
                        <div class="alert alert-success">
                            <strong>
                                <?php
                                echo $pesan;
                                ?>
                            </strong>
                        </div>
                        <?php
                    }
                    ?>
                    <?php
                    echo "<strong>No. Transaksi: </strong>" . $TransactionCode . "<br />";
                    echo "<strong>No. Nota: </strong>" . $ERPCode . "<br />";
                    ?>
                    <table class="table table-bordered table-hover table-striped">
                        <tr>
                            <th>No</th>
                            <th>Bin</th>
                            <th>SKU</th>
                            <th>ExpDate</th>
                            <th>Jumlah</th>
                            <th>RackSlot Skrg</th>
                            <th>Operator Terakhir</th>
                            <th>Clear</th>
                        </tr>
                        <?php
                        $i = 1;
                        foreach ($outstanding as $row) {
                            ?>
                            <tr>
                                <td><?php echo $i++ ?></td>
                                <td><?php echo $row['BinCode'] ?></td>
                                <td><?php echo $row['Keterangan'] ?></td>
                                <td><?php echo date('d-m-Y', strtotime($row['ExpDate'])); ?></td>
                                <td><?php echo $row['Qty'] ?></td>
                                <td><?php echo $row['CurrRack'] ?></td>
                                <td><?php echo $row['OperatorName'] ?></td>
                                <td><button onclick="clearoutstanding(<?php echo "'".$row['TransactionCode']."','".$row['BinCode']."',".$row['NoUrut'].",".$row['QueueNumber'] ?>)" class="btn-small btn-primary"><i class="icon-white icon-warning-sign"></i>Clear</button></td>
                            </tr>
                            <?php
                        }
                        ?>
                    </table>
                    <input type="hidden" id="refreshed" value="no">
                    <div id="formclear" >
                        <p class="validateTips">Catatan Clear Outstanding Bin <span id="BinCode"></span></p>
                        <form method="POST" action="<?php echo base_url() ?>index.php/admin_retur/simpan_clear_outstanding_finishmove" >
                            <fieldset>
                                <textarea name="noteclear" id="editor" rows="10" cols="100" placeholder="Enter text ..."></textarea>
                                <input type="hidden" name="TransactionCode" id="TransactionCode" />
                                <input type="hidden" name="NoUrut" id="NoUrut" />
                                <input type="hidden" name="QueueNumber" id="QueueNumber" />
                                <input type="submit" name="btnSimpanClear" value="Simpan" class="btn btn-small btn-danger" />
                            </fieldset>
                        </form>
                    </div>
                    <p><a href="<?php echo base_url() ?>index.php/admin_retur/edit_mastertaskrcv/<?php echo str_replace('=', '-', str_replace('/', '_', base64_encode($TransactionCode))); ?>" class="btn btn-large btn-inverse">Kembali</a></p>
                </div><!-- /.span4 -->
            </div><!-- /.row -->
        </div><!-- /.container -->
        <?php
        $this->load->view("include/footer");
        ?><script type="text/javascript">
            $("#formclear").hide();
            
            function clearoutstanding(TransactionCode,BinCode,NoUrut,QueueNumber) {
                $('#TransactionCode').val(TransactionCode);
                $('#NoUrut').val(NoUrut);
                $('#QueueNumber').val(QueueNumber);
                $('#BinCode').html(BinCode);
                $( "#formclear" ).dialog({ 
                    height: 380,
                    width: 550,
                    modal: true});
            };
        
        </script>
    </body>
</html>
