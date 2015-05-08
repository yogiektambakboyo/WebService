<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <title>Print Barang Kembali</title>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="description" content="">
        <meta name="author" content="bara alfa">
		<link href="<?php echo base_url() ?>files/css/print.css" rel="stylesheet" type="text/css" media="print">
		<link href="<?php echo base_url() ?>files/css/screenPrint.css" rel="stylesheet" type="text/css" media="screen,print">
		
    </head>

<style>
hr {
  border-top: 1px dotted black;
  color: #fff;
  background-color: #fff;
  height: 1px;
  width:100%;
}
</style>
 <body>
 <div class="page">
	<h1>Laporan Barang Kembali</h1>
		<p class="meta">
			<label>Tanggal Cetak :</label> <?php echo date('d-M-Y H:i:s') ?><br />
			<label>Petugas :</label> <?php echo $this->session->userdata('OperatorCode'); ?>
		</p>
	<div class="title">
	Transaction Code = <?php echo $cetak[0]['transactionCode'] ?>
	</div>
		<table class="gridview" cellspacing="1" cellpadding="0">
				<thead>
					<tr>
						<th>No.</th>
						<th>Kode Barang</th>
						<th>Nama Barang</th>
						<th>Jumlah</th>
					</tr>
				</thead>
				<tbody>
					<?php 
						$no = 1;
						foreach ($cetak as $laporan):                 
					?>
					<tr style="font-size: 12px">
						<td style="width: 20px"><?php echo $no ?></td>
						<td align='center' width="150px"><?php echo $laporan['skuCode'] ?></td>
						<td><?php echo $laporan['keterangan'] ?></td>
						<td><?php echo $laporan['qtyKonv'] ?></td>				   
					</tr>
					<?php $no++; endforeach; ?>
				</tbody>
			</table>
			</br>
			</br>
			Keterangan : <hr>
			</br>
			</br>
			</br>
			</br>
			<table class="gridview" cellspacing="1" cellpadding="0">
				<thead>
					<tr>
						<th>Serah</th>
						<th>Terima</th>
						<th>Serah</th>
						<th>Terima</th>
					</tr>
				</thead>
				<tbody>
					
					<tr style="font-size: 14px">
						<td align='center'><div style="margin-top:70px">(Shipper)</div></td>
						<td align='center'><div style="margin-top:70px">(Logistik)</div></td>
						<td align='center'><div style="margin-top:70px">(Divisi)</div></td>
						<td align='center'><div style="margin-top:70px">(Shipper)</div></td>				   
					</tr>
				</tbody>
			</table>
		</div>
	</body>
</html>