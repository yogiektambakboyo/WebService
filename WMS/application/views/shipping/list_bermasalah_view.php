<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <title>Tambah Shipping Bermasalah</title>
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
                    <h1>Tambah Shipping Bermasalah</h1>
                    <hr />
                    <?php
                    echo validation_errors();
                    if(isset($error))
                    {
                        echo "<div class='alert alert-error'><strong>".$error."</strong><br /></div>";
                    }
                    ?>
                    <div class="control-group">
                        <label id="label"><strong>PickList</strong></label> : <input type="text" readonly="readonly" class="input-medium" placeholder="PickList" value="<?php echo $this->session->userdata("ERPCode"); ?>" />
                        <div class="controls">

                        </div>
                    </div>
                    <form class="form-horizontal" method="POST" action="<?php echo base_url() ?>index.php/shipping/list_bermasalah">
                        <div class="control-group">
                            <h4>Pilih Barang:</h4>
                            <table class="table table-bordered table-hover table-striped">
                                <tr>
                                    <td>&nbsp;</td>
                                    <th>Nama Barang</th>
                                    <th>Jumlah</th>
                                </tr>
                                <?php
                                foreach ($listsku as $row) {
                                    ?>
                                    <tr>
                                        <td style="text-align: center"><input type="checkbox" name="SKUCode[]" value="<?php echo $row['BinCode']."~".$row['SKUCode'] ?>" /></td>
                                        <td><?php echo $row['Keterangan'] ?></td>
                                        <td><?php echo $row['QtyNeedNow'] ?></td>
                                    </tr>
                                    <?php
                                }
                                ?>
                            </table>
                        </div>
                        <div class="control-group">
                            <input type="hidden" id="refreshed" value="no">
                            <input type="submit" name="btnProses" class="btn btn-large" value="Proses"/>
                        </div>
                    </form>
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
            $('tr').click(function(event) {
                if (event.target.type !== 'checkbox') {
                    $(':checkbox', this).trigger('click');
                }
            });
        </script>
    </body>
</html>

