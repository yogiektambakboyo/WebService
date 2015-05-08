<?php

class Umum_model extends CI_Model {
    function get_brg($RackSlotCode)
    {
        $sql = "select bi.BinCode,b.Keterangan,bs.ExpDate,dbo.KonversiSatuanToText(bs.SKUCode,bs.Qty) as Qty
                from wms.Bin bi
                inner join wms.BinSKU bs
                on bi.BinCode=bs.BinCode
                inner join Barang b
                on bs.SKUCode=b.Kode
                where bi.RackSlotCode='".$RackSlotCode."'";
        $result = $this->db->conn_id->prepare($sql);
        $result->execute();
        $result = $result->fetchAll();
        return $result;
    }
}
?>
