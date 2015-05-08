<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <title>Generate Bin</title>
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
                    <h1>Generate Barcode Bin</h1>
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
                    <form class="form-horizontal" method="POST" action="<?php echo base_url() ?>index.php/barcode/generate_bin">
                        <div class="container">
                            <div class="row">
                                <div class="span12 center">
                                <div class="control-group">
                                    <input type="text" class="input-large" placeholder="Kode Bin Awal" name="kodebin1"/>
                                    <div class="controls">

                                    </div>
                                </div>
                                <div class="control-group">
                                    Sampai
                                    <div class="controls">

                                    </div>
                                </div>
                                 <div class="control-group">
                                    <input type="text" class="input-large" placeholder="Kode Bin Akhir" name="kodebin2"/>
                                    <div class="controls">

                                    </div>
                                </div>
                                <div class="control-group">
                                    <select name="type">
                                        <?php
                                        foreach($type as $row){   
                                        ?>
                                            <option value="<?php echo $row['BinTypeCode'] ?>" ><?php echo $row['BinTypeName'] ?></option>
                                        <?php
                                        }
                                        ?>
                                    </select>
                                    <div class="controls">

                                    </div>
                                </div>
                                <div class="control-group">
                                    <input type="submit" name="btnGenerate" class="btn btn-large" value="Generate"/>
                                    <div class="controls">

                                    </div>
                                </div>
                                </div><!-- /.span4 -->
                            </div><!-- /.row -->
                        </div><!-- /.container -->
                    </form>
                    </div><!-- /.span4 -->
                </div><!-- /.row -->
            </div><!-- /.container -->
         
        <?php
        $this->load->view("include/footer");
        ?>
    </body>
</html>

