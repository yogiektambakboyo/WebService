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
class Receive_model extends CI_Model {

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

    function getReturList($OperatorCode) {
        /* $sql = "select b.kodenota,s.kode,s.perusahaan
          from masterbeli b
          join supplier s on s.kode=b.supplier
          where tgl=convert(varchar(10),getdate(),121)"; */
        //mengambil list BPB yang menjadi project di master transaction
        $sql = "SELECT mt.TransactionCode as TransactionCode,mt.ERPCode as ERPCode,mt.TransactionDate as TransactionDate,(case when o.Assigned is null then 2 else cast(o.Assigned as int) end) as Assigned
                FROM wms.MasterTaskRcv mt
                left join wms.DetailTaskOpr o
                on o.TransactionCode=mt.TransactionCode and o.OprRole='10/WHR/001' and o.OperatorCode='".$OperatorCode."'
                WHERE
                mt.isFinish=0 AND mt.isCancel=0 AND mt.isFinishMove=0 AND mt.ProjectCode='RJT' 
                ORDER BY mt.TransactionDate DESC";
        $result = $this->db->conn_id->prepare($sql);
        $result->execute();
        $result = $result->fetchAll();
        return $result;
    }

    function cekDetailTransactionReturOpr($retur, $OperatorCode, $OprRole) {
        //waktu memilih pick list retur dari Master transaction maka operator tersebut akan dicatatkan sekarang sedang mengambil project apa saja dan hanya tidak boleh 1 master 2/lebih opr
        //cek apakah ada opr lain yang mengambilmaster ini dengan role yg sama
        $sql2 = "SELECT OperatorCode FROM wms.DetailTaskOpr WHERE TransactionCode='" . $retur . "' AND OperatorCode<>'" . $OperatorCode . "' AND OprRole='" . $OprRole . "'";
        
        $checkexist2 = $this->db->conn_id->prepare($sql2);
        $checkexist2->execute();
        $checkexist2 = $checkexist2->fetchAll();
        
        if (count($checkexist2) > 0) {
            $sql2 = "SELECT COUNT(*) as Jumlah FROM wms.DetailTaskRcv WHERE TransactionCode='" . $retur . "'";
        
            $checkexist2 = $this->db->conn_id->prepare($sql2);
            $checkexist2->execute();
            $checkexist2 = $checkexist2->fetchAll();
            if($checkexist2[0]['Jumlah']>0)
            {
                return FALSE;   
            }
            else
            {
                $sql2 = "DELETE FROM wms.DetailTaskOpr WHERE TransactionCode='" . $retur . "' AND OperatorCode<>'" . $OperatorCode . "' AND OprRole='" . $OprRole . "'";
        
                $checkexist2 = $this->db->conn_id->prepare($sql2);
                if($checkexist2->execute())
                {
                    return TRUE;
                }
                return FALSE;
            }
        }
        return TRUE;
    }

    function setDetailTransactionReturOpr($retur, $OperatorCode, $OprRole) {

        $sql = "SELECT COUNT(*) as Jumlah FROM wms.DetailTaskOpr WHERE TransactionCode='" . $retur . "' AND OperatorCode='" . $OperatorCode . "' AND OprROle='" . $OprRole . "'";
        $checkexist = $this->db->conn_id->prepare($sql);
        $checkexist->execute();
        $checkexist = $checkexist->fetchAll();
        $jumlah = 0;
        foreach ($checkexist as $rowjum) {
            $jumlah = $rowjum['Jumlah'];
        }
        if ($jumlah == 0) {
            $sql = "INSERT INTO wms.DetailTaskOpr VALUES('" . $retur . "','" . $OperatorCode . "','" . $OprRole . "','1')";
            $input = $this->db->conn_id->prepare($sql);
            $input->execute();
        }
        else{
            $sql = "Update wms.DetailTaskOpr set Assigned='1' where TransactionCode='" . $retur . "' and OperatorCode='" . $OperatorCode . "' and OprRole='" . $OprRole . "'";
            $input = $this->db->conn_id->prepare($sql);
            $input->execute();
        }
    }

    function cekSKUDERP($str,$TransactionCode)
    {
        $sql = "SELECT count(*) as jumlah FROM wms.DetailTaskDERP WHERE TransactionCode='".$TransactionCode."' 
                AND SKUCode not in(".$str.")";
        
        $result = $this->db->conn_id->prepare($sql);
        $result->execute();
        $result = $result->fetchAll();
        if($result[0]['jumlah']>0)
        {
            return false;
        }
        return true;
    }
    function setDetailTransaction($TransactionCode, $BinCode, $SKUCode, $ExpDate, $Qty, $CurrRackSlot, $DestRackSlot,$OperatorCode,$ReceiveSource) {
        $sql = "select count(*) as jumlah from wms.DetailTaskRcv where TransactionCode='".$TransactionCode."'";
        $NoUrut = $this->db->conn_id->prepare($sql);
        $NoUrut->execute();
        $NoUrut = $NoUrut->fetchAll();
        $No=$NoUrut[0]['jumlah'];
        $sql = "insert into wms.DetailTaskRcv(TransactionCode,NoUrut,BinCode,SKUCode,ExpDate,Qty,CurrRackSlot,CurrOnAisle,DestRackSlot,DestOnAisle,DestBin,DestQty)
                values('" . $TransactionCode . "',".($No+1).",'".$ReceiveSource."','" . $SKUCode . "','" . $ExpDate . "','" . $Qty . "','" . $CurrRackSlot . "','1','" . $CurrRackSlot . "','1','" . $BinCode . "','" . $Qty . "')";
       
        $result = $this->db->conn_id->prepare($sql);
        if ($result->execute()) { //bisa insert yang pertama, baru lanjut yang kedua
            $sql = "insert into wms.DetailTaskRcv(TransactionCode,NoUrut,BinCode,SKUCode,ExpDate,Qty,CurrRackSlot,DestRackSlot,CurrOnAisle,DestOnAisle) 
                values('" . $TransactionCode . "',".($No+2).",'" . $BinCode . "','" . $SKUCode . "','" . $ExpDate . "','" . $Qty . "','" . $CurrRackSlot . "','" . $DestRackSlot . "','1','0')";
            //echo $sql;
            $result = $this->db->conn_id->prepare($sql);
            if ($result->execute()) {
                 $sql = "UPDATE wms.DetailTaskRcvHistory SET User_1st='" . $OperatorCode . "' ,Time_1st=getdate()
                WHERE TransactionCode='".$TransactionCode."'
                AND NoUrut='" . ($No+2) . "'
                AND QueueNumber=1";
                //echo $sql;
                $result = $this->db->conn_id->prepare($sql);
                if ($result->execute()) {
                    return TRUE;
                } else {
                    return FALSE;
                }
            } else {
                return FALSE;
            }
        } else {
            return FALSE;
        }
    }
    function cari_barang($kodeNota, $cari) {
        $sql = "select b.Kode,b.Keterangan 
                from wms.DetailTaskDERP dd
                inner join
                barang b
                on b.Kode=dd.SKUCode
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
    function getRackName($RackSlotCode) {
        $sql = "select Name as RackName from wms.RackSlot where RackSlotCode='".$RackSlotCode."'";

        $result = $this->db->conn_id->prepare($sql);
        $result->execute();
        $result = $result->fetchAll();
        return $result[0];
    }
    function cari_barang_ajax($cari,$TransactionCode) {
        $sql = "select b.Kode,b.Keterangan 
                from barang b
                inner join wms.DetailTaskDerp d
                on b.Kode=d.SKUcode
                where (b.ItemBarcode='".$cari."' 
                        or b.ShipperBarcode='".$cari."' 
                            or b.BundleBarcode='".$cari."') and d.TransactionCode='".$TransactionCode."'";
        $result = $this->db->conn_id->prepare($sql);
        $result->execute();
        $result = $result->fetchAll();
        return $result;
    }
    
    function getsatuan($SKUCode) {
        $sql = "SELECT Rasio,Satuan FROM satuan where Brg='" . $SKUCode . "' AND SatuanAktif=1";
     
        $result = $this->db->conn_id->prepare($sql);
        $result->execute();
        $result = $result->fetchAll();
        return $result;
    }
    function getsatuanDERP($TransactionCode,$SKUCode) {
        
        $sql = "SELECT Count(RatioName) as jumlah FROM wms.DetailTaskDERP where SKUCode='" . $SKUCode . "' AND TransactionCode='".$TransactionCode."'";
      
        $result = $this->db->conn_id->prepare($sql);
        $result->execute();
        $result = $result->fetchAll();
        $hasil="kosong";
        if($result[0]['jumlah']>0){
            $sql = "SELECT RatioName FROM wms.DetailTaskDERP where SKUCode='" . $SKUCode . "' AND TransactionCode='".$TransactionCode."'";

            $result = $this->db->conn_id->prepare($sql);
            $result->execute();
            $result = $result->fetchAll();
            $hasil=$result[0];
        }
        return $hasil;
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

}

?>
