<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <title>Daftar Detail Transaksi Retur</title>
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
                    <h1>History Detail Transaksi Retur</h1>
                    <hr />
                    <?php
                    echo "Kode Transaksi : " . $detail_transaksi['TransactionCode'] . "<br />";
                    echo "Kode Bin : " . $detail_transaksi['BinCode'] . "<br />";
                    echo "RackSlot Tujuan : " . $detail_transaksi['DestRackSlot'] . "<br />";
                    echo "Barang : " . $detail_transaksi['Keterangan'] . "<br />";
                    echo "Jumlah : " . $detail_transaksi['Qty'] . "<br />";
                    ?>
                    <hr />
                    <table class="table table-bordered table-hover table-striped">
                        <tr>
                            <th>No</th>
                            <th>RackSlot Skrg</th>
                            <th>RackSlot Tujuan</th>
                            <th>OnAisle Skrg</th>
                            <th>OnAisle Tujuan</th>
                            <th>User 1</th>
                            <th>Waktu User 1</th>
                            <th>User 2</th>
                            <th>Waktu User 2</th>
                        </tr>
                        <?php
                        $i = 1;
                        foreach ($history_detail_transaksi as $row) {
                            ?>
                            <tr>
                                <td><?php echo $i++ ?></td>
                                <td><?php echo $row['CurrRackSlot'] ?></td>
                                <td><?php echo $row['DestRackSlot'] ?></td>
                                <td>
                                    <?php
                                    if ($row['CurrOnAisle'] == 1) {
                                        echo "Di Gang";
                                    } else {
                                        echo "Tidak Di Gang";
                                    }
                                    ?>
                                </td>
                                <td>
                                    <?php
                                    if($row['DestRackSlot']!=NULL)
                                    {
                                        if ($row['DestOnAisle'] == 1) {
                                            echo "Di Gang";
                                        } else {
                                            echo "Tidak Di Gang";
                                        }
                                    }
                                    ?>
                                </td>
                                <td><?php echo $row['UserPertama'] ?></td>
                                <td><?php echo $row['Waktu1'] ?></td>
                                <td><?php echo $row['UserKedua'] ?></td>
                                <td><?php echo $row['Waktu2'] ?></td>
                            </tr>
                            <?php
                        }
                        ?>
                    </table>
                    <p><a href="<?php echo base_url() ?>index.php/admin_retur/detail_transaksi_retur/<?php echo str_replace('=', '-', str_replace('/', '_', base64_encode($detail_transaksi['TransactionCode']))) ?>" class="btn btn-large btn-inverse">Kembali</a></p>
                </div><!-- /.span4 -->
            </div><!-- /.row -->
        </div><!-- /.container -->
        <?php
        $this->load->view("include/footer");
        ?>
    </body>
</html>
