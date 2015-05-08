<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

class Barcode_model extends CI_Model {

    function __construct() {
        parent::__construct();
    }

    function getBarcodeBin($str) {
        $sql = "select BinCode from wms.bin where BinCode in (" . $str . ")";
        $result = $this->db->conn_id->prepare($sql);
        $result->execute();
        $result = $result->fetchAll(PDO::FETCH_OBJ);
        return $result;
    }

    function getAllBarcodeBin() {
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
                where b.isStock=1
                group by b.BinCode,isUsed,g.Keterangan,t.BinTypeName,r.Name,br.Keterangan,bs.SKUCode";
        $result = $this->db->conn_id->prepare($sql);
        $result->execute();
        $result = $result->fetchAll();
        return $result;
    }

    function getAllBarcodeRack() {
        $sql = "select RackSlotCode as RackSlotCode,[Name]as RackName from wms.RackSlot";
        $result = $this->db->conn_id->prepare($sql);
        $result->execute();
        $result = $result->fetchAll();
        return $result;
    }

    function getBarcodeRack($RackSlotCode, $RackName) {
        $sql = "select RackSlotCode as RackSlotCode,[Name]as RackName from wms.RackSlot where RackSlotCode like '%" . $RackSlotCode . "%' and [Name] like '%" . $RackName . "%'";
        $result = $this->db->conn_id->prepare($sql);
        $result->execute();
        $result = $result->fetchAll();
        return $result;
    }

    function getBarcodeRackPrint($RackSlotCode) {
        $sql = "select RackSlotCode as RackSlotCode,[Name]as RackName,RackLevel,RackType,ShelfNum from wms.RackSlot where RackSlotCode in (" . $RackSlotCode . ") order by Aisle,Position,RackColumn,RackLevel desc,ShelfNum ";
      
        $result = $this->db->conn_id->prepare($sql);
        $result->execute();
        $result = $result->fetchAll(PDO::FETCH_OBJ);
        return $result;
    }

    function setRackSlot($gang, $kolom, $kanankiri, $level, $type, $shelfnum) {
        $sql = "select count(*) as jumlah from wms.RackSlot";
        $result = $this->db->conn_id->prepare($sql);
        $result->execute();
        $result = $result->fetchAll();

        $jumkode = strlen((String) ($result[0]['jumlah'] + 1));
        $sisakode = 6 - $jumkode;
        $kode = '10';
        for ($i = 0; $i < $sisakode; $i++) {
            $kode.='0';
        }
        $kode.=($result[0]['jumlah'] + 1);

        $sql = "insert into wms.RackSlot(RackSlotCode,Aisle,Position,RackColumn,RackLevel,RackType,ShelfNum) 
            values('" . $kode . "','" . $gang . "','" . $kanankiri . "','" . $kolom . "','" . $level . "','" . $type . "','" . $shelfnum . "')";
        $result = $this->db->conn_id->prepare($sql);
        if ($result->execute()) {
            return true;
        }
        return false;
    }

    function generateBin($BinCode, $type, $OperatorCode) {
        $sql = "insert into wms.Bin(BinCode,BinTypeCode,CreateUserId,CreateTime) 
            values('" . $BinCode . "','" . $type . "','" . $OperatorCode . "',getdate())";
        $result = $this->db->conn_id->prepare($sql);
        if ($result->execute()) {
            return true;
        }
        return false;
    }

    function gettypebin() {
        $sql = "select * from wms.BinType";
        $result = $this->db->conn_id->prepare($sql);
        $result->execute();
        $result = $result->fetchAll();
        return $result;
    }

    public function getAllBpb() {
        $sql = "select top 100 a.KodeNota,a.Tgl,b.perusahaan,a.keterangan  from masterbeli a
				left join supplier b
				on a.Supplier = b.Kode
				order by Tgl desc
				";

        $result = $this->db->conn_id->prepare($sql);
        $result->execute();
        $result = $result->fetchAll();
        return $result;
    }
	
	public function getBarang()
	{
		 $sql = "select b.BinCode,isUsed,g.Keterangan,t.BinTypeName,r.rackSlotCode,r.Name,br.Keterangan as NamaBrg,dbo.KonversiSatuanToText(bs.SKUCode,sum(bs.Qty)) as Total from wms.Bin b
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
                group by b.BinCode,isUsed,g.Keterangan,t.BinTypeName,r.Name,br.Keterangan,bs.SKUCode,r.rackSlotCode
				having sum(bs.qty) > 0";
        $result = $this->db->conn_id->prepare($sql);
        $result->execute();
        $result = $result->fetchAll();
        return $result;
	}

}

?>
