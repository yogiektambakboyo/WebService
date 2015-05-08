<?php
foreach ($this->cart->contents() as $item) {
    ?>
    <tr>
        <td><?php echo $item['options']['rackcode']; ?></td>
        <td><?php echo $item['id'] ?></td>
        <td><?php echo $item['name'] ?></td>
        <td><?php echo $item['options']['jmlSKUkonversi'] ?></td>
        <td><?php echo $item['options']['bintemp'] ?></td>
        <td>
            <button class="btn" onclick="edit_replenish('<?php echo $item['rowid'] ?>','<?php echo $item['options']['rackcode'] ?>','<?php echo $item['id'] ?>','<?php echo $item['options']['QtyAwal'] ?>','<?php echo $item['options']['QtyAwalKonversi'] ?>','<?php echo $item['options']['jmlSKU']/$item['options']['rasio'] ?>','<?php echo $item['name'] ?>','<?php echo $item['options']['kodeSKU'] ?>','<?php echo $item['options']['bintemp'] ?>','<?php echo $item['options']['rasio'] ?>')"><i class="icon-edit"></i> </button>
            <button class="btn" onclick="remove_replenish('<?php echo $item['rowid'] ?>')"><i class="icon-remove"></i> </button>
        </td>
    </tr>
    <?php
}
?>