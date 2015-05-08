<?php
$temp=array();
if($this->session->userdata('ListRpl'))
    $temp=$this->session->userdata('ListRpl');
foreach ($temp as $key=>$item) {
    ?>
    <tr>
        <td><?php echo $item['SrcRackName'] ?></td>
        <td><?php echo $item['BinCode'] ?></td>
        <td><?php echo $item['Keterangan'] ?></td>
        <td><?php echo $item['SrcQtykonversi'] ?></td>
        <td><?php echo $item['Qtykonversi'] ?></td>
        <td><?php echo $item['DestRackName'] ?></td>
        <td>
                                                                                                                                                                                                                                
            <button class="btn" onclick="edit_replenish('<?php echo $key ?>','<?php echo $item['SrcRackName'] ?>','<?php echo $item['SrcRack'] ?>','<?php echo $item['BinCode'] ?>','<?php echo $item['SKUCode'] ?>','<?php echo $item['Keterangan'] ?>','<?php echo $item['SrcQty'] ?>','<?php echo $item['SrcQtykonversi'] ?>','<?php echo $item['DestRack'] ?>','<?php echo $item['DestRackName'] ?>','<?php echo $item['Qty'] ?>','<?php echo $item['Rasio'] ?>')"><i class="icon-edit"></i> </button>
            <button class="btn" onclick="remove_replenish('<?php echo $key ?>')"><i class="icon-remove"></i> </button>
        </td>
    </tr>
    <?php
}
?>