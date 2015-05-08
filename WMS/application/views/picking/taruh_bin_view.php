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
                    if (isset($error)) {
                        echo "<div class='alert alert-error'><strong>" . $error . "</strong><br /></div>";
                    }
                    ?>
                    <input type="text" readonly="readonly" class="input-large" placeholder="Kode Bin Sekarang" value="<?php echo $this->session->userdata('BinNow'); ?>" />
                    <br />
                    <form class="form-horizontal" method="POST" action="<?php echo base_url() ?>index.php/picking/taruh_bin">
                        <div class="control-group">
                            <label id="label"><strong>Bin Temp</strong></label> : <input type="text" class="input-medium" placeholder="Kode Bin yang Dibawa" name="kodebin" id="bin" maxlength="7"/>
                            <div class="controls">

                            </div>
                        </div>
                        <div class="control-group">
                            <label id="label"><strong>Rack Dest</strong></label> : <input type="text" class="input-medium" placeholder="Kode Rack Tujuan" name="koderack" id="rack" maxlength="8"/>
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
                    <p><a href="<?php echo base_url() ?>index.php/picking/list_my_outstanding" class="btn btn-inverse  btn-large">Kembali</a></p>
                </div><!-- /.span4 -->
            </div><!-- /.row -->
        </div><!-- /.container -->
        <?php
        $this->load->view("include/footer");
        ?>

    </body>
</html>
<script type="text/javascript">

	function changefocus(input, interval) {
                var previous = "";

                setInterval( function() {

                    var val = $("#bin").val();
                    if (  val != previous ) {
                        previous = val;
                        if ($("#bin").val().length == $("#bin").attr('maxLength')) {
                            $('#rack').focus();
                          }
                    }

                }, interval);
            }

            changefocus($("#bin").val(), 10);

</script>
