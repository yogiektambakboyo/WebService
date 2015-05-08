<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of replenish_model
 *
 * @author bcp
 */
class Replenish_model extends CI_Model
{
    function queryCekKodeRack($kodeRack)
    {
        $sql        = "SELECT COUNT (*) AS jumlah form wms.RackSlot where RackSlotCode=".$kodeRack;
        $cekExist   = $this->db->conn_id->prepare($sql);
        $cekExist->execute();
        $cekExist   = $cekExist->fetchAll();
        $jumlah     = 0;
        foreach($cekExist as $row){
            $jumlah = $row['jumlah'];
        }
        if ($jumlah == 0){
            return false;
        }
        return true;
    }
    
    function getListReplenish($OperatorCode, $OperatorRole, $ProjectCode)
    {
        $sql = "select mt.TransactionCode, mt.ERPCode, mt.TransactionDate,(case when o.Assigned is null then 2 else cast(o.Assigned as int) end) as Assigned
                from wms.MasterTaskRpl mt
                left join wms.DetailTaskOpr o
                on mt.TransactionCode=o.TransactionCode and o.OprRole='10/WHR/002' and o.OperatorCode='".$OperatorCode."'
                where isFinish = '0' and isFinishMove = '0' and isCancel = '0' ";
				// var_dump($sql);
        $result = $this->db->conn_id->prepare($sql);
        $result->execute();
        $result = $result->fetchAll();
        return $result;
    }
	
	public function setDetailTaskOpr($transactionCode,$oprCode,$oprRole)
	{
		$sql = "insert into wms.DetailTaskOpr(TransactionCode, OperatorCode, OprRole,Assigned) values('".$transactionCode."','".$oprCode."','".$oprRole."','1')";
		
		$input  = $this->db->conn_id->prepare($sql);
        if ($input->execute()){
            return true;
        }else {
            $sql = "Update wms.DetailTaskOpr set Assigned='1' where TransactionCode='" . $transactioncode . "' and OperatorCode='" .$oprCode . "' and OprRole='" . $oprRole . "'";
            $input = $this->db->conn_id->prepare($sql);
            $input->execute();
            return false;
        }
	}
	
	public function getDetailTaskRplHis($trCode, $OperatorCode, $OperatorRole)
	{
		$sql = " Select a.TransactionCode, a.SKUCode,a.NoUrut,a.QueueNumber, b.Keterangan, c.BinCode, c.SrcRackSlot, 		c.DestBin, c.DestRackSlot, c.CreateTime,c.QtyNeedNow,dbo.KonversiSatuanToText(b.Kode,c.QtyNeedNow) as QtyKonversi, (select Name from wms.RackSlot Where RackSlotCode = a.SrcRackSlot) as SrcRackName, (select Name from wms.RackSlot Where RackSlotCode = c.DestRackSlot)  as DestRackName
			   from wms.DetailTaskRplHistory a 
			   left join wms.DetailTaskRpl c 
			   on a.TransactionCode = c.TransactionCode and a.NoUrut = c.NoUrut
			   left join dbo.barang b
			   on a.SKUCode = b.Kode 
			   where a.TransactionCode IN (select distinct m.TransactionCode
                from wms.MasterTaskRpl m,wms.DetailTaskOpr d
                where m.isFinish=0 AND m.isFinishMove=0 AND m.isCancel=0 and d.TransactionCode=m.TransactionCode and d.OperatorCode='" . $OperatorCode . "' and d.OprRole='" . $OperatorRole . "')
			   and a.DestRackSlot IS NULL and a.DestBin is null and a.user_1st is null and a.user_2nd is null 
			";
		// echo $sql;
		$result = $this->db->conn_id->prepare($sql);
        $result->execute();
        $result = $result->fetchAll();
        return $result;
	}
	
	public function setUser1stBinCode($TransactionCode, $QueueNumber, $NoUrut, $OperatorCode, $bin) {
        //melakukan update pada user_1st di detailtransactionhistory paling akhir

        $sql = "UPDATE wms.DetailTaskRplHistory SET DestBin='".$bin."', User_1st='" . $OperatorCode . "',Time_1st=getdate() 
                WHERE TransactionCode='" . $TransactionCode . "' AND NoUrut='" . $NoUrut . "'
                AND QueueNumber='" . $QueueNumber."'";
       // echo $sql;
        $result = $this->db->conn_id->prepare($sql);
        $result->execute();
    }
	
	public function getMyOutstanding($OperatorCode)
	{
		$sql = " Select a.DestBin as BinAwal,a.TransactionCode, a.SKUCode,a.NoUrut,a.QueueNumber, b.Keterangan, c.BinCode, c.SrcRackSlot, 		c.DestBin, c.DestRackSlot, c.CreateTime,c.QtyNeedNow,dbo.KonversiSatuanToText(b.Kode,c.QtyNeedNow) as QtyKonversi, (select Name from wms.RackSlot Where RackSlotCode = a.SrcRackSlot) as SrcRackName, (select Name from wms.RackSlot Where RackSlotCode = c.DestRackSlot)  as DestRackName
			   from wms.DetailTaskRplHistory a 
			   left join wms.DetailTaskRpl c 
			   on a.TransactionCode = c.TransactionCode and a.NoUrut = c.NoUrut
			   left join dbo.barang b
			   on a.SKUCode = b.Kode 
			   where a.DestRackSlot IS NULL and a.user_1st ='".$OperatorCode."' and a.user_2nd is null
			";
		// echo $sql;
		$result = $this->db->conn_id->prepare($sql);
        $result->execute();
        $result = $result->fetchAll();
        return $result;
	}
	
	
	
    
    function cekReplenishList($kodeBin,$skuCode,$rackSlot)
    {
        $sql    = "SELECT COUNT (*) AS Jumlah FROM wms.DetailTaskRpl WHERE SKUCode='".$skuCode."' 
                   AND SrcRackSlot='".$rackSlot."' AND SrcBin='".$kodeBin."' AND User_2nd IS NULL ";
        
        $result = $this->db->conn_id->prepare($sql);
        $result->execute();
        $result = $result->fetchAll();
        
        $jumlah     = 0;
        foreach($result as $row){
            $jumlah = $row['Jumlah'];
        }
        if ($jumlah == 0){
            return false;
        }
        return true;
    }
    
    function cekBinReplenish($kodeBin)
    {
        $sql    = "SELECT COUNT (*) AS Jumlah FROM wms.Bin WHERE BinCode='".$kodeBin."'";
        
        $result = $this->db->conn_id->prepare($sql);
        $result->execute();
        $result = $result->fetchAll();
        $jumlah = 0;
        foreach($result as $row){
            $jumlah = $row['Jumlah'];
        }
        return $jumlah;
    }
    
    function setReplenish($kodeBin,$skuCode,$rackSlot,$expDate,$qtyAwal,$note,$user,$date)
    {
        $sql    = "INSERT INTO wms.DetailTaskRpl(SKUCode,QtyStart,ExpDateNeed,SrcRackSlot,SrcBin,Note,User_1st)
                   VALUES('" . $skuCode . "', '" . $qtyAwal . "', '" . $expDate . "', '" . $rackSlot . "', '" . $kodeBin . "', '" . $note . "', '" . $user . "')";
        //echo $sql;
        $input  = $this->db->conn_id->prepare($sql);
        if ($input->execute()){
            return true;
        }else {
            return false;
        }
    }
    
    function updateReplenish($trCode,$binAwal, $qty, $binTujuan, $rackTujuan, $OnAisle, $user, $queue, $noUrut)
    {
//        $date   = date('y-m-d H:i:s');
        $sql    = "UPDATE wms.DetailTaskRplHistory SET DestRackSlot='" . $rackTujuan . "', DestOnAisle='" . $OnAisle . "', DestBin='" . $binTujuan . "', DestQty='".$qty."', User_2nd='" . $user . "', Time_2nd=getdate()
                   WHERE TransactionCode='".$trCode."' and NoUrut = '".$noUrut."' and QueueNumber ='".$queue."' ";
        // echo $sql;
        $update = $this->db->conn_id->prepare($sql);
        if ($update->execute()){
            return true;
        }else {
            return false;
        }
    }
    
    function getNamaBarang($kodeBin)
    {
        $sql    = "SELECT b.BinCode, b.RackSlotCode, s.SKUCode, s.ExpDate, s.Qty, k.Keterangan
                   FROM wms.Bin b 
                   LEFT JOIN wms.BinSKU s
                   ON b.BinCode = s.BinCode	
                   LEFT JOIN barang k
                   ON s.SKUCode = k.kode  
                   WHERE b.BinCode='".$kodeBin."'";
        
        
        $result = $this->db->conn_id->prepare($sql);
        $result->execute();
        $result = $result->fetchAll();
        return $result;
    }
    
    function getDataRp($kodeBin)
    {
        $sql    = "SELECT SKUCode, QtyStart, SrcRackSlot, SrcOnAisle, Note, NoUrut
                   FROM wms.DetailTaskRpl
                   WHERE SrcBin='".$kodeBin."' AND User_2nd IS NULL ";
        
        $result = $this->db->conn_id->prepare($sql);
        $result->execute();
        $result = $result->fetchAll();
        return $result;
    }
    function getsatuan2($SKUCode) {
        $sql = "SELECT Rasio,Satuan FROM satuan where Brg='" . $SKUCode . "' AND SatuanAktif=1";
        $result = $this->db->conn_id->prepare($sql);
        $result->execute();
        $result = $result->fetchAll();
        return $result;
    }
}

?>
