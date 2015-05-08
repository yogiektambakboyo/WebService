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
class Bpb_model extends CI_Model {

    //put your code here

    function tes() {
        $sql = "exec wms.sptesting";
        $result = $this->db->conn_id->prepare($sql);
        if (!$result->execute()) {
            echo "-" . $this->db->conn_id->errorInfo() . "-";
        }
        $result = $result->fetch(PDO::FETCH_OBJ);
        return $result;
    }

    

    function getDetailKodeNota($transactionCode) {
        $sql = "select t.transactionCode,t.ERPCode  as kodeNota,
                (case when t.ProjectCode='BPB' then 
                (select s.perusahaan from supplier s,masterbeli m where m.supplier=s.kode and m.kodenota=t.ERPCode )
                else 
                (select keterangan from gudang where kode=(select max(d.AsalGudang) from mastertransfer m,detailtransfer d where m.kodenota=d.kodenota and m.kodenota=t.ERPCode)) end) as perusahaan
                ,t.note as keterangan
                from wms.mastertaskrcv t
                where t.transactionCode = '" . $transactionCode . "'";
        var_dump($sql);
        $result = $this->db->conn_id->prepare($sql);
        $result->execute();
        $result = $result->fetchAll();
        return $result[0];
    }

    function getTodayBPBList($OperatorCode) {
        $sql = "select t.transactionCode,t.ERPCode  as kodeNota,
                (case when t.ProjectCode='BPB' then 
                (select s.perusahaan from supplier s,masterbeli m where m.supplier=s.kode and m.kodenota=t.ERPCode )
                else 
                (select keterangan from gudang where kode=(select max(d.AsalGudang) from mastertransfer m,detailtransfer d where m.kodenota=d.kodenota and m.kodenota=t.ERPCode)) end) as perusahaan
                ,t.Note,(case when o.Assigned is null then 2 else cast(o.Assigned as int) end) as Assigned
                from wms.masterTaskRcv t
                left join wms.DetailTaskOpr o
                on o.TransactionCode=t.TransactionCode and o.OprRole='10/WHR/001' and o.OperatorCode='".$OperatorCode."'
                where t.isFinish='0' and t.isFinishMove='0' and t.isCancel='0' and (t.ProjectCode='BPB' or t.ProjectCode='RTS') ";
        $result = $this->db->conn_id->prepare($sql);
        $result->execute();
        $result = $result->fetchAll();
        return $result;
    }

    function isBarang($kodeBarang,$TransactionCode) {
        $sql = "select * from wms.DetailTaskDERP
                where TransactionCode='".$TransactionCode."' and SKUCode='" . $kodeBarang . "'";
        $result = $this->db->conn_id->prepare($sql);
        $result->execute();
        $result = $result->fetchAll();
        if (count($result) > 0) {
            return TRUE;
        } else {
            return FALSE;
        }
    }

    function isRackSlot($rackSlotCode) {
        $sql = "select RackSlotCode
                from wms.rackslot
                where RackSlotCode='" . $rackSlotCode . "'";
        $result = $this->db->conn_id->prepare($sql);
        $result->execute();
        $result = $result->fetchAll();
        if (count($result) > 0) {
            return TRUE;
        } else {
            return FALSE;
        }
    }

    function cari_barang($kodeNota, $cari) {
        $sql = "select m.ERPCode as kodeNota,b.Kode as kode,b.keterangan,dd.Ratio,dd.RatioName
                from wms.DetailTaskDERP dd
                inner join
                barang b
                on b.Kode=dd.SKUCode
                inner join wms.MasterTaskRcv m
                on m.TransactionCode=dd.TransactionCode
                where dd.TransactionCode='".$kodeNota."' and
                    (dd.Qty*dd.Ratio)>(select isnull(SUM(DestQty),0) from wms.DetailTaskRcv d
                    inner join wms.BinImaginer i
                    on d.BinCode=i.ReceiveSource where TransactionCode='".$kodeNota."' and SKUCode=dd.SKUCode)
                    and
                    (b.ItemBarcode='".$cari."' 
                        or b.ShipperBarcode='".$cari."' 
                            or b.BundleBarcode='".$cari."'
                                or b.keterangan like '%".$cari."%')";      
      
        $result = $this->db->conn_id->prepare($sql);
        $result->execute();
        $result = $result->fetchAll();
        return $result;
    }

    function getDetailBarang($kodeBarang) {
        $sql = "select kode,keterangan
                from barang
                where kode='" . $kodeBarang . "' or bundleBarcode='" . $kodeBarang . "'";
        $result = $this->db->conn_id->prepare($sql);
        $result->execute();
        $result = $result->fetchAll();
        return $result[0];
    }
    function getQtyDERP($TransactionCode,$SKUCode) {
        $sql = "select Qty*Ratio as jumlah from wms.DetailTaskDERP where TransactionCode='".$TransactionCode."' and SKUCode='".$SKUCode."'";
        $result = $this->db->conn_id->prepare($sql);
        $result->execute();
        $result = $result->fetchAll();
        return $result[0]['jumlah'];
    }

    function getQtyRcv($TransactionCode,$SKUCode,$ReceiveSource) {
        $sql = "select sum(Qty) as jumlah from wms.DetailTaskRcv where BinCode='".$ReceiveSource."' and SKUCode='".$SKUCode."' and TransactionCode='".$TransactionCode."'";
        $result = $this->db->conn_id->prepare($sql);
        $result->execute();
        $result = $result->fetchAll();
        return $result[0]['jumlah'];
    }
    function getQtyKonversi($SKUCode,$Qty) {
        $sql = "select dbo.KonversiSatuanToText('".$SKUCode."',".$Qty.") as Qtykonversi";
        $result = $this->db->conn_id->prepare($sql);
        $result->execute();
        $result = $result->fetchAll();
        return $result[0]['Qtykonversi'];
    }
    
    function isKodeOrBarcodeBarang($kodeNota, $kodeBarang) {
        $sql = "select mb.kodeNota,b.kode,b.keterangan
                from masterbeli mb
                join detailbeli db on mb.kodeNota=db.kodeNota
                join barang b on db.brg=b.kode
                where mb.kodeNota='" . $kodeNota . "' and (kode='" . $kodeBarang . "' or bundleBarcode='" . $kodeBarang . "')";
        $result = $this->db->conn_id->prepare($sql);
        $result->execute();
        $result = $result->fetchAll();
        if (count($result) > 0) {
            return TRUE;
        } else {
            $sql = "select count(*) as jumlah
                from mastertransfer m
                join detailtransfer d on m.kodeNota=d.kodeNota
                join barang b on d.brg=b.kode
                where m.kodeNota='" . $kodeNota . "' and (b.kode='" . $kodeBarang . "' or b.bundleBarcode='" . $kodeBarang . "')";
                $result = $this->db->conn_id->prepare($sql);
                $result->execute();
                $result = $result->fetchAll();
                if($result[0]['jumlah']>0)
                {
                    return TRUE;
                }
                return FALSE;
        }
    }

    function getEDLama($transactionCode, $kodeBarang) {
        $sql = "select top 1 convert(varchar(10),ExpDate,111) ExpDate
                from wms.DetailTaskRcv 
                where TransactionCode='" . $transactionCode . "' and SKUCode='" . $kodeBarang . "' 
                order by CreateTime desc";
        
        $result = $this->db->conn_id->prepare($sql);
        $result->execute();
        $result = $result->fetchAll();
        if (count($result) > 0) {
            return $result[0]['ExpDate'];
        } else {
            return NULL;
        }
    }

    function getRasioBarang($transactionCode, $kodeBarang) {
        $sql = "select ratio,ratioName
                from wms.DetailTaskDERP
                where TransactionCode = '" . $transactionCode . "' and SKUCode = '" . $kodeBarang . "'";
        $result = $this->db->conn_id->prepare($sql);
        $result->execute();
        $result = $result->fetchAll();
        if (count($result) > 0) {
            return $result[0];
        } else {
            return NULL;
        }
    }

    function isDetailTransactionOpr($TransactionCode, $OperatorCode, $role) {
        $sql = "select TransactionCode
                from wms.DetailTaskOpr
                where TransactionCode='" . $TransactionCode . "' and OperatorCode='" . $OperatorCode . "' and OprRole='" . $role . "'";
        $result = $this->db->conn_id->prepare($sql);
        $result->execute();
        $result = $result->fetchAll();
        if (count($result) > 0) {
            return TRUE;
        } else {
            return FALSE;
        }
    }

    function setDetailTransactionOpr($TransactionCode, $OperatorCode, $role) {
        $sql = "insert into wms.DetailTaskOpr values('" . $TransactionCode . "','" . $OperatorCode . "','" . $role . "','1')";
        $result = $this->db->conn_id->prepare($sql);
        $result->execute();
    }

    function updateDetailTransactionOpr($TransactionCode, $OperatorCode, $role) {
        $sql = "update wms.DetailTaskOpr set Assigned='1' where TransactionCode='" . $TransactionCode . "' and OperatorCode='" . $OperatorCode . "' and OprRole='" . $role . "'";
        $result = $this->db->conn_id->prepare($sql);
        $result->execute();
    }
    
    function isUsedBin($kodeBin) {
        $sql = "select isUsed
                from wms.bin
                where BinCode='" . $kodeBin . "'";
        $result = $this->db->conn_id->prepare($sql);
        $result->execute();
        $result = $result->fetchAll();
        if (count($result) > 0 && $result[0]['isUsed'] == "0") { //boleh dipake
            return TRUE;
        } else {
            return FALSE;
        }
    }

    function setDetailTransaction($TransactionCode, $BinCode, $SKUCode, $ExpDate, $Qty, $CurrRackSlot, $DestRackSlot,$ReceiveSource,$OperatorCode) {
        $sql = "select count(*) as jumlah from wms.DetailTaskRcv where TransactionCode='".$TransactionCode."'";
        $NoUrut = $this->db->conn_id->prepare($sql);
        $NoUrut->execute();
        $NoUrut = $NoUrut->fetchAll();
        $No=$NoUrut[0]['jumlah'];
        $sql = "insert into wms.DetailTaskRcv(TransactionCode,NoUrut,BinCode,SKUCode,ExpDate,Qty,CurrRackSlot,CurrOnAisle,DestOnAisle,DestBin,DestQty,CreateUserId)
                values('" . $TransactionCode . "',".($No+1).",'".$ReceiveSource."','" . $SKUCode . "','" . $ExpDate . "','" . $Qty . "','" . $CurrRackSlot . "','1','1','" . $BinCode . "','" . $Qty . "','".$OperatorCode."')";
        //echo $sql;
        $result = $this->db->conn_id->prepare($sql);
        if ($result->execute()) { //bisa insert yang pertama, baru lanjut yang kedua
            $sql = "insert into wms.DetailTaskRcv(TransactionCode,NoUrut,BinCode,SKUCode,ExpDate,Qty,CurrRackSlot,CurrOnAisle,DestOnAisle,CreateUserId) 
                values('" . $TransactionCode . "',".($No+2).",'" . $BinCode . "','" . $SKUCode . "','" . $ExpDate . "','" . $Qty . "','" . $CurrRackSlot . "','1','0','".$OperatorCode."')";
           // echo $sql;
            $result = $this->db->conn_id->prepare($sql);
            if ($result->execute()) {
                return TRUE;
            } else {
                return FALSE;
            }
        } else {
            return FALSE;
        }
    }
    function listbrg($TransactionCode,$OperatorCode) {
        $sql = "select m.ERPCode,b.Keterangan,d.DestBin as BinCode,d.ExpDate,dbo.KonversiSatuanToText(d.SKUCode,d.Qty) as Qty,r.Name
                from wms.MasterTaskRcv m
                inner join wms.DetailTaskRcv d
                on m.TransactionCode=d.TransactionCode
                inner join wms.BinImaginer i
                on d.BinCode=i.ReceiveSource
                left join barang b
                on b.Kode=d.SKUCode
                left join wms.RackSlot r
                on r.RackSlotCode=d.CurrRackSlot
                where m.TransactionCode='".$TransactionCode."' and d.CreateUserId='".$OperatorCode."'";
        $result = $this->db->conn_id->prepare($sql);
        $result->execute();
        $result = $result->fetchAll();
        return $result;
    }
    function getlastbrg($TransactionCode,$SKUCode){
        $sql = "select bg.kode,bg.keterangan from wms.DetailTaskRcv d 
                inner join wms.BinImaginer b
                on d.BinCode=b.ReceiveSource
                left join wms.DetailTaskDERP dd
                on d.TransactionCode=dd.TransactionCode and d.SKUCode=dd.SKUCode
                left join barang bg
                on bg.Kode=d.SKUCode
                where d.TransactionCode='".$TransactionCode."' and bg.kode='".$SKUCode."'
                group by bg.kode,dd.Qty,dd.Ratio,bg.keterangan
                having sum(d.DestQty)<(dd.Qty*dd.Ratio)";
        $result = $this->db->conn_id->prepare($sql);
        $result->execute();
        $result = $result->fetchAll();
        return $result[0];
    }

    function getEDdefault($kodeBarang,$EDinput) {
        
        $sql = "select top 1 convert(varchar(10),
            (case when CAST((case when len('".$EDinput."')=0 then getdate() else '".$EDinput."' end) AS DATETIME)<=getdate() 
                then (CAST((case when len('".$EDinput."')=0 then getdate() else '".$EDinput."' end) AS DATETIME)+isnull(CasePerShelf,0)) 
                    else CAST((case when len('".$EDinput."')=0 then getdate() else '".$EDinput."' end) AS DATETIME) end ),111) as ExpDate
                from Barang 
                where Kode='" . $kodeBarang . "'";
   
        $result = $this->db->conn_id->prepare($sql);
        $result->execute();
        $result = $result->fetchAll();
        return $result[0]['ExpDate'];
        
    }
}

?>
