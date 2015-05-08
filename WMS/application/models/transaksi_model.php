<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of transaksi
 *
 * @author USER
 */
class Transaksi_model extends CI_Model {

    //put your code here

    function getTodayBPBList($tgl, $cabang) {
        $sql = "select distinct(m.Kodenota),s.Perusahaan,m.Keterangan
                from masterbeli m
                inner join detailbeli db
                on m.kodeNota = db.kodeNota
                inner join wms.gudangWms gd
                on db.gudang = gd.kode
				left join supplier s
                on s.kode=m.shipfrom
                where m.kodenota like '" . $cabang . "'+'/B%' and m.tgl='" . $tgl . "'
                and m.total>0 and not exists
                (select * from wms.MasterTaskRcv where ERPCode=m.Kodenota)
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

    function getTodayTransaksiBPBList() {
        /* $sql = "select t.transactionCode,t.projectCode,t.note,t.isFinish,t.isCancel,b.kodeNota,s.perusahaan
          from wms.MasterTaskRcv t
          join masterbeli b on b.kodenota=t.ERPCode
          join supplier s on s.kode = b.supplier
          where t.TransactionDate = convert(varchar(10),getdate(),121)"; */
        $sql = "select t.TransactionDate,t.isFinishMove,t.transactionCode,t.projectCode,t.note,t.isFinish,t.isCancel,b.kodeNota,b.keterangan,s.perusahaan
                from wms.MasterTaskRcv t
                join masterbeli b on b.kodenota=t.ERPCode
                join supplier s on s.kode = b.supplier
                where t.isFinish=0 and t.isCancel=0 and t.ProjectCode='BPB'
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

    function setBPB($kode_proyek, $bpb, $tanggal, $siteId, $operatorCode) {
        $sql = "exec wms.spCreateTaskReceive '" . $kode_proyek . "','" . $bpb . "','" . $tanggal . "','" . $siteId . "','" . $operatorCode . "'";
        $result = $this->db->conn_id->prepare($sql);
        if ($result->execute()) {
            return TRUE;
        } else {
            return FALSE;
        }
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

    function getDetailTransaction($TransactionCode) { //keterangan transaksi
        $sql = "select t.transactionCode,t.projectCode,t.note,t.isFinish,t.isCancel,b.kodeNota,s.perusahaan
                from wms.MasterTaskRcv t
                join masterbeli b on b.kodenota=t.ERPCode
                join supplier s on s.kode = b.supplier
                where t.TransactionCode = '" . $TransactionCode . "'";
        $result = $this->db->conn_id->prepare($sql);
        $result->execute();
        $result = $result->fetchAll();
        return $result[0];
    }

    function getDetailTransaction2($TransactionCode,$ReceiveSource) { //detail transaksi
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

    function getDetailTransaction4($TransactionCode, $NoUrut) { //detail transaksi dengan bin
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

    function getDetailTransaction3($TransactionCode, $NoUrut) { //detail transaksi history
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
    function getTransactionDate($TransactionCode){
        $sql = "select TransactionDate from wms.MasterTaskRcv where TransactionCode='".$TransactionCode."'";
        $result = $this->db->conn_id->prepare($sql);
        $result->execute();
        $result = $result->fetchAll();
        return date("Y/m/d",strtotime($result[0]['TransactionDate']));
    }
    public function getDetailReceivingBerjalan($TransactionCode){
		$sql = " --yg belum taruh rack (yg baru input dari checker/yg baru pindah bin/sisa dari yg masuk sebagian)
					select (select Name from wms.RackSlot where RackSlotCode=d.CurrRackSlot )as RackSekarang,(select Name from wms.RackSlot where RackSlotCode=d.DestRackSlot )as RackTujuan
					,d.TransactionCode,d.ExpDate,b.keterangan
					,d.BinCode,d.SKUCode,derp.Qty*derp.ratio as QtyAsli, dbo.KonversiSatuanToText(derp.skucode,derp.Qty*derp.ratio) as QtyAsliKonv ,d.Qty as QtyMasuk, dbo.KonversiSatuanToText(d.SKUCode,d.Qty) as QtyMasukKonv
					from wms.DetailTaskRcv d
					inner join wms.BinImaginer i
					on d.BinCode<>i.ReceiveSource
					left join wms.detailtaskderp derp
					on d.SKUCode=derp.SKUCode and d.TransactionCode = derp.TransactionCode
					join barang b
					on d.SKUCode = b.kode 
					where d.Status2=0 and d.TransactionCode='".$TransactionCode."'
					union all
					--yg masuk rack dengan jumlah penuh
					select (select Name from wms.RackSlot where RackSlotCode=d.CurrRackSlot )as RackSekarang,(select Name from wms.RackSlot where RackSlotCode=d.DestRackSlot )as RackTujuan
					,d.TransactionCode,d.ExpDate,b.keterangan
					,d.DestBin,d.SKUCode,derp.Qty*derp.ratio as QtyAsli, dbo.KonversiSatuanToText(derp.skucode,derp.Qty*derp.ratio) as QtyAsliKonv ,d.Qty as QtyMasuk, dbo.KonversiSatuanToText(d.SKUCode,d.Qty) as QtyMasukKonv
					from wms.DetailTaskRcv d
					inner join wms.BinImaginer i
					on d.BinCode<>i.ReceiveSource and d.DestBin<>i.ReceiveProblem --yg tidak dianggap hilang o/ admin ikut
					left join wms.detailtaskderp derp
					on d.SKUCode=derp.SKUCode and d.TransactionCode = derp.TransactionCode
					join barang b
					on d.SKUCode = b.kode
					where d.Qty=d.DestQty and d.BinCode=d.DestBin and d.TransactionCode='".$TransactionCode."'
					union all
					--yg pindah bin tapi masuk rack
					select (select Name from wms.RackSlot where RackSlotCode=d.CurrRackSlot )as RackSekarang,(select Name from wms.RackSlot where RackSlotCode=d.DestRackSlot )as RackTujuan
					,d.TransactionCode,d.ExpDate,b.keterangan
					,d.DestBin,d.SKUCode,derp.Qty*derp.ratio as QtyAsli, dbo.KonversiSatuanToText(derp.skucode,derp.Qty*derp.ratio) as QtyAsliKonv ,d.Qty as QtyMasuk, dbo.KonversiSatuanToText(d.SKUCode,d.Qty) as QtyMasukKonv
					from wms.DetailTaskRcv d
					inner join wms.BinImaginer i
					on d.BinCode<>i.ReceiveSource and d.DestBin<>i.ReceiveProblem --yg tidak dianggap hilang o/ admin ikut
					left join wms.detailtaskderp derp
					on d.SKUCode=derp.SKUCode and d.TransactionCode = derp.TransactionCode
					join barang b
					on d.SKUCode = b.kode
					where d.Qty=d.DestQty and d.BinCode<>d.DestBin and DestOnAisle=0 and d.TransactionCode='".$TransactionCode."'
					union all
					--yg masuk rack sebagian
					select (select Name from wms.RackSlot where RackSlotCode=d.CurrRackSlot )as RackSekarang,(select Name from wms.RackSlot where RackSlotCode=d.DestRackSlot )as RackTujuan
					,d.TransactionCode,d.ExpDate,b.keterangan
					,d.DestBin,d.SKUCode,derp.Qty*derp.ratio as QtyAsli, dbo.KonversiSatuanToText(derp.skucode,derp.Qty*derp.ratio) as QtyAsliKonv ,d.Qty as QtyMasuk, dbo.KonversiSatuanToText(d.SKUCode,d.Qty) as QtyMasukKonv
					from wms.DetailTaskRcv d
					inner join wms.BinImaginer i
					on d.BinCode<>i.ReceiveSource and d.DestBin<>i.ReceiveProblem --yg tidak dianggap hilang o/ admin ikut
					left join wms.detailtaskderp derp
					on d.SKUCode=derp.SKUCode and d.TransactionCode = derp.TransactionCode
					join barang b
					on d.SKUCode = b.kode
					where d.Qty>d.DestQty and d.BinCode<>d.DestBin and DestOnAisle=0 and d.TransactionCode='".$TransactionCode."'
					union all
					/*select null as RackSekarang,'' as RackTujuan
					,'','',b.keterangan
					,'0' as DestBin,derp.SKUCode,derp.Qty*derp.ratio as QtyAsli, dbo.KonversiSatuanToText(derp.skucode,derp.Qty*derp.ratio) as QtyAsliKonv ,'',''
					from wms.detailtaskderp derp
					join barang b
					on derp.SKUCode = b.kode
					where derp.transactionCode = '".$TransactionCode."' and derp.skucode not in (select d.SKUCode
					from wms.DetailTaskRcv d
					where d.Status2=0 and d.TransactionCode='".$TransactionCode."' )
					*/


					select null,null,null,null,b.Keterangan,null,b.Kode,abs(sum(Jml)) as Qty,dbo.KonversiSatuanToText(b.Kode,abs(sum(Jml))) Jml,
					null,
					case when sum(Jml)>0 then null else '1' end as [Status]
					from barang b,
					(select SKUCode as Brg,Qty*Ratio as Jml
					from wms.DetailTaskDERP
					where TransactionCode='".$TransactionCode."'
					union all
					select SKUCode,DestQty
					from wms.DetailTaskRcv d,wms.Bin b
					where TransactionCode='".$TransactionCode."' and b.BinCode=d.BinCode and b.isStock=1
					union all
					select SKUCode,DestQty*-1
					from wms.DetailTaskRcv d,wms.Bin b
					where TransactionCode='".$TransactionCode."' and b.BinCode=d.DestBin and b.isStock=1
					union all
					select Brg,Jml*Rasio
					from wms.DetailTaskAddTrans t,detailbeli d
					where t.Tipe='Beli' and d.kodenota=t.kodenota and t.TransactionCode='".$TransactionCode."'
					union all
					select Brg,Jml*Rasio*-1
					from wms.DetailTaskAddTrans t,detailjual d
					where t.Tipe='Jual' and d.kodenota=t.kodenota and t.TransactionCode='".$TransactionCode."') a
					where a.Brg=b.Kode
					group by b.Kode,b.Keterangan
					having sum(Jml)<>0

				";
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
