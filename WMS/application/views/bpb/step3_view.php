<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <title>Terima BPB</title>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <?php
        $this->load->view('include/header');
        ?>
    </head>

    <body>
        <div class="container">
            <div class="row">
                <div class="span12 center">
                    <h1>Terima BPB</h1>
                    <hr />
                    <div class="scroll">
                        <h5><?php echo $this->session->userdata('bpb') ?></h5>
                        <h5><?php echo $this->session->userdata('namaSupplier') ?></h5>
                    </div>
                    <br />
                    <?php
                    echo validation_errors();
                    if (isset($error)) {
                        ?>
                        <div class="alert alert-error">
                            <?php
                            echo $error;
                            ?>
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
                    if ($this->session->flashdata('error')) {
                        ?>
                        <div class="alert alert-error">
                            <?php
                            echo $this->session->flashdata('error');
                            ?>
                        </div>
                        <?php
                    }
                    ?>
                    <form class="form-horizontal" method="POST" action="<?php echo base_url() ?>index.php/bpb/step3">
                        <div class="control-group input-append">
                            <input type="text" class="input-large" placeholder="Kode Barang / Barcode" name="sku" value="<?php echo set_value('sku') ?>"/>
                            <a href="<?php echo base_url() ?>index.php/bpb/cari_barang" class="btn">Cari</a>
                        </div>
                        <div class="control-group">
                            <input type="submit" name="btnSet" class="btn btn-large" value="Set"/>
                        </div>
                    </form>
                    <form class="form-horizontal" method="POST" action="<?php echo base_url() ?>index.php/bpb/step3">
                        <div class="control-group">
                            <input type="text" class="input-large" placeholder="Kode Barang" name="kodeSKU" readonly="readonly" value="<?php echo set_value('kodeSKU', isset($barang) ? $barang['kode'] : '') ?>"/>
                        </div>
                        <div class="control-group">
                            <input type="text" class="input-large" placeholder="Nama Barang" name="namaSKU" readonly="readonly" value="<?php echo set_value('namaSKU', isset($barang) ? $barang['keterangan'] : '') ?>"/>
                        </div>
                        <div class="control-group">
                            <?php
                            if (isset($rasio)) {
                                ?>
                                <input type="text" class="input-large" placeholder="Jumlah Barang (<?php echo $rasio['ratioName'] . " - " . $rasio['ratio'] ?>)" name="jumlahSKU" value="<?php echo set_value('jumlahSKU') ?>"/>
                                <input type="hidden" name="ratio" value="<?php echo $rasio['ratio'] ?>"/>
                                <?php
                            } else {
                                ?>
                                <input type="text" class="input-large" placeholder="Jumlah Barang (SATUAN - RASIO)" name="jumlahSKU" value="<?php echo set_value('jumlahSKU') ?>"/>
                                <?php
                            }
                            ?>
                        </div>
                        <div class="control-group">
                            <input type="text" class="input-large tanggal" placeholder="Masukkan ED SKU" name="edSKU" value="<?php echo set_value('edSKU', isset($edSKULama) ? $edSKULama : '') ?>"/>
                        </div>
                        <div class="control-group">
                            <input type="submit" name="btnTambah" class="btn btn-large" value="Tambah"/>
                        </div>
                    </form>
                </div><!-- /.span4 -->
            </div><!-- /.row -->
        </div><!-- /.container -->
        <?php
        $this->load->view("include/footer");
        $today = mktime(0, 0, 0, date("m"), date("d"), date("Y"));
        ?>
        <script type="text/javascript">
            $(".tanggal").datepicker({
                format: "yyyy/mm/dd"
            });
            $('.tanggal').datepicker('setStartDate', "<?php echo date("Y-m-d", $today) ?>");
        </script>
    </body>
</html>
