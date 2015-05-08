<?php
foreach ($this->cart->contents() as $item) {
    ?>
    <tr>
        <td><?php echo $item['id'] ?></td>
        <td><?php echo $item['options']['namabarang'] ?></td>
        <td>
            <?php
                $ed = $item['options']['ed'];
                $namabarang=$item['options']['namabarang'];
                $rasio=$item['options']['rasio'];
                echo $ed;
            ?>
        </td>
        <td><?php echo $item['qty'] ?></td>
        <td>
            <button class="btn" onclick="edit_retur('<?php echo $item['rowid'] ?>','<?php echo $item['id'] ?>','<?php echo $item['name'] ?>','<?php echo $ed ?>','<?php echo $item['qty'] ?>','<?php echo $namabarang ?>','<?php echo $rasio ?>')"><i class="icon-edit"></i> </button>
            <button class="btn" onclick="remove_retur('<?php echo $item['rowid'] ?>')"><i class="icon-remove"></i> </button>
        </td>
    </tr>
    <?php
}
?>