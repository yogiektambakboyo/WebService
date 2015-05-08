<!--
To change this template, choose Tools | Templates
and open the template in the editor.
-->
<!DOCTYPE html>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Cek Bin</title>
        <?php $this->load->view('include/header'); ?>
    </head>
    <body>
        <div class="container">
            <div class="row">
                <div class="span12 center">
                    <h1>Ambil Bin</h1>
                    <hr />
                    <?php 
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
                        
//                            var_dump($array['TransactionCode']);
//                            var_dump($this->session->userdata('transactionCode'));
                    ?>
                    
                    <form class="form-horizontal" method="POST" action="">
                        <input type="hidden" name="transactionCode" value="<?php echo $this->session->userdata('TransactionCode') ?>" />
                        <input type="hidden" name="kodeBin" value="<?php echo $this->session->userdata('BinCode') ?>" />
                        <input type="hidden" name="kodeSku" value="<?php echo $this->session->userdata('SkuCode') ?>" />
                        <input type="hidden" name="namaBrg" value="<?php echo $this->session->userdata('namaBrg') ?>" />
                        <input type="hidden" name="qtyAwal" value="<?php echo $this->session->userdata('qty') ?>" />
                        <input type="hidden" name="currRackSlot" value="<?php echo $this->session->userdata('rackAwal') ?>" />
                        <input type="hidden" name="destRackSlot" value="<?php echo $this->session->userdata('rackTujuan') ?>" />
                        <input type="hidden" name="expDate" value="<?php echo $this->session->userdata('expDate') ?>" />
                        
                        <div class="control-group">
                            <label id="label"><strong>Bin Awal</strong></label>:
                            <input type="text" class="input-medium" placeholder="Kode Bin Awal" name="binAwal" id="kodeBinAwal"/>
                        </div>
                        <div id="namaBarang"></div>
                        <div class="control-group">
                            <label id="label"><strong>Rack Awal</strong></label>:
                            <input type="text" class="input-medium" placeholder="Kode Rack Awal" name="rackAwal" />    
                        </div>
                        <div class="control-group">
                            <label id="label"><strong>Qty</strong></label>:
                            <input type="text" class="input-medium" placeholder="Quantity" name="qty" />
                        </div>
                        <div class="control-group">
                            <label id="label"><strong>Bin Tjn</strong></label>:
                            <input type="text" class="input-medium" placeholder="Kode Bin Tujuan" name="binTujuan" />
                        </div>
                        <div class="control-group">
                            <label id="label"><strong>Rack Tjn</strong></label>:
                            <input type="text" class="input-medium" placeholder="Kode Rack Tujuan" name="rackTujuan" />
                        </div>
                        
                        <div class="control-group">
                            <input class="btn btn-large" type="submit" name="btnProses" value="Proses" />
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <?php
            $this->load->view('include/footer');
        ?>
        <script type="text/javascript">
            $("#kodeBinAwal").change(function(){
                var kodeBin     = $("#kodeBinAwal").val();
                var postdata    = {'kodeBinAwal':kodeBin};
                
                if (kodeBin != ''){
                    var output  = $('#namaBarang');
                    var link    = "<?php echo base_url() ?>index.php/replenish/get_nama_barang";
                    $.ajax({
                        type        : 'POST',
                        url         : link,
                        dataType    : 'jsonp',
                        data        : postdata,
                        jsonp       : 'jsoncallback',
                        timeout     : 5000,
                        
                        success : function(data){
//                            var str="";
//                            $.each(data, function(i, item){
//                                str+='<div class=control-group><input type=text class=input-large name=skuCode readOnly value="'+item["SkuCode"]+'"/></div>';
//                                str+='<div class=control-group><input type=text class=input-large name=keterangan readOnly value="'+item["Keterangan"]+'"/></div>';
//                                str+='<div class=control-group><input type=text class=input-large name=rackSlot readOnly value="'+item["RackSlot"]+'"/></div>';
//                            });
//                            
//                            output.html(str);
                            var str="<div class=control-group>";
                            if (data.SkuCode != null){
                                str+='<strong>Sku Code : <font style=color:red>'+data.SkuCode+'</font></strong><br />';
                                str+='<strong>Qty Awal : <font style=color:red>'+data.Qty+'</font></strong><br />';
                                str+='<strong>Kode Rack Awal : <font style=color:red>'+data.RackSlotAwal+'</font></strong><br />';
//                                str+='<strong>Kode Rack Tujuan : <font style=color:red>'+data.RackSlotTujuan+'</font></strong><br />';
                            }else{
                                str+="<strong><font style=color:red>Data Tidak Ditemukan</font></strong>";
                            }
                                str+="</div>";
                                output.html(str);
                        
                            
                        }
                    });
                }
            });
        </script>
    </body>
</html>
