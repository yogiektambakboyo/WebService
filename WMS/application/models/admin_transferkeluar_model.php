<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of receiving_model
 *
 * @author USER
 */
class Admin_transferkeluar_model extends CI_Model {

    function getPickingList($tgl, $cabang) {
        $sql = "select m.kodenota as NoPicklist,m.tgl as TglPicklist,count(1) as JmlInv from mastertransfer m
                inner join detailtransfer d
                on m.kodenota=d.kodenota
                inner join wms.gudangwms g
                on g.kode=d.asalgudang and g.kode<>d.tujuangudang 
				where m.kodenota like '".$cabang."'+'%'
                and m.tgl='".$tgl."' and not exists
                (select * from wms.MasterTaskPck where ERPCode=m.kodenota)
                group by m.kodenota,m.tgl";
        $result = $this->db->conn_id->prepare($sql);
        $result->execute();
        $result = $result->fetchAll();
        return $result;
    }

    function getCabang() {
        $sql = "select Cabang,NamaCabang from kategori where tgltransaksi>getdate()-".$this->session->userdata('HariCabang');
        $result = $this->db->conn_id->prepare($sql);
        $result->execute();
        $result = $result->fetchAll();
        return $result;
    }

    function setPicklist($kode_proyek, $NoPickList, $tanggal, $siteId, $operatorCode) {
        $sql = "exec wms.spCreateTaskPicking '" . $kode_proyek . "','" . $NoPickList . "','" . $tanggal . "','" . $siteId . "','" . $operatorCode . "','Baru'";
        $result = $this->db->conn_id->prepare($sql);
        //var_dump($sql);
        if ($result->execute()) {
            return TRUE;
        } else {
            return FALSE;
        }
    }

    function getMasterTaskPicking() {
        $sql = "select * from wms.MasterTaskPck where isFinish=0 and isCancel=0 and ProjectCode='PTS'
                order by TransactionDate desc";
        $result = $this->db->conn_id->prepare($sql);
        $result->execute();
        $result = $result->fetchAll();
        return $result;
    }

    function getNoteMaster($TransactionCode) {
        $sql = "select * 
                from wms.MasterTaskPck 
                where TransactionCode='" . $TransactionCode . "'";

        $result = $this->db->conn_id->prepare($sql);
        $result->execute();
        $result = $result->fetchAll();
        return $result[0];
    }

    function setNoteMaster($TransactionCode, $Note) {
        if (trim($Note, " ") != "") {
            $sql = "update wms.MasterTaskPck set Note='" . $Note . "'
                where TransactionCode='" . $TransactionCode . "'";
        } else {
            $sql = "update wms.MasterTaskPck set Note=NULL
                where TransactionCode='" . $TransactionCode . "'";
        }
        $result = $this->db->conn_id->prepare($sql);
        if ($result->execute()) {
            return true;
        }
        return false;
    }

    function getDetailTransaction($TransactionCode) { //keterangan transaksi
        $sql = "select t.transactionCode,t.projectCode,t.note,t.isFinish,t.isCancel,t.ERPCode
                from wms.MasterTaskPck t
                where t.TransactionCode = '" . $TransactionCode . "'";
        $result = $this->db->conn_id->prepare($sql);
        $result->execute();
        $result = $result->fetchAll();
        return $result[0];
    }

    function getDetailTransaction2($TransactionCode) { //detail transaksi
        $sql = "select d.*,b.Keterangan,(select Name from wms.RackSlot where RackSlotCode=d.SrcRackSlot) as Curr,
                (select Name from wms.RackSlot where RackSlotCode=d.DestRackSlot) as Dest,
                 dbo.KonversiSatuanToText(d.SKUCode,d.Qty) as KonversiQty,
                dbo.KonversiSatuanToText(d.SKUCode,d.QtyNeedStart) as KonversiQtyNeedStart,
                (select Name from wms.Operator where OperatorCode=d.User_1st) as User_1stName,
                (select Name from wms.Operator where OperatorCode=d.User_2nd) as User_2ndName
                from wms.DetailTaskPck d 
                inner join Barang b
                on b.Kode=d.SkuCode                
                where TransactionCode='" . $TransactionCode . "'";
        $result = $this->db->conn_id->prepare($sql);
        $result->execute();
        $result = $result->fetchAll();
        return $result;
    }

    function getNote($TransactionCode, $NoUrut) {
        $sql = "select dt.TransactionCode,dt.NoUrut,dt.Note,dt.BinCode,t.ERPCode 
                from wms.DetailTaskPck dt 
                inner join wms.MasterTaskPck t
                on t.TransactionCode=dt.TransactionCode
                where dt.TransactionCode='" . $TransactionCode . "' and dt.NoUrut = '" . $NoUrut . "'";

        $result = $this->db->conn_id->prepare($sql);
        $result->execute();
        $result = $result->fetchAll();
        return $result[0];
    }

    function setNote($TransactionCode, $NoUrut, $Note) {
        if (trim($Note, " ") != "") {
            $sql = "update wms.DetailTaskPck set Note='" . $Note . "'
                where TransactionCode='" . $TransactionCode . "' and NoUrut = '" . $NoUrut . "'";
        } else {
            $sql = "update wms.DetailTaskPck set Note=NULL
                where TransactionCode='" . $TransactionCode . "' and NoUrut = '" . $NoUrut . "'";
        }
        $result = $this->db->conn_id->prepare($sql);
        if ($result->execute()) {
            return true;
        }
        return false;
    }

    function getstatustask($TransactionCode) {
        $sql = "select isFinishMove,isFinish,isCancel
                from wms.MasterTaskPck
                where TransactionCode='" . $TransactionCode . "'";

        $result = $this->db->conn_id->prepare($sql);
        $result->execute();
        $result = $result->fetchAll();
        return $result[0];
    }

    function cekPembatalan($TransactionCode) {
        $sql = "select count(*) as jumlah from wms.DetailTaskPck where TransactionCode='" . $TransactionCode . "'";
        $result = $this->db->conn_id->prepare($sql);
        $result->execute();
        $result = $result->fetchAll();
        if ($result[0]['jumlah'] == 0) {
            $sql = "select count(*) as jumlah from wms.DetailTaskPckS where TransactionCode='" . $TransactionCode . "'";
            $result = $this->db->conn_id->prepare($sql);
            $result->execute();
            $result = $result->fetchAll();
            if ($result[0]['jumlah'] == 0) {
                return true;
            }
            return false;
        }
        return false;
    }

    function setNoteMasterPembatalan($TransactionCode, $Note, $OperatorCode) {

        $sql = "update wms.MasterTaskPck set Note='" . $Note . "',isCancel=1,UpdateUserId='" . $OperatorCode . "', UpdateTime=getdate()
                where TransactionCode='" . $TransactionCode . "'";
        $result = $this->db->conn_id->prepare($sql);
        if ($result->execute()) {
            return true;
        }
        return false;
    }

    function cekOutstandingFinishMove($TransactionCode) {
        $sql = "(select dp.TransactionCode,dp.SKUCode,b.Keterangan,dp.ExpDate,dp.Qty as Qty,r.Name as SrcRackName,dp.DestBin as Bin,'PICKING' as Jenis 
                from wms.DetailTaskPck dp 
                left join Barang b on dp.SKUCode=b.Kode 
                left join wms.Bin bi on bi.BinCode=dp.DestBin 
                left join wms.RackSlot r on bi.RackSlotCode=r.RackSlotCode 
                where dp.TransactionCode='" . $TransactionCode . "' and dp.DestBin is not null and dp.DestRackSlot is null)
                 union 
                 (select dps.TransactionCode,dps.SKUCode,b.Keterangan,dps.ExpDate,dps.QtyNeedNow as Qty,r.Name as SrcRackName,dps.BinCode as Bin,'SHIPPING' as jenis 
                 from wms.DetailTaskPckS dps 
                 left join Barang b on dps.SKUCode=b.Kode 
                 left join wms.RackSlot r on dps.CurrRackSlot=r.RackSlotCode 
                 where dps.TransactionCode='" . $TransactionCode . "' and dps.DestBin is null)";

        $result = $this->db->conn_id->prepare($sql);
        $result->execute();
        $result = $result->fetchAll();
        return $result;
    }

    function setFinishMove($TransactionCode, $OperatorCode) {
        $sql = "update wms.MasterTaskPck set isFinishMove=1,UpdateUserId='" . $OperatorCode . "', UpdateTime=getdate()
                where TransactionCode='" . $TransactionCode . "'";
        $result = $this->db->conn_id->prepare($sql);
        if ($result->execute()) {
            return true;
        }
        return false;
    }

    function cekOutstandingFinishAdmin($TransactionCode) {
        $sql = "exec wms.spCekPendingItemShp '" . $TransactionCode . "'";
        $result = $this->db->conn_id->prepare($sql);
        $result->execute();
        $result = $result->fetchAll();
        return $result;
    }

    function getNotaPenyelesaian($TransactionCode) {
        $sql = "select d.*,m.tgl,p.Perusahaan,p.Kode from wms.DetailTaskAddTrans d
                inner join Masterjual m on m.KodeNota=d.Kodenota
                inner join Pelanggan p on m.Cust=p.Kode
                where d.TransactionCode='" . $TransactionCode . "'";
        $result = $this->db->conn_id->prepare($sql);
        $result->execute();
        $result = $result->fetchAll();
        return $result;
    }

    function setFinishAdmin($TransactionCode, $OperatorCode) {
        $sql = "update wms.MasterTaskPck set isFinish=1,UpdateUserId='" . $OperatorCode . "', UpdateTime=getdate()
                where TransactionCode='" . $TransactionCode . "'";
        $result = $this->db->conn_id->prepare($sql);
        if ($result->execute()) {
            return true;
        }
        return false;
    }

    function setPenyelesaian($TransactionCode, $KodeNota, $Tipe) {
        $sql = "insert into wms.DetailTaskAddTrans(TransactionCode,Kodenota,Tipe) values('" . $TransactionCode . "','" . $KodeNota . "','" . $Tipe . "')";
        $result = $this->db->conn_id->prepare($sql);
        if ($result->execute()) {
            return true;
        }
        return false;
    }

    function removePenyelesaian($TransactionCode, $KodeNota) {
        $sql = "delete from wms.DetailTaskAddTrans where TransactionCode='" . $TransactionCode . "' and Kodenota='" . $KodeNota . "'";

        $result = $this->db->conn_id->prepare($sql);
        if ($result->execute()) {
            return true;
        }
        return false;
    }

    function cekKodeNota($KodeNota, $ERPCode) {
        if (substr($KodeNota, 0, 5) == substr($ERPCode, 0, 5)) {
            $sql = "select count(*) as jumlah from MasterJual where KodeNota='" . $KodeNota . "'";
            $result = $this->db->conn_id->prepare($sql);
            $result->execute();
            $result = $result->fetchAll();
            if ($result[0]['jumlah'] > 0) {
                $sql = "select count(*) as jumlah from wms.DetailTaskAddTrans where Kodenota='" . $KodeNota . "' and Tipe='jual'";
                $result = $this->db->conn_id->prepare($sql);
                $result->execute();
                $result = $result->fetchAll();
                if ($result[0]['jumlah'] > 0) {
                    return false;
                }
                return true;
            }
            return false;
        }
        return false;
    }
	
	public function getListEditPicklist()
	{
		$sql	= "Select TransactionCode from wms.detailtaskpckpl group by transactioncode";
		$result = $this->db->conn_id->prepare($sql);
        $result->execute();
        $result = $result->fetchAll();
        return $result;
	}
	
	public function getEdit($tr)
	{
		$sql	= "select * from wms.detailTaskPckPl where TransactionCode = '".$tr."'";
		$result = $this->db->conn_id->prepare($sql);
        $result->execute();
        $result = $result->fetchAll();
        return $result;
	}
	
	public function hapusPicking($tr,$pl)
	{
		$sql = "delete from wms.detailTaskPckPl where TransactionCode = '".$tr."' and PickList = '".$pl."'";
		// echo $sql;
		$result = $this->db->conn_id->prepare($sql);
        $result->execute();
        $result = $result->fetchAll();
        return $result;
	}
	
	public function hapusPickingAll($tr)
	{
		$sql = "delete from wms.detailTaskPckPl where TransactionCode = '".$tr."'";
		// echo $sql;
		$result = $this->db->conn_id->prepare($sql);
        $result->execute();
        $result = $result->fetchAll();
        return $result;
	}
        function getAssigned($TransactionCode){
        $sql = "select d.TransactionCode,o.OperatorCode,o.Name,d.OprRole,r.Name as NamaRole,d.Assigned 
                from wms.DetailTaskOpr d
                inner join wms.WHRole r
                on r.WHRoleCode=d.OprRole
                inner join wms.Operator o
                on o.OperatorCode=d.OperatorCode
                where d.TransactionCode='".$TransactionCode."'";
        $result = $this->db->conn_id->prepare($sql);
        $result->execute();
        $result = $result->fetchAll();
        return $result;
    }
    function getRole($TransactionCode){
        $sql = "select r.WHRoleCode,r.Name
                from wms.MasterTaskPck m
                inner join wms.ProjectWHRole p
                on p.ProjectCode=m.ProjectCode
                inner join wms.WHRole r
                on p.WHRoleCode=r.WHRoleCode
                where m.TransactionCode='".$TransactionCode."'";
        $result = $this->db->conn_id->prepare($sql);
        $result->execute();
        $result = $result->fetchAll();
        return $result;
    }
    function getOperator($WHRoleCode,$TransactionCode){
        $sql = "select o.OperatorCode,o.Name
                from wms.OperatorWHRole ow
                inner join wms.Operator o
                on ow.OperatorCode=o.OperatorCode
                where ow.WHRoleCode='".$WHRoleCode."' and not exists 
                (select * from wms.DetailTaskOpr
                where TransactionCode='".$TransactionCode."' 
                and OprRole='".$WHRoleCode."' and OperatorCode=o.OperatorCode)";
        $result = $this->db->conn_id->prepare($sql);
        $result->execute();
        $result = $result->fetchAll();
        return $result;
    }
    function deleteAssigned($TransactionCode,$OperatorCode,$OprRole)
    {
        $sql = "delete from wms.DetailTaskOpr where TransactionCode='".$TransactionCode."' and OperatorCode='".$OperatorCode."' and OprRole='".$OprRole."'";
        $result = $this->db->conn_id->prepare($sql);
        if($result->execute())
        {
            return TRUE;
        }
        return false;
    }
    function setAssigned($TransactionCode,$OperatorCode,$OprRole)
    {
        $sql = "insert into wms.DetailTaskOpr(TransactionCode,OperatorCode,OprRole) 
            values('".$TransactionCode."','".$OperatorCode."','".$OprRole."')";
        $result = $this->db->conn_id->prepare($sql);
        if($result->execute())
        {
            return TRUE;
        }
        return false;
    }

}

?>
