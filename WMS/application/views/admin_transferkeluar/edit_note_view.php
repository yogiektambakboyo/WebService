<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <title>Edit Note</title>
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
                    <h1>Edit Note</h1>
                    <hr />
                    <?php
                    if (isset($error)) {
                        echo "<div class='alert alert-error'><strong>" . $error . "</strong><br /></div>";
                    }
                    ?>
                    <?php
                    echo "<strong>No. Transaksi: </strong>" . $note['TransactionCode'] . "<br />";
                    echo "<strong>No. Nota: </strong>" . $note['ERPCode'] . "<br />";
                    echo "<strong>Kode Bin: </strong>" . $note['BinCode'] . "<br />";
                    ?>
                    <form method="POST" action="<?php echo base_url() ?>index.php/admin_transferkeluar/simpan_note" >
                        <textarea name="note" id="editor" rows="10" cols="100" placeholder="Enter text ..."><?php echo $note['Note']; ?></textarea>
                        <input type="hidden" name="TransactionCode" value="<?php echo $note['TransactionCode']; ?>" />
                        <input type="hidden" name="NoUrut" value="<?php echo $note['NoUrut']; ?>" />
                        <br/>
                        <?php
                        if ($Status2 == 0) {
                            ?>

                            <input type="submit" name="btnSimpan" value="Simpan" class="btn btn-large" />
                            <?php
                        }
                        ?> 
                    </form>
                    <p><a href="<?php echo base_url() ?>index.php/admin_transferkeluar/detail_transaksi_pck/<?php echo str_replace('=', '-', str_replace('/', '_', base64_encode($note['TransactionCode']))) ?>" class="btn btn-large btn-inverse">Kembali</a></p>
                </div><!-- /.span4 -->
            </div><!-- /.row -->
        </div><!-- /.container -->
        <?php
        $this->load->view("include/footer");
        ?>
        <script type="text/javascript">
            $('#editor').wysihtml5({
                "font-styles": true, //Font styling, e.g. h1, h2, etc. Default true
                "emphasis": true, //Italics, bold, etc. Default true
                "lists": true, //(Un)ordered lists, e.g. Bullets, Numbers. Default true
                "html": false, //Button which allows you to edit the generated HTML. Default false
                "link": true, //Button to insert a link. Default true
                "image": true, //Button to insert an image. Default true,
                "color": false //Button to change color of font  
            });
        </script>
    </body>
</html>
