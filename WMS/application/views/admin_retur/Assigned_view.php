<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <title>Assignment</title>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <?php
        $this->load->view('include/header');
        ?>

    </head>

    <body>
        <div class="container">
            <div class="row">
                <div class="span12 center">
                    <h1>Assignment</h1>
                    <h3><?php echo $this->session->userdata('ERPCode'); ?></h3>
                    <hr />
                    <?php
                        echo validation_errors();
                        if (isset($error)) {
                            echo "<div class='alert alert-error'><strong>" . $error . "</strong><br /></div>";
                        }
                        if (isset($pesan)) {
                            echo "<div class='alert alert-success'><strong>" . $pesan . "</strong><br /></div>";
                        }
                    ?>
                    <form class="form-horizontal" method="POST" action="<?php echo base_url() ?>index.php/admin_retur/tambah_assigned">
                        <div class="control-group">
                            <label id="label"><strong>Role</strong></label> : 
                            <select name="Role" id="Role">
                                <?php
                                foreach ($Role as $row) {
                                    ?>
                                    <option value="<?php echo $row['WHRoleCode'] ?>" ><?php echo $row['Name'] ?></option>
                                    <?php
                                }
                                ?>
                            </select>
                        </div>
                        <div class="control-group">
                            <label id="label"><strong>Operator</strong></label> : 
                            <select name="Operator" id="Operator">
                            </select>
                        </div>

                        <div class="control-group">
                            <input type="submit" name="btnTambah" value="Tambah" class="btn btn-large" />
                        </div>
                    </form>
                    <p><a href="<?php echo base_url() ?>index.php/admin_retur/daftar_transaksi_retur" class="btn btn-large btn-inverse">Kembali</a></p>
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <td>Operator</td>
                                <td>Peran</td>
                                <td>Aksi</td>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            foreach ($Assigned as $row) {
                                ?>
                                <tr
                                <?php if ($row['Assigned'] == 0) {
                                    ?>
                                        bgcolor="#00FFFF"
                                        <?php
                                    }
                                    ?>
                                    >
                                    <td><?php echo $row['Name'] ?></td>
                                    <td><?php echo $row['NamaRole'] ?></td>
                                    <td>
                                        <?php if ($row['Assigned'] == 0) {
                                    ?>
                                         <a href="<?php echo base_url() ?>index.php/admin_retur/delete_assigned/<?php echo str_replace('=', '-', str_replace('/', '_', base64_encode($row['OperatorCode']))) ?>/<?php echo str_replace('=', '-', str_replace('/', '_', base64_encode($row['OprRole']))) ?>" class="btn btn-info">Hapus</a>
                                        <?php
                                    }
                                    ?>
                                    </td>
                                </tr>
                                <?php
                            }
                            ?>
                        </tbody>
                    </table>
                    <input type="hidden" id="refreshed" value="no">
                    
                </div><!-- /.span4 -->
            </div><!-- /.row -->
        </div><!-- /.container -->
        <?php
        $this->load->view("include/footer");
        ?>
        <script type="text/javascript">
            
            function getOperator(input, interval) {
                var previous = "";

                setInterval( function() {

                    var val = $("#Role").val();
                    if (  val != previous ) {
                      
                        previous = val;
                        var WHRoleCode=$("#Role").val();
                        var postdata = {'WHRoleCode':WHRoleCode};
                        var link = "<?php echo base_url() ?>index.php/admin_retur/get_ajax_Operator";
                        $.ajax({
                            type: 'POST',
                            url: link,
                            dataType: 'jsonp',
                            data: postdata,
                            jsonp: 'jsoncallback',
                            timeout: 5000,
                            success: function(data){
                                var str='';
                                $.each(data, function(i,item){
                                    str = str +'<option value="'+item["OperatorCode"]+'"';
                                   
                                    
                                    str+='>'+item["Name"]+'</option>';
                                });
                                $("#Operator").html(str);
                            },
                            /*error: function (xhr, ajaxOptions, thrownError) {
                                alert(xhr.responseText);
                                alert(thrownError);
                            }*/
                            error: function(){
                                $("#Operator").val('');  
                            }
                        });   
                        
                    }

                }, interval);
            }

            getOperator($("#Role").val(), 10);
        </script>
    </body>
</html>