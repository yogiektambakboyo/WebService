<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of admin_transfermasuk
 *
 * @author USER
 */
class admin_transfermasuk_model extends CI_Model {

    //put your code here

    function getTransferMasuk($tgl, $cabang) {
        $sql = "select distinct(m.KodeNota),g.Keterangan as Perusahaan,m.Tgl,m.Keterangan from
                mastertransfer m
                inner join detailtransfer d
                on m.kodenota=d.kodenota
                inner join wms.gudangwms gw
                on gw.kode=d.TujuanGudang and gw.kode<>d.AsalGudang
                left join gudang g
                on d.AsalGudang=g.kode
                where m.KodeNota like '".$cabang."/T%' and m.Tgl='".$tgl."'
                and not exists (select * from wms.MasterTaskRcv where ERPCode=m.Kodenota)
                and m.Kodenota not in (select Kodenota from wms.DetailTaskAddTrans)
                order by m.kodenota";
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

    function setTransferMasuk($kode_proyek, $bpb, $tanggal, $siteId, $operatorCode) {
        $sql = "exec wms.spCreateTaskReceive '" . $kode_proyek . "','" . $bpb . "','" . $tanggal . "','" . $siteId . "','" . $operatorCode . "'";
        $result = $this->db->conn_id->prepare($sql);
        if ($result->execute()) {
            return TRUE;
        } else {
            return FALSE;
        }
    }
    
    function getadmin_transfermasukList() {
        $sql = "select t.TransactionDate,t.isFinishMove,t.transactionCode,t.projectCode,t.note,t.isFinish,t.isCancel,t.ERPCode,
                (select keterangan from gudang where kode=
                (select max(d.AsalGudang) from mastertransfer m,detailtransfer d where m.kodenota=d.kodenota and m.kodenota=t.ERPCode)) as perusahaan
                from wms.MasterTaskRcv t
                where t.isFinish=0 and t.isCancel=0 and t.ProjectCode='RTS'
                order by TransactionDate desc";
        $result = $this->db->conn_id->prepare($sql);
        $result->execute();
        $result = $result->fetchAll();
        return $result;
    }

    function getJumlahNota($tahun, $siteId) {
        $sql = "select count(*) jumlah
                from wms.MasterTaskRcv
                where transactionCode like '" . $tahun . "/" . $siteId . "/%'";
        $result = $this->db->conn_id->prepare($sql);
        $result->execute();
        $result = $result->fetchAll();
        return $result[0]['jumlah'];
    }

    

    function cekPembatalan($TransactionCode) {
        $sql = "select count(*) as jumlah from wms.DetailTaskRcv where TransactionCode='" . $TransactionCode . "'";
        $result = $this->db->conn_id->prepare($sql);
        $result->execute();
        $result = $result->fetchAll();
        if ($result[0]['jumlah'] == 0) {
            return true;
        }
        return false;
    }

    function getDetailTransaction($TransactionCode) { //keterangan admin_transfermasuk
        $sql = "select t.transactionCode,t.projectCode,t.note,t.isFinish,t.isCancel,t.ERPCode,
            (select keterangan from gudang where kode=
            (select max(d.AsalGudang) from mastertransfer m,detailtransfer d where m.kodenota=d.kodenota and m.kodenota=t.ERPCode)) as perusahaan
                from wms.MasterTaskRcv t
                where t.TransactionCode = '" . $TransactionCode . "'";
        $result = $this->db->conn_id->prepare($sql);
        $result->execute();
        $result = $result->fetchAll();
        return $result[0];
    }

    function getTransactionDate($TransactionCode){
        $sql = "select TransactionDate from wms.MasterTaskRcv where TransactionCode='".$TransactionCode."'";
        $result = $this->db->conn_id->prepare($sql);
        $result->execute();
        $result = $result->fetchAll();
        return date("Y/m/d",strtotime($result[0]['TransactionDate']));
    }
    function getDetailTransaction2($TransactionCode,$ReceiveSource) { //detail admin_transfermasuk
        $sql = "select (select Name from wms.RackSlot where RackSlotCode=dh.CurrRackSlot) as RackSlotSekarang,(select Name from wms.RackSlot where RackSlotCode=dt.DestRackSlot) as RackSlotTujuan,dt.*,dbo.KonversiSatuanToText(dt.SKUCode,dt.Qty) as Qtykonversi,convert(varchar(10),dt.ExpDate,121) tanggalExp,dt.CreateTime waktuBuat,b.keterangan
                from wms.DetailTaskRcv dt
                join barang b 
                on dt.SKUCode = b.Kode
                inner join wms.DetailTaskRcvHistory dh
                on dt.TransactionCode=dh.TransactionCode and dt.NoUrut=dh.NoUrut
                where dt.TransactionCode =  '" . $TransactionCode . "' and dt.BinCode<>'".$ReceiveSource."'
                and dh.QueueNumber=
                (select top 1 QueueNumber from wms.DetailTaskRcvHistory where TransactionCode=dt.TransactionCode and NoUrut=dt.NoUrut order by QueueNumber desc)";
        $result = $this->db->conn_id->prepare($sql);
        $result->execute();
        $result = $result->fetchAll();
        return $result;
    }

    function getDetailTransaction4($TransactionCode, $NoUrut) { //detail admin_transfermasuk dengan bin
        $sql = "select dt.*,de.RatioName,(dt.Qty/de.Ratio) as jumlah,r.Name as DestRackName,convert(varchar(10),dt.ExpDate,121) tanggalExp,CONVERT(VARCHAR(8),dt.CreateTime,108) waktuBuat,b.Keterangan
                from wms.DetailTaskRcv dt
                join barang b on dt.SKUCode = b.Kode
                left join wms.RackSlot r on dt.DestRackSlot=r.RackSlotCode
                left join wms.DetailTaskDERP de on de.TransactionCode=dt.TransactionCode and de.SKUCode=dt.SKUCode
                where dt.TransactionCode =  '" . $TransactionCode . "' and dt.NoUrut='" . $NoUrut . "'";
        $result = $this->db->conn_id->prepare($sql);
        $result->execute();
        $result = $result->fetchAll();
        return $result[0];
    }

    function getDetailTransaction3($TransactionCode, $NoUrut) { //detail admin_transfermasuk history
        $sql = "select dth.*,
            (select Name from wms.RackSlot where RackSlotCode=dth.CurrRackSlot) as CurrRackName,
            (select Name from wms.RackSlot where RackSlotCode=dth.DestRackSlot) as DestRackName,
            (select Name from wms.Operator where OperatorCode=dth.User_1st) as UserPertama,
            (select Name from wms.Operator where OperatorCode=dth.User_2nd) as UserKedua
            ,b.Keterangan,dth.Time_1st Waktu1,dth.Time_2nd Waktu2
                from wms.DetailTaskRcvHistory dth
                join barang b on dth.SKUCode = b.Kode
                where dth.TransactionCode='" . $TransactionCode . "' and dth.NoUrut = '" . $NoUrut . "'";
        $result = $this->db->conn_id->prepare($sql);
        $result->execute();
        $result = $result->fetchAll();
        return $result;
    }

    function getNote($TransactionCode, $NoUrut) {
        $sql = "select dt.TransactionCode,dt.NoUrut,dt.Note,dt.BinCode,t.ERPCode 
                from wms.DetailTaskRcv dt 
                inner join wms.MasterTaskRcv t
                on t.TransactionCode=dt.TransactionCode
                where dt.TransactionCode='" . $TransactionCode . "' and dt.NoUrut = '" . $NoUrut . "'";

        $result = $this->db->conn_id->prepare($sql);
        $result->execute();
        $result = $result->fetchAll();
        return $result[0];
    }

    function setNote($TransactionCode, $NoUrut, $Note) {
        if (trim($Note, " ") != "") {
            $sql = "update wms.DetailTaskRcv set Note='" . $Note . "'
                where TransactionCode='" . $TransactionCode . "' and NoUrut = '" . $NoUrut . "'";
        } else {
            $sql = "update wms.DetailTaskRcv set Note=NULL
                where TransactionCode='" . $TransactionCode . "' and NoUrut = '" . $NoUrut . "'";
        }
        $result = $this->db->conn_id->prepare($sql);
        if ($result->execute()) {
            return true;
        }
        return false;
    }

    function setDestRackSlot($TransactionCode, $NoUrut, $RackSlotCode) {

        $sql = "update wms.DetailTaskRcv set DestRackSlot=" . $RackSlotCode . "
                where TransactionCode='" . $TransactionCode . "' and NoUrut = '" . $NoUrut . "'";
        $result = $this->db->conn_id->prepare($sql);
        if ($result->execute()) {
            return true;
        }
        return false;
    }

    function getNoteMaster($TransactionCode) {
        $sql = "select * 
                from wms.MasterTaskRcv 
                where TransactionCode='" . $TransactionCode . "'";

        $result = $this->db->conn_id->prepare($sql);
        $result->execute();
        $result = $result->fetchAll();
        return $result[0];
    }

    function setNoteMaster($TransactionCode, $Note) {
        if (trim($Note, " ") != "") {
            $sql = "update wms.MasterTaskRcv set Note='" . $Note . "'
                where TransactionCode='" . $TransactionCode . "'";
        } else {
            $sql = "update wms.MasterTaskRcv set Note=NULL
                where TransactionCode='" . $TransactionCode . "'";
        }
        $result = $this->db->conn_id->prepare($sql);
        if ($result->execute()) {
            return true;
        }
        return false;
    }

    function setNoteMasterPembatalan($TransactionCode, $Note, $OperatorCode) {

        $sql = "update wms.MasterTaskRcv set Note='" . $Note . "',isCancel=1,UpdateUserId='" . $OperatorCode . "', UpdateTime=getdate()
                where TransactionCode='" . $TransactionCode . "'";
        $result = $this->db->conn_id->prepare($sql);
        if ($result->execute()) {
            return true;
        }
        return false;
    }

    function setNotePembatalan($TransactionCode, $NoUrut, $QueueNumber, $OperatorCode,$ReceiveProblem) {

        $sql = "update wms.DetailTaskRcvhistory set User_2nd='" . $OperatorCode . "', Time_2nd=getdate(), DestRackSlot=CurrRackSlot, DestOnAisle=0, DestBin='".$ReceiveProblem."', DestQty=Qty
                where TransactionCode='" . $TransactionCode . "' and NoUrut='" . $NoUrut . "' and QueueNumber='" . $QueueNumber . "'";

        $result = $this->db->conn_id->prepare($sql);

        if ($result->execute()) {
            return true;
        }
        return false;
    }

    function getstatustask($TransactionCode) {
        $sql = "select isFinishMove,isFinish,isCancel
                from wms.MasterTaskRcv
                where TransactionCode='" . $TransactionCode . "'";

        $result = $this->db->conn_id->prepare($sql);
        $result->execute();
        $result = $result->fetchAll();
        return $result[0];
    }

    function cekOutstandingFinishMove($TransactionCode) {
        $sql = "select h.*,o.Name as OperatorName,b.Keterangan,
                (select Name from wms.RackSlot where RackSlotCode=h.CurrRackSlot) as CurrRack
                from wms.DetailTaskRcvHistory h
                left join wms.Operator o
                on h.User_1st=o.OperatorCode
                left join Barang b
                on h.SKUCode=b.Kode
                where h.User_2nd is NULL and 
                h.TransactionCode='" . $TransactionCode . "'";
        $result = $this->db->conn_id->prepare($sql);
        $result->execute();
        $result = $result->fetchAll();
        return $result;
    }

    function cekOutstandingFinishAdmin($TransactionCode) {
        $sql = "exec wms.spCekPendingItemRcv '" . $TransactionCode . "'";
        $result = $this->db->conn_id->prepare($sql);
        $result->execute();
        $result = $result->fetchAll();
        return $result;
    }

    function setFinishMove($TransactionCode, $OperatorCode) {
        $sql = "update wms.MasterTaskRcv set isFinishMove=1,UpdateUserId='" . $OperatorCode . "', UpdateTime=getdate()
                where TransactionCode='" . $TransactionCode . "'";
        $result = $this->db->conn_id->prepare($sql);
        if ($result->execute()) {
            return true;
        }
        return false;
    }

    function setFinishAdmin($TransactionCode, $OperatorCode) {
        $sql = "update wms.MasterTaskRcv set isFinish=1,UpdateUserId='" . $OperatorCode . "', UpdateTime=getdate()
                where TransactionCode='" . $TransactionCode . "'";
        $result = $this->db->conn_id->prepare($sql);
        if ($result->execute()) {
            return true;
        }
        return false;
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

    function cekKodeNota($KodeNota, $Tipe, $ERPCode) {
        if (substr($KodeNota, 0, 5) == substr($ERPCode, 0, 5)) {
            if ($Tipe == 'jual') {
                $sql = "select count(*) as jumlah from MasterJual where KodeNota='" . $KodeNota . "'";
            } else {
                $sql = "select count(*) as jumlah from MasterBeli where KodeNota='" . $KodeNota . "'";
            }
            $result = $this->db->conn_id->prepare($sql);
            $result->execute();
            $result = $result->fetchAll();
            if ($result[0]['jumlah'] > 0) {
                $sql = "select count(*) as jumlah from wms.DetailTaskAddTrans where Kodenota='" . $KodeNota . "' and Tipe='" . $Tipe . "'";
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

    function buka_finishmove($TransactionCode) {
        $sql = "update wms.MasterTaskRcv set isFinishMove=0 where TransactionCode='" . $TransactionCode . "'";

        $result = $this->db->conn_id->prepare($sql);
        if ($result->execute()) {
            return true;
        }
        return false;
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
                from wms.MasterTaskRcv m
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
