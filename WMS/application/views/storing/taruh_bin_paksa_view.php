<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <title>Taruh Bin</title>
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
                    <h1>Taruh Bin</h1>
                    <hr />
                    <?php
                    echo validation_errors();
                    if(isset($error))
                    {
                        echo "<div class='alert alert-error'><strong>".$error."</strong><br /></div>";
                    }
                    ?>
                    <form class="form-horizontal" method="POST" action="<?php echo base_url() ?>index.php/storing/proses_taruh_bin">
                        <div class="control-group">
                            <label id="label"><strong>Bin</strong></label> : <input type="text" class="input-medium" readonly="readonly" placeholder="Masukkan Kode Bin" name="kodebin" id="kodebin" value="<?php echo $kodebin; ?>"/>
                            <div class="controls">
                                
                            </div>
                        </div>
                        <div class="control-group">
                            <select name="jenisbarang" readonly="readonly"><option value="<?php echo $jenisbarang; ?>"><?php echo $keterangan; ?></option></select>
                            
                            <div class="controls">
                                
                            </div>
                        </div>
                        <div id="informationbin">
                        </div>
                        <div class="control-group">
                            <label id="label"><strong>Rack Dest</strong></label> : <input type="text" class="input-medium" placeholder="Masukkan Kode Rack" name="koderack"/>
                            <div class="controls">
                                
                            </div>
                        </div>
			<div class="control-group">
                            <label id="label"><strong>Qty</strong></label> : <input type="text" class="input-medium" placeholder="Jumlah Barang" name="jumlah" id="jumlah" value="<?php echo $jumlah; ?>" />
                            <span id="satuan"><?php echo $satuan; ?></span>
                            <div class="controls">
                             
                            </div>
                        </div>
			<div class="control-group">
                            <select name="isonaisle">
                                <option value="0">- Apakah di Gang? -</option>
                                <option value="1">Ya</option>
                                <option value="2">Tidak</option>
                            </select>
                        </div>
                        <div class="control-group">
                            <label id="label"><strong>Bin Dest</strong></label> : <input type="text" class="input-medium" placeholder="Kode Bin Tujuan" name="kodebindest" />
                            <div class="controls">
                                
                            </div>
                        </div>
                        <input type="hidden" name="asal" value="<?php echo $asal; ?>"/>
                        <div class="control-group">
                            <input type="hidden" id="refreshed" value="no">
                            <input type="submit" name="btnProses" class="btn btn-large" value="Proses"/>
                            <div class="controls">
                                
                            </div>
                        </div>
                    </form>
                    <?php 
                        if($asal=="ambil_bin")
                        {
                    ?>
                    <p><a href="<?php echo base_url() ?>index.php/storing/ambil_bin" class="btn btn-large">Kembali</a></p>
                    <?php
                        }
                        else {
                    ?>
                    <p><a href="<?php echo base_url() ?>index.php/storing/list_my_outstanding" class="btn btn-inverse btn-large">Kembali</a></p>
                    <?php
                        }
                     ?>
                </div><!-- /.span4 -->
            </div><!-- /.row -->
        </div><!-- /.container -->
        <?php
        $this->load->view("include/footer");
        ?>
      
    </body>
</html>
