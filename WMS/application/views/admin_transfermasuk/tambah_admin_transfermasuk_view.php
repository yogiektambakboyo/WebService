<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <title>Tambah TransferStok Masuk</title>
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
                    <h1>Tambah TransferStok Masuk</h1>
                    <hr />
                    <?php
                    echo validation_errors();
                    if ($this->session->flashdata('pesan')) {
                        ?>
                        <div class="alert alert-success">
                            <strong>
                                <?php
                                echo $this->session->flashdata('pesan');
                                ?>
                            </strong>
                        </div>
                        <?php
                    }
                    ?>
                    <table>
                        <tr>
                            <td>Tanggal :</td>
                            <td><input type="text" class="input-medium tanggal" placeholder="Masukkan Tanggal" id="tgl" value="<?php echo date("Y/m/d") ?>"/></td>
                            <td>Cabang :</td>
                            <td>
                                <select name="Cabang" id="Cabang">
                                    <?php
                                    foreach ($cabang as $row) {
                                        ?>
                                        <option value="<?php echo $row['Cabang']; ?>"><?php echo $row['NamaCabang']; ?></option>
                                        <?
                                    }
                                    ?>
                                </select>
                            </td>
                        </tr>
                    </table>
                    <form class="form-horizontal" method="POST" action="<?php echo base_url() ?>index.php/admin_transfermasuk/tambah_admin_transfermasuk">
                        <div class="control-group">
                            <h4>Pilih KodeNota TransferStok:</h4>
                            <table id="returlist" class="table table-bordered table-hover table-striped">
                                <thead>
                                    <tr>
                                        <td>&nbsp;</td>
                                        <th>No TransferStok</th>
                                        <th>Tgl TransferStok</th>
                                        <th>Keterangan</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    foreach ($kodenota as $row) {
                                        ?>
                                        <tr onclick="checkthebox(this)">
                                            <td style="text-align: center"><input type="checkbox" name="kodenota[]" value="<?php echo $row['KodeNota'] ?>" /></td>
                                            <td><?php echo $row['KodeNota'] ?></td>
                                            <td><?php echo $row['Perusahaan'] ?></td>
                                            <td><?php echo $row['Keterangan'] ?></td>
                                        </tr>
                                        <?php
                                    }
                                    ?>
                                </tbody>
                            </table>
                        </div>
                        <div class="control-group">
                            <input type="submit" name="btnTambah" class="btn btn-large" value="Proses"/>
                        </div>
                    </form>
                    <button class="btn btn-large" id="selectall" >Select All</button>
                </div><!-- /.span4 -->
            </div><!-- /.row -->
        </div><!-- /.container -->
        <?php
        $this->load->view("include/footer");
        ?>
        <script type="text/javascript">
            
            function checkthebox(e)
            {
               $(e).find('input:checkbox').trigger('click'); 
            }
            $('#selectall').click(function(){
                if($('#selectall').html()=='Select All')
                {
                    $('input:checkbox').prop('checked', true);
                    $('#selectall').html('Deselect All');
                }
                else
                {
                    $('input:checkbox').prop('checked', false);
                    $('#selectall').html('Select All');
                }
                //$(this).closest('form').find(':checkbox').prop('checked', this.checked);
            });
            $(".tanggal").datepicker({
                format: "yyyy/mm/dd"
            });
            $("#tgl,#Cabang").change(function(){
                var tgl=$("#tgl").val();
                var Cabang=$("#Cabang").val();
                var postdata = {'tgl':tgl,'cabang':Cabang};
                if($.trim(tgl)!=''){
                    
                    var output = $('#returlist tbody');
                    var link = "<?php echo base_url() ?>index.php/admin_transfermasuk/get_ajax_tambah_admin_transfermasuk";
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
                                    str+= '<tr onclick="checkthebox(this)"><td style="text-align: center"><input type="checkbox" name="kodenota[]" value="'+item['KodeNota']+'" /></td>';
                                    str+= '<td>'+item['KodeNota']+'</td>';
                                    str+= '<td>'+item['Perusahaan']+'</td>';
                                    str+= '<td>'+item['Keterangan']+'</td></tr>';
                                });
                                output.html(str); 
                            },
                            error: function (xhr, ajaxOptions, thrownError) {
                                alert(xhr.responseText);
                                alert(thrownError);
                            }
                            /*error: function(){
                                    //alert(data,null,'Error','Ok');
                                    alert('There is a problem',null,'Error','Ok');
                            }*/
                    });
                }
                
            });
        </script>
    </body>
</html>
