<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <title>Barcode Rack</title>
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
                    <h1>Barcode Rack</h1>
                    <form class="form-horizontal" method="POST" action="<?php echo base_url() ?>index.php/transaksi/simpan_destrack">
                        <table class="table table-bordered table-hover table-striped">
                            <thead>
                                <tr><td>Pilih</td><td>Nama Rack</td><td>Kode Rack</td></tr>
                                <tr><td></td><td><input type="text" name="fnama" id="fnama" /></td><td><input type="text" name="fkode" id="fkode" /></td></tr>
                            </thead>
                            <tbody>
                                <?php
                                foreach ($rack as $row) {
                                    ?>
                                    <tr>
                                        <td><input type="radio" name="rackslotcode" value="<?php echo $row['RackSlotCode'] ?>" 
                                            <?php
                                            if ($row['RackSlotCode'] == $DestRackSlot) {
                                                ?>
                                                       checked="checked"
                                                       <?php
                                                   }
                                                   ?>
                                                   /></td>
                                        <td><?php echo $row['RackName'] ?></td>
                                        <td><?php echo $row['RackSlotCode'] ?></td>
                                    </tr>
                                    <?php
                                }
                                ?>
                            </tbody>
                        </table>
                        <div class="control-group">
                            <input type="hidden" name="TransactionCode" value="<?php echo $TransactionCode; ?>"/>
                            <input type="hidden" name="NoUrut" value="<?php echo $NoUrut; ?>"/>
                            <input type="submit" name="btnPilih" class="btn btn-large" value="Pilih" />
                            <div class="controls">

                            </div>
                        </div>
                    </form>
                    <p><a href="<?php echo base_url() ?>index.php/transaksi/detail_transaksi_bpb/<?php echo str_replace('=', '-', str_replace('/', '_', base64_encode($TransactionCode))) ?>" class="btn btn-large btn-inverse">Kembali</a></p>
                </div><!-- /.span4 -->
            </div><!-- /.row -->
        </div><!-- /.container -->

        <?php
        $this->load->view("include/footer");
        ?>

        <script type="text/javascript">
            $("#fnama").keyup(function(){
                $("#fnama").val($("#fnama").val().toUpperCase());
           
                var racknama=$("#fnama").val();
                var rackkode=$("#fkode").val();
           
                var postdata = {'racknama':racknama,'rackkode':rackkode};
                var output = $("tbody");
                var link = "<?php echo base_url() ?>index.php/barcode/get_ajax_rack";
                $.ajax({
                    type: 'POST',
                    url: link,
                    dataType: 'jsonp',
                    data: postdata,
                    jsonp: 'jsoncallback',
                    timeout: 5000,
                    success: function(data){
                        var str;
                        $.each(data, function(i,item){
                            str = str +'<tr><td><input type="radio" name="rackslotcode" value="'+item["RackSlotCode"]+'" /></td><td>'+item["RackName"]+'</td><td>'+item["RackSlotCode"]+'</td></tr>';
                        });
                        //output.html('<tr><td></td><td><input type="hidden" name="rackslotcode[]" value="" /></td></tr>'); 
                        output.html(str); 
                    }
                    /*error: function (xhr, ajaxOptions, thrownError) {
                         alert(xhr.responseText);
                         alert(thrownError);
                     }
                     error: function(){
                             //alert(data,null,'Error','Ok');
                             alert('There is a problem',null,'Error','Ok');
                     }*/
                });
            });
            $("#fkode").keyup(function(){
                $("#fnama").val($("#fnama").val().toUpperCase());
           
                var racknama=$("#fnama").val();
                var rackkode=$("#fkode").val();
           
                var postdata = {'racknama':racknama,'rackkode':rackkode};
                var output = $("tbody");
                var link = "<?php echo base_url() ?>index.php/barcode/get_ajax_rack";
                $.ajax({
                    type: 'POST',
                    url: link,
                    dataType: 'jsonp',
                    data: postdata,
                    jsonp: 'jsoncallback',
                    timeout: 5000,
                    success: function(data){
                        var str;
                        $.each(data, function(i,item){
                            str = str +'<tr><td><input type="radio" name="rackslotcode" value="'+item["RackSlotCode"]+'" /></td><td>'+item["RackName"]+'</td><td>'+item["RackSlotCode"]+'</td></tr>';
                        });
                        //output.html('<tr><td></td><td><input type="hidden" name="rackslotcode[]" value="" /></td></tr>'); 
                        output.html(str); 
                    }
                    /*error: function (xhr, ajaxOptions, thrownError) {
                         alert(xhr.responseText);
                         alert(thrownError);
                     }
                     error: function(){
                             //alert(data,null,'Error','Ok');
                             alert('There is a problem',null,'Error','Ok');
                     }*/
                });
            });
        </script>

    </body>
</html>

