<?php

class Abb_Model extends CI_Model
{
	public function listAbb()
	{
		$sql 	= "SELECT a.TransactionCode, a.SKUCode, a.Qty, b.ERPCode, c.BinCode, c.DestRackSlot, dbo.KonversiSatuanToText(a.SKUCode,a.Qty) as qtyKonv, c.NoUrut,c.ExpDate, a.qty-a.pick as selisih, dbo.KonversiSatuanToText(a.SKUCode,a.qty-a.pick)as selisihKonv, d.Keterangan,c.destBin
		FROM wms.DetailTaskRcvOver a
		left join wms.MasterTaskRcv b
		on a.TransactionCode = b.TransactionCode
		left join wms.detailTaskRcv c
		on a.TransactionCode = c.TransactionCode
		left join barang d
		on a.SKUCode = d.Kode
		where (a.Qty-a.Pick) > 0 and c.Status = '1' and c.Status2 ='1' and c.NoUrut = '2' ";
		// echo $sql;
		$result = $this->db->conn_id->prepare($sql);
		$result->execute();
		$result = $result->fetchAll();
		return $result;
	}
	
	public function getSuggestion($TransactionCode,$SKUCode,$Qty,$bin)
	{
		$sql 	= "SELECT a.TransactionCode, a.SKUCode, a.ExpDate, a.DestRackSlot, a.DestQty, a.BinCode, b.name, b.RackLevel, dbo.KonversiSatuanToText(a.SKUCode,a.DestQty) as qtyKonv, c.qty, a.NoUrut, (select max(nourut)as noUrut from wms.detailtaskrcv where TransactionCode = '".$TransactionCode."') maxnourut, dbo.KonversiSatuanToText(d.SKUCode,d.Qty) as qtyBinSku
		from wms.detailTaskRcv a 
		left join wms.rackSlot b 
		on a.DestRackSlot = b. RackSlotCode
		left join wms.DetailTaskRcvOver c
		on a.TransactionCode = c.TransactionCode and a.SKUCode = c.SKUCode
		left join wms.BinSku d
		on a.DestBin = d.BinCode and a.SKUCode = d.SKUCode
		where a.TransactionCode = '".$TransactionCode."' and a.SKUCOde = '".$SKUCode."' and a.Status = 1 and a.status2 = 1 ";
		// echo $sql;
		$result = $this->db->conn_id->prepare($sql);
		$result->execute();
		$result = $result->fetchAll();
		return $result;
	}
	
	public function setDetailTaskRcv($rackSrc,$binSrc,$binDest,$qty,$trCode, $sku,$expDate, $noUrut)
	{
		$sql 	= "insert into wms.detailTaskRcv(TransactionCode,NoUrut,BinCode,SKUCode,Qty,ExpDate,CurrRackSlot,CurrOnAisle,DestRackSlot,DestOnAisle,Note,CreateTime) values('".$trCode."','".$noUrut."','".$binSrc."','".$sku."','".$qty."','".$expDate."','".$rackSrc."','1','".$rackSrc."','0','ABB',getDate()) ";
		// echo $sql;
		$result = $this->db->conn_id->prepare($sql);
		$result->execute();
		$result = $result->fetchAll();
		return $result;
	}
	
	public function updateDetailTaskHistory($rackSrc,$binSrc,$binDest,$qty,$trCode, $sku,$expDate, $noUrut, $user)
	{
		$sql 	= "update wms.detailTaskRcvHistory SET DestRackSlot = '".$rackSrc."', DestBin = '".$binDest."', DestQty = '".$qty."', User_1st = '".$user."', User_2nd='".$user."', DestOnAisle ='0' where TransactionCode = '".$trCode."' and SKUCode ='".$sku."' and NoUrut = '".$noUrut."'";
		// echo $sql;
		$result = $this->db->conn_id->prepare($sql);
		$result->execute();
		$result = $result->fetchAll();
		return $result;
	}
	
	public function setDetailTaskRcv2($rackSrc,$binSrc,$binDest,$qty,$trCode, $sku,$expDate, $noUrut)
	{
		$sql 	= "insert into wms.detailTaskRcv(TransactionCode,NoUrut,BinCode,SKUCode,Qty,ExpDate,CurrRackSlot,CurrOnAisle,DestOnAisle,CreateTime) values('".$trCode."','".$noUrut."','".$binDest."','".$sku."','".$qty."','".$expDate."','".$rackSrc."','1','0',getDate()) ";
		// echo $sql;
		$result = $this->db->conn_id->prepare($sql);
		$result->execute();
		$result = $result->fetchAll();
		return $result;
	}
	
	public function updateDetailOver($trCode,$sku,$totQty)
	{
		$sql 	= "update wms.detailTaskRcvOver set pick = '".$totQty."' where TransactionCode = '".$trCode."' and SKUCode = '".$sku."' ";
		$result = $this->db->conn_id->prepare($sql);
		$result->execute();
		$result = $result->fetchAll();
		return $result;
		
	}
	
	public function cekDetailOver($trCode,$sku)
	{
		$sql 	= "select * from wms.detailTaskRcvOver where TransactionCode = '".$trCode."' and SKUCode = '".$sku."' ";
		$result = $this->db->conn_id->prepare($sql);
		$result->execute();
		$result = $result->fetchAll();
		return $result[0];
	}
	
	public function getsatuan($trCode,$SKUCode) 
	{
        $sql = "SELECT Rasio,satuan from dbo.satuan where brg='".$SKUCode."' and satuanaktif=1 ";
		// echo $sql;
        $result = $this->db->conn_id->prepare($sql);
        $result->execute();
        $result = $result->fetchAll();
        return $result;
    }
	
	 function cekBin($kodeBin)
    {
        $sql    = "SELECT COUNT (*) AS Jumlah FROM wms.bin WHERE binCode='".$kodeBin."'";
        
        $result = $this->db->conn_id->prepare($sql);
        $result->execute();
        $result = $result->fetchAll();
        $jumlah = 0;
        foreach($result as $row){
            $jumlah = $row['Jumlah'];
        }
        return $jumlah;
    }
	
	function getRackName($RackSlotCode) {
        $sql = "select Name as RackName from wms.RackSlot where RackSlotCode='".$RackSlotCode."'";

        $result = $this->db->conn_id->prepare($sql);
        $result->execute();
        $result = $result->fetchAll();
        return $result[0];
    }
	
	
}



