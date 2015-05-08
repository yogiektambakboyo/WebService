<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <title>Detail <?php echo $Keterangan; ?></title>
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
                    <h1>Detail <?php echo $Keterangan; ?></h1>
                    <hr />
                    <?php
                    echo validation_errors();
                    if(isset($error))
                    {
                        echo "<div class='alert alert-error'><strong>".$error."</strong><br /></div>";
                    }
                    ?>
                    <div class="control-group">
                        <label id="label"><strong>Asal</strong></label> : <input type="text" readonly="readonly" class="input-small" placeholder="Kode Bin Asal" value="<?php echo $BinCode; ?>" />
                        <div class="controls">

                        </div>
                    </div>
                    <div class="control-group">
                        <label id="label"><strong>Tujuan</strong></label> : <input type="text" readonly="readonly" class="input-small" placeholder="Kode Bin Tujuan" value="<?php echo $this->session->userdata('kodebindest'); ?>" />
                        <div class="controls">

                        </div>
                    </div>
                    <div class="control-group">
                        <label id="label"><strong>Qty di Bin</strong></label> : <input type="text" readonly="readonly" class="input-small" placeholder="Jumlah di Bin" value="<?php echo $QtyNeedNow; ?>" />
                        <div class="controls">

                        </div>
                    </div>
                    
                    <br />
                    <div class="control-group">
                        <!-- div class="scroll2" -->
                        <form class="form-horizontal" method="POST" action="<?php echo base_url() ?>index.php/shipping/proses_masuk_barang">
                            <table class="table table-bordered table-hover table-striped table-condensed">
                                <thead>
                                    <tr>
                                        <th>Exp Date</th>
                                        <th>Jumlah</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $i = 0;
                                    $jumlah = 0;
                                    foreach ($listsku as $row) {
                                        ?>

                                        <tr>
                                            <td><?php echo date("d-m-Y", strtotime($row['ExpDate'])) ?></td>
                                            <td><input type="hidden" name="QtyNeedNow[]" value="<?php echo $row['QtyNeedNow'] ?>" />
                                                <input type="hidden" name="NoUrut[]" value="<?php echo $row['NoUrut'] ?>" />
                                                <input type="text" class="input-small" name="Qty[]" id="<?php echo "Qty" . $i; ?>" value="<?php echo $row['QtyNeedNow'] ?>" onchange="gettotal()"/>
                                            </td>
                                        </tr>
                                        <?php
                                        $jumlah+=$row['QtyNeedNow'];
                                        $i++;
                                    }
                                    ?>
                                <input type="hidden" name="TransactionCode" value="<?php echo $TransactionCode ?>" />
                                <input type="hidden" name="SKUCode" value="<?php echo $SKUCode ?>" />
                                <input type="hidden" name="Keterangan" value="<?php echo $Keterangan ?>" />
                                <input type="hidden" name="BinCode" value="<?php echo $BinCode ?>" />

                                </tbody>
                                <tfoot>
                                <th>Total</th>
                                <th><div id="totalqty"><?php echo $QtyNeedNow; ?></div></th>
                                </tfoot>
                            </table>
                            <div class="control-group">
                                <input type="submit" name="btnProses" class="btn btn-large" value="Proses"/>
                            </div>
                        </form>
                        <!--/div -->
                    </div>
                    <div class="container">
                        <div class="row">
                            <div class="span12 center">
                                <p><a href="<?php echo base_url() ?>index.php/shipping/list_sku_bin" class="btn btn-large btn-inverse">Kembali</a></p>
                            </div><!-- /.span4 -->
                        </div><!-- /.row -->
                    </div><!-- /.container -->
                    <input type="hidden" id="refreshed" value="no">
                </div><!-- /.span4 -->
            </div><!-- /.row -->
        </div><!-- /.container -->
        <?php
        $this->load->view("include/footer");
        ?>
        <script type="text/javascript">
            var countqty=<?php echo $i; ?>;
            
            
            function gettotal()
            {
                var SKUCode='<?php echo $SKUCode; ?>';
                var i=0;
                var total=0;
                for(i=0;i<countqty;i++)
                {
                    total+=parseFloat($('#Qty'+i).val());
                }
                var link='<?php echo base_url() ?>index.php/shipping/get_ajax_konversiqty';
                var postdata = {
                    "Total": total,
                    "SKUCode": SKUCode
                };
                $.ajax({
                    type: 'POST',
                    url: link,
                    dataType: 'jsonp',
                    data: postdata,
                    jsonp: 'jsoncallback',
                    timeout: 5000,
                    success: function(data){
                        $('#totalqty').html(data.Qtykonversi);      
                    },
                    /*error: function (xhr, ajaxOptions, thrownError) {
                                alert(xhr.responseText);
                                alert(thrownError);
                            }*/
                    error: function(){
                       $('#totalqty').html(0); 
                    }
                });
                
            }
            
        </script>
    </body>
</html>


