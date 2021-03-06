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

    function isKodeNota($kodeNota) {
        $sql = "select kodenota
                from masterbeli
                where kodeNota='" . $kodeNota . "'";
        $result = $this->db->conn_id->prepare($sql);
        $result->execute();
        $result = $result->fetchAll();
        if (count($result) > 0) { //Ada
            return TRUE;
        } else {
            return FALSE;
        }
    }

    function getDetailKodeNota($transactionCode) {
        $sql = "select t.transactionCode,b.kodeNota,s.perusahaan
                from wms.masterTaskRcv t
                join masterBeli b on b.kodeNota=t.ERPCode
                join supplier s on s.kode=b.supplier
                where t.transactionCode = '" . $transactionCode . "'";
        $result = $this->db->conn_id->prepare($sql);
        $result->execute();
        $result = $result->fetchAll();
        return $result[0];
    }

    function getTodayBPBList() {
        $sql = "select t.transactionCode,b.kodeNota,s.perusahaan,t.Note
                from wms.masterTaskRcv t
                join masterBeli b on b.kodeNota=t.ERPCode
                join supplier s on s.kode=b.supplier
                where t.isFinish='0' and t.isFinishMove='0' and t.isCancel='0' and t.ProjectCode='BPB'";
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
        $sql = "insert into wms.DetailTaskOpr values('" . $TransactionCode . "','" . $OperatorCode . "','" . $role . "')";
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

    function setDetailTransaction($TransactionCode, $BinCode, $SKUCode, $ExpDate, $Qty, $CurrRackSlot, $DestRackSlot,$ReceiveSource) {
        $sql = "select count(*) as jumlah from wms.DetailTaskRcv where TransactionCode='".$TransactionCode."'";
        $NoUrut = $this->db->conn_id->prepare($sql);
        $NoUrut->execute();
        $NoUrut = $NoUrut->fetchAll();
        $No=$NoUrut[0]['jumlah'];
        $sql = "insert into wms.DetailTaskRcv(TransactionCode,NoUrut,BinCode,SKUCode,ExpDate,Qty,CurrRackSlot,CurrOnAisle,DestOnAisle,DestBin,DestQty)
                values('" . $TransactionCode . "',".($No+1).",'".$ReceiveSource."','" . $SKUCode . "','" . $ExpDate . "','" . $Qty . "','" . $CurrRackSlot . "','1','1','" . $BinCode . "','" . $Qty . "')";
        //echo $sql;
        $result = $this->db->conn_id->prepare($sql);
        if ($result->execute()) { //bisa insert yang pertama, baru lanjut yang kedua
            $sql = "insert into wms.DetailTaskRcv(TransactionCode,NoUrut,BinCode,SKUCode,ExpDate,Qty,CurrRackSlot,CurrOnAisle,DestOnAisle) 
                values('" . $TransactionCode . "',".($No+2).",'" . $BinCode . "','" . $SKUCode . "','" . $ExpDate . "','" . $Qty . "','" . $CurrRackSlot . "','1','0')";
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

}

?>
