<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <title>Daftar Semua Outstanding Storing</title>
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
                    <h1>Daftar Semua Outstanding Storing</h1>
                    <hr />
                    <div class="alert alert-error">Bin Tidak Ditemukan</div>
                    <div class="control-group">
                        <label id="label"><strong>Bin</strong></label> : <input type="text" class="input-medium" id="BinCode" placeholder="Masukkan palet" maxlength="7"/>
                    </div>
                    <div class="control-group">
                        <table class="table table-bordered table-hover table-striped">
                            <thead>
                                <tr>
                                    <th>Kode Nota</th>
                                    <th>Kode Bin</th>
                                    <th>Nama Barang</th>
                                    <th>Rak Sekarang</th>
                                    <th>Jumlah</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                foreach ($outstanding as $row) {
                                    ?>
                                        <!--tr onclick="document.getElementById('myForm<?php echo $i; ?>').submit();"-->
                                    <tr>
                                        <td><?php echo $row['ERPCode'] ?></td>
                                        <td><?php echo $row['BinCode'] ?></td>
                                        <td><?php echo $row['Keterangan'] ?></td>
                                        <td><?php echo $row['CurrRackSlot'] ?></td>
                                        <td><?php echo $row['Qtykonversi'] ?>
                                            <form method="POST" action="<?php echo base_url() ?>index.php/storing/proses_ambil_bin" id="myForm<?php echo $row['BinCode']; ?>">
                                                <input type="hidden" name="TransactionCode" value="<?php echo $row['TransactionCode'] ?>" />
                                                <input type="hidden" name="QueueNumber" value="<?php echo $row['QueueNumber'] ?>" />
                                                <input type="hidden" name="NoUrut" value="<?php echo $row['NoUrut'] ?>" />
                                                <input type="hidden" name="Keterangan" value="<?php echo $row['Keterangan'] ?>" />
                                                <input type="hidden" name="ERPCode" value="<?php echo $row['ERPCode'] ?>" />
                                                <input type="hidden" name="BinCode" value="<?php echo $row['BinCode'] ?>" />
                                                <input type="hidden" name="Ratio" value="<?php echo $row['Ratio'] ?>" />
                                                <input type="hidden" name="RatioName" value="<?php echo $row['RatioName'] ?>" />
                                                <input type="hidden" name="Qty" value="<?php echo $row['Qty'] ?>" />
                                                <input type="hidden" name="SKUCode" value="<?php echo $row['SKUCode'] ?>" />
                                                <input type="hidden" name="DestRackSlot" value="<?php echo $row['DestRackSlot'] ?>" />
                                            </form>
                                        </td>

                                    </tr>
                                    <?php
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                    <div class="container">
                        <div class="row">
                            <div class="span12 center">
                                <input type="hidden" id="refreshed" value="no">
                                <p><a href="<?php echo base_url() ?>index.php/storing/back_tambah_bpb_storing" class="btn btn-large btn-primary">Refresh</a></p>
                                <p><a href="<?php echo base_url() ?>index.php/storing/list_my_outstanding" class="btn btn-large">Outstanding ku</a></p>
                                <?php
                                if ($this->session->userdata('ProjectCode') == 'BPB') {
                                    ?>
                                    <p><a href="<?php echo base_url() ?>index.php/storing/tambah_bpb_storing" class="btn btn-inverse btn-large">Kembali</a></p>
                                    <?php
                                } else {
                                    ?>
                                    <p><a href="<?php echo base_url() ?>index.php/storing/tambah_retur_storing" class="btn btn-inverse btn-large">Kembali</a></p>
                                    <?php
                                }
                                ?>
                            </div><!-- /.span4 -->
                        </div><!-- /.row -->
                    </div><!-- /.container -->

                </div><!-- /.span4 -->
            </div><!-- /.row -->
        </div><!-- /.container -->
        <?php
        $this->load->view("include/footer");
        ?>
        <script type="text/javascript">
            $( document ).ready(function() {
                $('#BinCode').focus();
                $('.alert').hide();
            });
            function gotoambilbin(input, interval) {
                var previous = "";

                setInterval( function() {

                    var val = $("#BinCode").val();
                    if (  val != previous ) {
                        previous = val;
                        if ($("#BinCode").val().length == $("#BinCode").attr('maxLength')) {
                            if (! $('#myForm'+val).length){
                                $('.alert').show();
                            }
                            else{
                                $('#myForm'+val).submit();
                            }    
                        }
                    }

                }, interval);
            }

            gotoambilbin($("#BinCode").val(), 10);
            
            function refreshoutstanding(interval) {
                setInterval( function() {
                        var link = "<?php echo base_url() ?>index.php/storing/get_ajax_daftar_all_outstanding";
                        $.ajax({
                            type: 'POST',
                            url: link,
                            dataType: 'jsonp',
                            jsonp: 'jsoncallback',
                            timeout: 5000,
                            success: function(data){
                                var str=''
                                $.each(data, function(i,item){
                                    str+='<tr>';
                                    str+='<td>'+item['ERPCode']+'</td>';
                                    str+='<td>'+item['BinCode']+'</td>';
                                    str+='<td>'+item['Keterangan']+'</td>';
                                    str+='<td>'+item['CurrRackSlot']+'</td>';
                                    str+='<td>'+item['Qtykonversi'];
                                    str+='<form method="POST" action="<?php echo base_url() ?>index.php/storing/proses_ambil_bin" id="myForm'+item['BinCode']+'">';
                                    str+='<input type="hidden" name="TransactionCode" value="'+item['TransactionCode']+'" />';
                                    str+='<input type="hidden" name="QueueNumber" value="'+item['QueueNumber']+'" />';
                                    str+='<input type="hidden" name="NoUrut" value="'+item['NoUrut']+'" />';
                                    str+='<input type="hidden" name="Keterangan" value="'+item['Keterangan']+'" />';
                                    str+='<input type="hidden" name="ERPCode" value="'+item['ERPCode']+'" />';
                                    str+='<input type="hidden" name="BinCode" value="'+item['BinCode']+'" />';
                                    str+='<input type="hidden" name="Ratio" value="'+item['Ratio']+'" />';
                                    str+='<input type="hidden" name="RatioName" value="'+item['RatioName']+'" />';
                                    str+='<input type="hidden" name="Qty" value="'+item['Qty']+'" />';
                                    str+='<input type="hidden" name="SKUCode" value="'+item['SKUCode']+'" />';
                                    str+='<input type="hidden" name="DestRackSlot" value="'+item['DestRackSlot']+'" />';
                                    str+='</form>';
                                    str+='</td>';
                                    str+='</tr>';
                                });
                                $("tbody").html(str)
                            },
                            /*error: function (xhr, ajaxOptions, thrownError) {
                                alert(xhr.responseText);
                                alert(thrownError);
                            }*/
                            error: function(){
                                $("tbody").html('<tr><td></td><td></td><td></td><td></td><td></td></tr>');
                            }
                        }); 
                }, interval);
            }

            refreshoutstanding(60000);
        </script>
    </body>
</html>


