<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <title>Edit Task</title>
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
                    <h1>Edit Task Receive</h1>
                    <hr />
                    <?php
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
                    echo validation_errors();
                    if (isset($error)) {
                        echo "<div class='alert alert-error'><strong>" . $error . "</strong><br /></div>";
                    }
                    ?>
                    <?php
                    echo "<strong>No. Transaksi: </strong>" . $note['TransactionCode'] . "<br />";
                    echo "<strong>No. Nota: </strong>" . $note['ERPCode'] . "<br />";
                    ?>
                    <h4>Edit Note Master</h4>
                    <br/>
                    <form method="POST" action="<?php echo base_url() ?>index.php/transaksi/simpan_mastertaskrcv" >
                        <textarea name="note" id="editor" rows="10" cols="100" placeholder="Enter text ..."><?php echo $note['Note']; ?></textarea>
                        <input type="hidden" name="TransactionCode" value="<?php echo $note['TransactionCode']; ?>" />
                        <input type="hidden" name="ERPCode" value="<?php echo $note['ERPCode']; ?>" />
                        <br/>

                        <input type="submit" name="btnSimpan" value="Simpan" class="btn btn-primary" />

                    </form>
                    <div id="formcanceltask">
                        <p class="validateTips">Catatan Pembatalan Receiving.</p>
                        <form method="POST" action="<?php echo base_url() ?>index.php/transaksi/simpan_pembatalanrcv" >
                            <fieldset>
                                <textarea name="notecancel" id="editor" rows="10" cols="100" placeholder="Enter text ..."></textarea>
                                <input type="hidden" name="TransactionCode" value="<?php echo $note['TransactionCode']; ?>" />
                                <input type="hidden" name="ERPCode" value="<?php echo $note['ERPCode']; ?>" />
                                <input type="submit" name="btnSimpanCancel" value="Simpan" class="btn btn-small btn-danger" />
                            </fieldset>
                        </form>
                    </div>
                    <?php
                    if ($isFinishMove == 0) {
                        ?>
                        <p><a href="<?php echo base_url() ?>index.php/transaksi/cek_finishmove/<?php echo str_replace('=', '-', str_replace('/', '_', base64_encode($note['TransactionCode']))) . "/" . str_replace('=', '-', str_replace('/', '_', base64_encode($note['ERPCode']))) ?>" class="btn btn-small btn-warning"><i class="icon-white icon-road"></i> Selesai Pemindahan</a></p>

                        <p><button id="canceltask" class="btn btn-small btn-danger" onclick="opendialog()"><i class="icon-white icon-trash"></i> Batalkan Task Receiving</button></p>
                        <?php
                    } else {
                        ?>
                        <p><a href="<?php echo base_url() ?>index.php/transaksi/cek_finishadmin/<?php echo str_replace('=', '-', str_replace('/', '_', base64_encode($note['TransactionCode']))) . "/" . str_replace('=', '-', str_replace('/', '_', base64_encode($note['ERPCode']))) ?>" class="btn btn-small btn-warning"><i class="icon-white icon-star"></i> Selesai Administrasi</a></p>
                        <?php
                    }
                    ?>


                    <p><a href="<?php echo base_url() ?>index.php/transaksi/daftar_transaksi_bpb" class="btn btn-large btn-inverse">Kembali</a></p>
                </div><!-- /.span4 -->
            </div><!-- /.row -->
        </div><!-- /.container -->
<?php
$this->load->view("include/footer");
?>
        <script type="text/javascript">
            $("#formcanceltask").hide();
            $('#editor').wysihtml5({
                "font-styles": true, //Font styling, e.g. h1, h2, etc. Default true
                "emphasis": true, //Italics, bold, etc. Default true
                "lists": true, //(Un)ordered lists, e.g. Bullets, Numbers. Default true
                "html": false, //Button which allows you to edit the generated HTML. Default false
                "link": true, //Button to insert a link. Default true
                "image": true, //Button to insert an image. Default true,
                "color": false //Button to change color of font  
            });
            
            function opendialog() {
                $( "#formcanceltask" ).dialog({ 
                    height: 380,
                    width: 550,
                    modal: true});
            };
        
        </script>
    </body>
</html>
