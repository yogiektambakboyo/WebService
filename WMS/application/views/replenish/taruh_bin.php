<!--
To change this template, choose Tools | Templates
and open the template in the editor.
-->
<!DOCTYPE html>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <meta name='viewport' content='width=device-width, initial-scale=1.0'>
        <title>Pindah Barang</title>
        <?php
            $this->load->view("include/header");
        ?>
    </head>
    <body>
        <div class='container'>
            <div class='row'>
                <div class='span12 center'>
                    <h1>Taruh Bin</h1>
                    <hr />
                    <?php 
                            //var_dump($keterangan);
                        echo validation_errors();
                        if (isset($error)){
                            echo $error;
                        }
                         if ($this->session->flashdata('error')){
                                ?>
                                <div class="alert alert-error">
                                    <strong><?php echo $this->session->flashdata('error'); ?></strong>
                                </div>
                            <?php
                            
                        }
                    ?>
                    <label>
                        Kode Bin :
                        <strong>
                            <?php
                            echo $this->session->userdata('binAwal');
                            ?>
                        </strong>
                    </label>
                    <label>
                        Barang :
                        <strong>
                            <?php
                            echo $this->session->userdata('ket');
                            ?>
                        </strong>
                    </label>
                    <label>
                        Dest Rack :
                        <strong>
                            <?php
                            echo $this->session->userdata('rackTjnNama');
                            ?>
                        </strong>
                    </label>
                    <form class="form-horizontal" method="POST" action="">
                       <div class="control-group">
                            <label id="label"><strong>Bin</strong></label> : <input type="text" class="input-medium" placeholder="Masukkan Kode Bin" name="binAwal" id="kodebin"/>
                            <div class="controls">

                            </div>
                        </div>
                        <div class="control-group">
                            <label id="label"><strong>Rack Dest</strong></label> : <input type="text" class="input-medium" id="RackSlotCode" placeholder="Masukkan Kode Rack" name="rackTjn"/>
                            <span id="RackName"></span>
                            <div class="controls">

                            </div>
                        </div>
                        
						
						<input type="hidden" name="trCode" value="<?php echo $this->session->userdata('trCode')  ?>" />
						<input type="hidden" name="SrcBin" value="<?php echo $this->session->userdata('binAwal') ?>" />
						<input type="hidden" name="NoUrut" value="<?php echo $this->session->userdata('NoUrut'); ?>" />
                                                <input type="hidden" name="QueueNumber" value="<?php echo $this->session->userdata('QueueNumber'); ?>" />
						<input type="hidden" name="DestBin" value="<?php echo $this->session->userdata('binTjn')?>" />
						<input type="hidden" name="DestRack" value="<?php echo $this->session->userdata('rackTjnKode')?>" />
						<input type="hidden" name="quntity" value="<?php echo $this->session->userdata('qty') ?>" />
						
						<div class='control-group'>
                            <p><input type='submit' name='btnProses' class='btn btn-large' value='Proses' /></p>
							<p><a href="<?php echo base_url() ?>index.php/replenish/my_outstanding" class="btn btn-large btn-inverse">Kembali</a></p>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <?php
            $this->load->view("include/footer");
        ?>
        
    </body>
</html>
