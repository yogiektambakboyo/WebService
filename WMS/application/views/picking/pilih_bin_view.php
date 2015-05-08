<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <title>Pilih Bin</title>
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
                    <h1>Pilih Bin</h1>
                    <hr />
                    <?php
                        echo validation_errors();
                    ?>
                    
                    <form class="form-horizontal" method="POST" action="<?php echo base_url() ?>index.php/picking/proses_pilih_bin">
			<div class="control-group">
                            <label id="label"><strong>Bin</strong></label> : <input type="text" class="input-medium" placeholder="Masukkan Kode Bin" name="kodebin" id="kodebin"/>
                            <div class="controls">
                                
                            </div>
                        </div>
                        <div class="control-group">
                            <input type="submit" name="btnProses" class="btn btn-large" value="Proses"/>
                            <input type="hidden" id="refreshed" value="no">
                            <div class="controls">
                                
                            </div>
                        </div>
                    </form>
                    <p><a href="<?php echo base_url() ?>index.php/picking/refresh_all_outstanding" class="btn btn-inverse  btn-large">Kembali</a></p>
                </div><!-- /.span4 -->
            </div><!-- /.row -->
        </div><!-- /.container -->
        <?php
        $this->load->view("include/footer");
        ?>
       
    </body>
</html>
