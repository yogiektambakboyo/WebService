<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <title>Daftar Shipping Kembali</title>
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
                    <h1>Daftar Shipping Kembali</h1>
                    <hr />
                    <?php
                    if ($this->session->flashdata('error')) {
                        ?>
                        <div class="alert alert-error">
                            <strong>
                                <?php
                                echo $this->session->flashdata('error');
                                ?>
                            </strong>
                        </div>
                        <?php
                    }
                    if ($this->session->flashdata('pesan')) {
                        ?>
                        <div class="alert alert-success">
                            <?php
                            echo $this->session->flashdata('pesan');
                            ?>
                        </div>
                        <?php
                    }
                    ?>
                    <strong>No. PickList</strong> : <input type="text" readonly="readonly" class="input-large" placeholder="PickList" value="<?php echo $this->session->userdata('ERPCode'); ?>" />
                    <br />
                    <div class="control-group">
                        <!-- div class="scroll2" -->
                        <form class="form-horizontal" method="POST" action="<?php echo base_url() ?>index.php/shipping/kembali_barang">
                            <table class="table table-bordered table-hover table-striped table-condensed">
                                <thead>
                                    <tr>
                                        <td>&nbsp;</td>
                                        <th>Bin</th>
                                        <th>Nama Barang</th>
                                        <th>Exp Date</th>
                                        <th>jumlah</th>
                                        <!--th>Proses</th-->
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $i = 0;
                                    foreach ($listsku as $row) {
                                        ?>

                                        <tr>
                                            <td style="text-align: center"><input type="radio" name="NoUrut" value="<?php echo $row['NoUrut']."~".$row['ExpDate']."~".$row['BinCode']."~".$row['SKUCode']."~".$row['QtyNeedNow']."~".$row['Keterangan']."~".$row['WHCode']."~".$row['Status2']  ?>" /></td>
                                            <td><?php echo $row['BinCode'] ?></td>
                                            <td><?php echo $row['Keterangan'] ?></td>
                                            <td><?php echo date("d-m-Y", strtotime($row['ExpDate'])) ?></td>
                                            <td><?php echo $row['QtyNeedNow'] ?>
                                                <input type="hidden" name="ExpDate" id="ExpDate" value="<?php echo $row['ExpDate'] ?>" />
                                                <input type="hidden" name="SKUCode" id="SKUCode" value="<?php echo $row['SKUCode'] ?>" />
                                                <input type="hidden" name="TransactionCode" id="TransactionCode" value="<?php echo $row['TransactionCode'] ?>" />
                                                <input type="hidden" name="BinCode" id="BinCode" value="<?php echo $row['BinCode'] ?>" />
                                            </td>
                                        </tr>
                                        <?php
                                        $i++;
                                    }
                                    ?>
                                </tbody>
                            </table>
                            <div class="control-group">
                                <input type="hidden" id="refreshed" value="no">
                                <input type="submit" name="btnProses" class="btn btn-large" value="Proses"/>
                                <div class="controls">

                                </div>
                            </div>
                        </form>
                        <p><a href="<?php echo base_url() ?>index.php/shipping/tambahbrgkembali" class="btn btn-large btn-warning"><i class="icon-warning-sign"></i> Tambah Barang Kembali</a></p>
                        <div id="suggestion"></div>
                    </div>
                    <div class="container">
                        <div class="row">
                            <div class="span12 center">
                                <p><a href="<?php echo base_url() ?>index.php/shipping/gotomainmenu" class="btn btn-large btn-inverse">Kembali</a></p>
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
            $('tr').click(
            function() {
                $('input[type=radio]',this).prop('checked', true);
                var ExpDate=$(this).find('#ExpDate').val();
                var BinCode=$(this).find('#BinCode').val();
                var SKUCode=$(this).find('#SKUCode').val();
                var TransactionCode=$(this).find('#TransactionCode').val();
                var postdata = {'TransactionCode':TransactionCode, 'SKUCode':SKUCode,'BinCode':BinCode,'ExpDate':ExpDate};
                
                var output = $('#suggestion');
                
                var link = "<?php echo base_url() ?>index.php/shipping/get_ajax_suggestionkembali";
                $.ajax({
                    type: 'POST',
                    url: link,
                    dataType: 'jsonp',
                    data: postdata,
                    jsonp: 'jsoncallback',
                    timeout: 5000,
                    success: function(data){
                        var str='<h4>Asal</h4><table class="table table-bordered table-hover table-striped table-condensed"><thead><th>Barang</th><th>RackSlot</th><th>Kode Bin</th></thead><tbody>';
                        $.each(data, function(i,item){
                            str = str +'<tr><td>'+item["Keterangan"]+'</td><td>'+item["SrcRackSlot"]+'</td><td>'+item["SrcBin"]+'</td></tr>';
                        });
                        str+='</tbody></table>';
                        output.html(str); 
                    }/*,
                    error: function (xhr, ajaxOptions, thrownError) {
                                alert(xhr.responseText);
                                alert(thrownError);
                            }
                            error: function(){
                                    //alert(data,null,'Error','Ok');
                                    alert('There is a problem',null,'Error','Ok');
                            }*/
                });
                
            }
        );    
        </script>
    </body>
</html>


