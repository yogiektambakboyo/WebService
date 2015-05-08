<?php

class Admin_Laporan_Model extends CI_Model
{
	
	public function getBarang()
	{
		 $sql = "select b.BinCode,isUsed,g.Keterangan,t.BinTypeName,r.Name,br.Keterangan as NamaBrg,dbo.KonversiSatuanToText(bs.SKUCode,sum(bs.Qty)) as Total from wms.Bin b
                inner join wms.BinType t
                on b.BinTypeCode=t.BinTypeCode
                left join wms.RackSlot r
                on b.RackSlotCode=r.RackSlotCode
                left join Gudang g
                on b.WHCode=g.Kode
                left join wms.BinSKU bs
                on bs.BinCode=b.BinCode
                left join Barang br
                on bs.SKUCode=br.Kode
                where b.isStock=1 and b.isUsed <> 0
                group by b.BinCode,isUsed,g.Keterangan,t.BinTypeName,r.Name,br.Keterangan,bs.SKUCode
				having sum(bs.qty) > 0";
        $result = $this->db->conn_id->prepare($sql);
        $result->execute();
        $result = $result->fetchAll();
        return $result;
	}
	
}