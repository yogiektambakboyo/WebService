<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <title>Outstanding Finish Admin BPB</title>
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
                    <h1>Outstanding Finish Admin BPB</h1>
                    <hr />
                    <?php
                    echo validation_errors();
                    if (isset($error)) {
                        echo "<div class='alert alert-error'><strong>" . $error . "</strong><br /></div>";
                    }
                    if (isset($pesan)) {
                        ?>
                        <div class="alert alert-success">
                            <strong>
                                <?php
                                echo $pesan;
                                ?>
                            </strong>
                        </div>
                        <?php
                    }
                    ?>
                    <?php
                    echo "<strong>No. Transaksi: </strong>" . $TransactionCode . "<br />";
                    echo "<strong>No. Nota: </strong>" . $ERPCode . "<br />";
                    ?>
                    <table class="table table-bordered table-hover table-striped">
                        <tr>
                            <th>No.</th>
                            <th>SKU</th>
                            <th>Barang</th>
                            <th>Jumlah</th>
                            <th>Status</th>
                        </tr>
                        <?php
                        $i = 1;
                        foreach ($outstanding as $row) {
                            ?>
                            <tr>
                                <td><?php echo $i++ ?></td>
                                <td><?php echo $row['Kode'] ?></td>
                                <td><?php echo $row['Keterangan'] ?></td>
                                <td><?php echo $row['Jml'] ?></td>
                                <td><?php echo $row['Status'] ?></td>
                            </tr>
                            <?php
                        }
                        ?>
                    </table>
                    <div class="control-group right">
                        <a href="<?php echo base_url() ?>index.php/transaksi/export_excel/<?php echo str_replace('=', '-', str_replace('/', '_', base64_encode($TransactionCode))) ?>/<?php echo str_replace('=', '-', str_replace('/', '_', base64_encode($ERPCode))) ?>" class="btn btn-primary"><i class="icon-white icon-file"></i> Export to Excel</a>
                        <a href="<?php echo base_url() ?>index.php/transaksi/batal_finish/<?php echo str_replace('=', '-', str_replace('/', '_', base64_encode($TransactionCode))) ?>/<?php echo str_replace('=', '-', str_replace('/', '_', base64_encode($ERPCode))) ?>" class="btn btn-warning"><i class="icon-white icon-refresh"></i> Receiving Tambahan</a>
                    </div>
                    <hr/>
                    <input type="hidden" id="refreshed" value="no">
                    <h4>Penyelesaian :</h4>
                    <form method="POST" action="<?php echo base_url() ?>index.php/transaksi/tambah_notapenyelesaian">
                        <table>
                            <tr>
                                <td>
                                    <input type="text" placeholder="Kode Nota" name="kodenota" class="input-large" >
                                </td>
                                <td>
                                    <select name="tipe">
                                        <option value="jual">Faktur/Retur Jual</option>
                                        <option value="beli">Faktur/Retur Beli</option>
                                    </select>
                                </td>
                                <td>
                                    <input type="submit" name="btnSimpan" value="Simpan" class="btn btn-info">
                                </td>
                            </tr>
                        </table>
                    </form>
                    <table class="table table-bordered table-hover table-striped">
                        <thead>
                            <th>Kode Nota</th>
                            <th>Jenis</th>
                            <th>Pelanggan</th>
                            <th>Tanggal</th>
                            <td></td>
                        </thead>
                        <tbody>
                            <?php
                            foreach ($penyelesaian as $row) {
                                ?>
                                <tr>
                                    <td><?php echo $row['Kodenota'] ?></td>
                                    <td><?php
                            if ($row['Tipe'] == 'jual') {
                                echo 'Faktur/Retur Jual';
                            } else {
                                echo 'Faktur/Retur beli';
                            }
                                ?>
                                    </td>
                                    <td><?php echo $row['Perusahaan'] ?></td>
                                    <td><?php echo date('d-m-Y', strtotime($row['tgl'])); ?></td>
                                    <td><a href="<?php echo base_url() ?>index.php/transaksi/hapus_kodenota/<?php echo str_replace('=', '-', str_replace('/', '_', base64_encode($row['TransactionCode']))) ?>/<?php echo str_replace('=', '-', str_replace('/', '_', base64_encode($row['Kodenota']))) ?>" class="btn btn-danger"><i class="icon-white icon-remove"></i> Hapus</a></td>
                                </tr>
                                <?php
                            }
                            ?>
                        </tbody>
                    </table>
                    <p><a href="<?php echo base_url() ?>index.php/transaksi/edit_mastertaskrcv/<?php echo str_replace('=', '-', str_replace('/', '_', base64_encode($TransactionCode))); ?>" class="btn btn-large btn-inverse">Kembali</a></p>
                </div><!-- /.span4 -->
            </div><!-- /.row -->
        </div><!-- /.container -->
        <?php
        $this->load->view("include/footer");
        ?>
    </body>
</html>
