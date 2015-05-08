<?php session_start();?>
<div class="header">

    <h1 class="page-title">Inbound Task</h1>
</div>
<div class="main-content" style="height:100%">
    <div class="panel panel-default">
        <a href="#page-stats" class="panel-heading" data-toggle="collapse"></span></span> </a>
        <div id="page-stats" class="panel-collapse panel-body collapse in">

            <div class="row">

                <div class="form-group col-xs-4">

                    <div class="col-xs-3"><label class="control-label">Jenis : </label></div>
                    <div class="col-xs-9">
                        <select class="form-control" id="jenis">
                            <?php
                            include_once("../script/connectbon.php");
                            $sql="select * from Inbound_CloseSetting where TransactionType = :inbound";
                            $sth = $dbh->prepare($sql);
                            $result = $sth->execute(array(
                                ":inbound"=>$_GET["inbound"]
                            ));
                            $result=$sth->fetchAll();
                            foreach ($result as $row) {
                               $arrjenis[]=$row['Jenis'];
                            }


                            foreach (array_unique($arrjenis) as $row) {
                                echo '<option value="'.substr($row,0,3).'">'.$row.'</option>';
                            }



                            ?>
                        </select>
                    </div>
                </div>
                <div class="form-group col-xs-4">

                    <div class="col-xs-3"><label class="control-label">Cust : </label></div>
                    <div class="col-xs-9">

                        <select class="form-control" id="cust">
                            <?php
                            foreach ($result as $row) {
                                $cust=$row['Cust'];
                                if($row['Cust']=='#'){
                                    $cust=$_GET['shipto'];
                                }

                                echo '<option value="'.$cust.'" class="'.substr($row['Jenis'],0,3).' custopt" style="display:none">'.$cust.'</option>';
                            }



                            ?>
                        </select>

                    </div>
                </div>



                <div class="form-group col-xs-2">

                    <button type="button" class="btn btn-primary" id="Simpanbtn">Simpan</button>
                </div>

            </div>
            <div class="scrollable-area" style="max-height:350px;overflow:scroll;font-size: 12px;">
                <div class="row" id="CloseTbl" style="margin: 0px">
                    <?php include_once "../taskinbound/closetask.php";?>
                </div>
            </div>

        </div>
    </div>


</div>
<script>
    $(document).ready(function(){
        var jenis=$('#jenis').val();
        $('.custopt').hide();
        $('.'+jenis).show();
        $('#cust').val($('.'+jenis).val())

    })
    $(document).on("change","#jenis",function() {
        var jenis=$('#jenis').val();
        $('.custopt').hide();
        $('.'+jenis).show();
        $('#cust').val($('.'+jenis).val())

    });
    $(document).on("click","#Movebtn",function(){
        var namaproduct=$('.info').find('td:eq(1)').html();
        var kode=$('.info').find('td:eq(2)').html();
        var jml=$('.info').find('td:eq(3)').html();

        if(kode==undefined){
            alert("Pilih barang yang akan dibuatkan transaksi");
            return;
        }

        var keterangan = window.prompt("Jumlah Barang (Maksimal "+jml+")", "");
        if(parseInt(keterangan)>parseInt(jml)){
            alert("Jumlah melebihi selisih");
            return;
        }
        if(keterangan!=null){
            var found=0;
            $('table tr').each(function(){

                if($(this).find('td').eq(0).text() == kode){
                    var jmlawl=$(this).find('td').eq(1).text()
                    $(this).addClass('info');
                    $(this).find('td').eq(1).text(parseInt(jmlawl)+parseInt(keterangan));
                    found=1
                }
            })
            if(found==0){
                $('#movetbl').append('<tr><td>'+kode+'</td><td>'+keterangan+'</td><td><a href="javascript:void(0)" class="removebrg"><i class="fa fa-close"></i></a></td></tr>')
            }
            $('.info').find('td:eq(3)').html(parseInt(jml)-parseInt(keterangan));

        }

    })


</script>