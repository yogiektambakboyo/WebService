<html>
    <head>
        <style>
        </style>
    </head>
    <body>
        <table border='1'>
			<thead>
				<tr>
					<th>No</th>
					<th>Bin Code</th>
					<th>Rack Code</th>
					<th>Nama Rack</th>
					<th>Nama Barang</th>
				</tr>
			</thead>
			<tbody>
				
				<?php $i=1;  //var_dump($barang);
					foreach ($barang as $row) { ?>
					<tr>
						<td align="center" width="30px"><?php echo $i; ?></td>
						<td align="center" width="100px"><?php echo $row['BinCode']; ?>
							<br clear="all"><img src="<?php echo base_url() ?>index.php/barcode/code39?<?php printf("size=20&text=%s", $row['BinCode'] ); ?>"/>
						</td>
						<td align="center" width="100px"><?php echo $row['rackSlotCode'] ?><br clear="all"><img src="<?php echo base_url() ?>index.php/barcode/code39?<?php printf("size=20&text=%s", $row['rackSlotCode'] ); ?>"/></td>
						<td align="center" width="100px">
							<?php echo $row['Name']; ?>
							
						</td>
						<td align="center" width="300px" ><?php echo $row['NamaBrg']; ?></td>
					</tr>
				<?php $i++; } ?>				
			</tbody>
        </table>
    </body>
</html>	
