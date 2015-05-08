<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <title>Generate Barcode Bin</title>
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
                    if ($this->session->flashdata('pesan')) {
                        ?>
                        <div class="alert alert-success">
                            <?php
                            echo $this->session->flashdata('pesan');
                            ?>
                        </div>
                        <?php
                    }
                    if(isset($error))
                    {
                        ?>
    
                        <div class="alert alert-error">
                            <?php
                            echo $error;
                            ?>
                        </div>
                    <?php
                    }
                    ?>
                    <form class="form-horizontal" method="POST" action="<?php echo base_url() ?>index.php/barcode/generate_barcode">
                        <div class="container">
                            <div class="row">
                                <div class="span12 center">
                                    <div class="control-group">
                                        <label id="label"><strong>Gang</strong></label> : <input type="text" class="input-large" placeholder="Gang" name="gang"/>
                                        <div class="controls">

                                        </div>
                                    </div>

                                    <div class="control-group">
                                        <label id="label"><strong>Kanan/Kiri</strong></label> : 
                                        <select name="kanankiri" >
                                            <option value="R">Kanan</option>
                                            <option value="L">Kiri</option>
                                        </select>
                                        <div class="controls">

                                        </div>
                                    </div>
                                    <div class="control-group">
                                        <label id="label"><strong>Kolom Awal</strong></label> : <input type="text" class="input-large" placeholder="Kolom Awal" name="kolom1"/>
                                        <div class="controls">

                                        </div>
                                    </div>
                                    <div class="control-group">
                                        <label id="label"><strong>Kolom Akhir</strong></label> : <input type="text" class="input-large" placeholder="Kolom Akhir" name="kolom2"/>
                                        <div class="controls">

                                        </div>
                                    </div>
                                    <div class="control-group">
                                        <label id="label"><strong>Level Awal</strong></label> : <input type="text" class="input-large" placeholder="Level Awal" name="level1"/>
                                        <div class="controls">

                                        </div>
                                    </div>
                                    <div class="control-group">
                                        <label id="label"><strong>Level Akhir</strong></label> : <input type="text" class="input-large" placeholder="Level Akhir" name="level2"/>
                                        <div class="controls">

                                        </div>
                                    </div>
                                    <div class="control-group">
                                        <label id="label"><strong>Tipe</strong></label> : 
                                        <select name="tipe" >
                                            <option value="G">General</option>
                                            <option value="H">Half</option>
                                            <option value="S">Self</option>
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
                    <p><a href="<?php echo base_url() ?>index.php/barcode" class="btn btn-large btn-inverse">Kembali</a></p>
                </div><!-- /.span4 -->
            </div><!-- /.row -->
        </div><!-- /.container -->

        <?php
        $this->load->view("include/footer");
        ?>
    </body>
</html>

