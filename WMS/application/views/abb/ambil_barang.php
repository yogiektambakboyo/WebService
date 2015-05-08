<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <title>Ambil Barang</title>
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
                    <h1>Ambil Barang</h1>
                    <hr />
                    <?php
                    echo validation_errors();
                    if (isset($error)) {
                        echo "<div class='alert alert-error'><strong>" . $error . "</strong><br /></div>";
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
                        Rack Src :
                        <strong>
                            <?php 
                            echo $this->session->userdata('rackNm');
                            // echo '<br>'.$this->session->userdata('noUrut');
                            ?>
                        </strong>
                    </label>
                    <label>
                        Bin Src :
                        <strong>
                            <?php
                            echo $this->session->userdata('binSrc');
                            ?>
                        </strong>
                    </label>
                    <label>
                        Qty Harus Diambil :
                        <strong>
                            <?php
                            echo $this->session->userdata('qtyKonv');
                            ?>
                        </strong>
                    </label>
                    <form class="form-horizontal" method="POST" action="">
                        <div class="control-group">
                            <label id="label"><strong>Rack Src</strong></label> : 
							<input type="text" id="RackSlotCode" class="input-medium" placeholder="Masukkan Kode Rack" name="rackSrc"/>
							<span id="ajaxLoader" style="display:none"><img src="<?php echo base_url() ?>files/css/ui-lightness/images/ajax-loader2.gif"></span><span id="RackName"></span>
							<input type="hidden" name="cekRackSrc" value="<?php echo $this->session->userdata('rackSrc') ?>"/>
                        </div>
						<div class="control-group">
                            <label id="label"><strong>Bin Scr</strong></label> : 
							<input type="text" class="input-medium" placeholder="Masukkan Kode Bin" name="binSrc"/>
							<input type="hidden" name="cekBinSrc" value="<?php echo $this->session->userdata('binSrc') ?>"/>
                        </div>
						<div class="control-group">
                            <label id="label"><strong>Bin Dest</strong></label> :
							<input type="text" class="input-medium" placeholder="Masukkan Kode Bin" name="binDest"/>
                        </div>
                        <div class="control-group">
                            <label id="label"><strong>Qty</strong></label> : 
							<input type="text" class="input-small" placeholder="Jumlah Barang" name="qty" value=""/>
                            <select name="satuan" class="input-small">
                                <?php
                                foreach ($satuan as $row) {
                                    ?>
                                    <option value="<?php echo $row['Rasio']; ?>"><?php echo $row['satuan']; ?></option>
                                        <?php } ?>    
                            </select>
                        </div>
						<div class="control-group">
                            <input type="hidden" id="refreshed" value="no">
                            <input type="submit" name="btnProses" class="btn btn-large" value="Proses"/>
                        </div>
                    </form>
                    <p><a href="<?php echo base_url() ?>index.php/abb" class="btn btn-large btn-inverse">Kembali</a></p>
                </div><!-- /.span4 -->
            </div><!-- /.row -->
        </div><!-- /.container -->
        <?php
        $this->load->view("include/footer");
        ?>
		<script type="text/javascript">
		function detectChangeRack(input, interval) {
                var previous = "";

                setInterval( function() {

                    var val = $("#RackSlotCode").val();
                    if (  val != previous ) {
                        previous = val;
                        var postdata = {'RackSlotCode':val};
                        var link = "<?php echo base_url() ?>index.php/abb/get_ajax_rack";
						$("#ajaxLoader").show();
                        $.ajax({
                            type: 'POST',
                            url: link,
                            dataType: 'jsonp',
                            data: postdata,
                            jsonp: 'jsoncallback',
                            timeout: 5000,
                            success: function(data){
                                
                                $("#RackName").html(data.RackName);  
								$("#ajaxLoader").hide();								
                            },
                            /*error: function (xhr, ajaxOptions, thrownError) {
                                alert(xhr.responseText);
                                alert(thrownError);
                            }*/
                            error: function(){
                                $("#RackName").html('');  
                            }
                        });   
                        
                    }

                }, interval);
            }

            detectChangeRack($("#RackSlotCode").val(), 10);
		</script>
    </body>
</html>
