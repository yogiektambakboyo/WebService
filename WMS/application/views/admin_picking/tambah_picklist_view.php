<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <title>Picking</title>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <?php
        $this->load->view('include/header');
        ?>
    </head>

    <body>
        <div class="container">
            <div class="row">
                <div class="span12 center">
                    <h1>Picking</h1>
                    <h3>Pemilihan Beberapa Picklist Akan Digabung</h3>
                    <hr />
                    <?php
                    echo validation_errors();
                    if (isset($error)) { //kode nota tidak valid
                        ?>
                        <div class="alert alert-error">
                            <strong>
                                <?php
                                echo $error;
                                ?>
                            </strong>
                        </div>
                        <?php
                    }
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
                            <td><input type="text" class="input-medium tanggal" placeholder="Masukkan Tanggal" id="tgl" value="<?php echo $tgl ?>"/></td>
                            <td>Cabang :</td>
                            <td>
                                <select name="Cabang" id="Cabang">
                                    <?php
                                    foreach ($cabang as $row) {
                                        ?>
                                        <option value="<?php echo $row['Cabang']; ?>"
                                        <?php
                                        if ($defaultcabang == $row['Cabang']) {
                                            ?>
                                                    selected="selected"
                                                    <?php
                                                }
                                                ?>

                                                ><?php echo $row['NamaCabang']; ?></option>
                                                <?
                                            }
                                            ?>
                                </select>
                            </td>
                        </tr>
                    </table>
                    <form class="form-horizontal" method="POST" action="<?php echo base_url() ?>index.php/admin_picking/tambahpicklist">
                        <div class="control-group">
                            <h4>Pilih Picklist:</h4>
                            <table id="pick" class="table table-bordered table-hover table-striped">
                                <thead>
                                    <tr>
                                        <td>&nbsp;</td>
                                        <th>No Picklist</th>
                                        <th>Tgl Picklist</th>
                                        <th>Jumlah Invoice</th>
                                        <th>Gudang</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    foreach ($picklist as $row) {
                                        ?>
                                        <tr onclick="checkthebox(this)">
                                            <td style="text-align: center"><input type="checkbox" name="picklist[]" value="<?php echo $row['NoPicklist'] ?>" /></td>
                                            <td><?php echo $row['NoPicklist'] ?></td>
                                            <td><?php echo date('d-m-Y', strtotime($row['TglPicklist'])); ?></td>
                                            <td><?php echo $row['JmlInv'] ?></td>
                                            <td><?php echo $row['keterangan'] ?></td>
                                        </tr>
                                        <?php
                                    }
                                    ?>
                                </tbody>
                            </table>
                        </div>
                        <div class="control-group">
                            <input type="submit" name="btnProses" class="btn btn-large" value="Proses"/>
                        </div>
                    </form>
                    <button class="btn btn-large" id="selectall" >Select All</button>

                    <br />
                    <br />


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
                    
                    var output = $('#pick tbody');
                    var link = "<?php echo base_url() ?>index.php/admin_picking/get_ajax_tambahpicklist";
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
                                str+= '<tr onclick="checkthebox(this)"><td style="text-align: center"><input type="checkbox" name="picklist[]" value="'+item['NoPicklist']+'" /></td>';
                                str+= '<td>'+item['NoPicklist']+'</td>';
                                str+= '<td>'+item['TglPicklist']+'</td>';
                                str+= '<td>'+item['JmlInv']+'</td>';
                                str+= '<td>'+item['keterangan']+'</td></tr>';
                            });
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
                }
                
            });
        </script>
    </body>
</html>
