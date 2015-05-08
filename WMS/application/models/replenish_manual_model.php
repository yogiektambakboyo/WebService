<?php

class replenish_manual_model extends CI_Model {

    function getTransactionCode() {
        $sql = "select cast(right(max(TransactionCode),6) as int) as jumlah from wms.MasterTaskRpl";

        $result = $this->db->conn_id->prepare($sql);
        $result->execute();
        $result = $result->fetchAll();
        $kode = '10/' . date('y') . '/L/';
        for ($i = 0; $i < (6 - strlen(((String) $result[0]['jumlah'] + 1))); $i++) {
            $kode.='0';
        }
        $kode.=(String) ($result[0]['jumlah'] + 1);
        return $kode;
    }

    function setMasterTaskRpl($TransactionCode, $OperatorCode) {

        $sql = "insert into wms.MasterTaskRpl(TransactionCode,ProjectCode,TransactionDate,CreateUserId,CreateTime)
                values('" . $TransactionCode . "','RPL',getdate(),'" . $OperatorCode . "',getdate())";


        $result = $this->db->conn_id->prepare($sql);
        if ($result->execute()) {
            return true;
        }
        return false;
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

    function updateHWCode($TransactionCode,$WHCode){
        $sql = "update wms.MasterTaskRpl set WHCodeSrc='".$WHCode."',WHCodeDest='".$WHCode."' where TransactionCode='".$TransactionCode."'";


        $result = $this->db->conn_id->prepare($sql);
        if ($result->execute()) {
            return true;
        }
        return false;
    }
    function konversisatuan($SKUCode, $Qty) {
        $sql = "select dbo.KonversiSatuanToText('" . $SKUCode . "'," . $Qty . ") as Qtykonversi";
        $result = $this->db->conn_id->prepare($sql);
        $result->execute();
        $result = $result->fetchAll();
        return $result[0]['Qtykonversi'];
    }

    function getbin($RackSlotCode) {
        $sql = "select BinCode from wms.Bin where RackSlotCode='" . $RackSlotCode . "'";
        $result = $this->db->conn_id->prepare($sql);
        $result->execute();
        $result = $result->fetchAll();
        return $result;
    }

    function getNoUrut($TransactionCode) {
        $sql = "select isnull(max(NoUrut),0)+1 as NoUrut from wms.DetailTaskRpl where TransactionCode='" . $TransactionCode . "'";
        $result = $this->db->conn_id->prepare($sql);
        $result->execute();
        $result = $result->fetchAll();
        return $result[0]['NoUrut'];
    }
    
    
    function setdetailtask($TransactionCode, $BinCode, $SKUCode, $QtyNeedStart, $ExpDate, $SrcRacSlot, $OperatorCode, $BinTemp) {
        $sql = "select count(1) as jumlah from wms.DetailTaskRpl where TransactionCode='" . $TransactionCode . "' and BinCode='" . $BinCode . "'";
        $result = $this->db->conn_id->prepare($sql);
        $result->execute();
        $result = $result->fetchAll();

        if ($result[0]['jumlah'] == 0) {

            $NoUrut = $this->getNoUrut($TransactionCode);

            $sql = "insert into wms.DetailTaskRpl(TransactionCode,NoUrut,BinCode,SKUCode,QtyNeedStart,QtyNeedNow,ExpDate,SrcRackSlot,SrcOnAisle,DestRackSlot)
                    values('" . $TransactionCode . "','" . $NoUrut . "','" . $BinCode . "','" . $SKUCode . "','" . $QtyNeedStart . "','" . $QtyNeedStart . "','" . $ExpDate . "','" . $SrcRacSlot . "',0,'" . $SrcRacSlot . "')";

            $result = $this->db->conn_id->prepare($sql);
            if ($result->execute()) {
                $sql = "update wms.DetailTaskRplHistory set User_1st='" . $OperatorCode . "',Time_1st=getdate(),
                    DestRackSlot='" . $SrcRacSlot . "',DestBin='" . $BinTemp . "',DestQty=".$QtyNeedStart.",DestOnAisle=0,User_2nd='" . $OperatorCode . "',Time_2nd=getdate() where TransactionCode='" . $TransactionCode . "' and QueueNumber=1 and NoUrut='" . $NoUrut . "'";
                $result = $this->db->conn_id->prepare($sql);
                if ($result->execute()) {
                    $NoUrut = $this->getNoUrut($TransactionCode);
                    $sql = "insert into wms.DetailTaskRpl(TransactionCode,NoUrut,BinCode,SKUCode,QtyNeedStart,QtyNeedNow,ExpDate,SrcRackSlot,SrcOnAisle)
                    values('" . $TransactionCode . "','" . $NoUrut . "','" . $BinTemp . "','" . $SKUCode . "','" . $QtyNeedStart . "','" . $QtyNeedStart . "','" . $ExpDate . "','" . $SrcRacSlot . "',0)";

                    $result = $this->db->conn_id->prepare($sql);
                    if ($result->execute()) {
                        $sql = "update wms.DetailTaskRplHistory set User_1st='" . $OperatorCode . "',Time_1st=getdate() where TransactionCode='" . $TransactionCode . "' and QueueNumber=1 and NoUrut='" . $NoUrut . "'";
                        $result = $this->db->conn_id->prepare($sql);
                        if ($result->execute()) {
                            return TRUE;
                        }
                    }
                }
            }
        } else {
            return TRUE;
        }
        return FALSE;
    }

    function getsku($BinCode) {
        $sql = "select bs.ExpDate,bi.WHCode,bs.BinCode,bs.SKUCode,b.Keterangan,bs.Qty,dbo.KonversiSatuanToText(bs.SKUCode,bs.Qty) as QtyKonversi
                from wms.BinSKU bs
                inner join Barang b
                on bs.SKUCode=b.Kode
                inner join wms.Bin bi
                on bs.BinCode=bi.BinCode
                where bs.BinCode='" . $BinCode . "'";
        $result = $this->db->conn_id->prepare($sql);
        $result->execute();
        $result = $result->fetchAll();
        return $result[0];
    }

    function getsatuan($SKUCode) {
        $sql = "select * from satuan where brg='" . $SKUCode . "' and satuanaktif=1";
        $result = $this->db->conn_id->prepare($sql);
        $result->execute();
        $result = $result->fetchAll();
        return $result;
    }

    function setDetailTransactionReplenishOpr($retur, $OperatorCode, $OprRole) {

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
        } else {
            $sql = "Update wms.DetailTaskOpr set Assigned='1' where TransactionCode='" . $retur . "' and OperatorCode='" . $OperatorCode . "' and OprRole='" . $OprRole . "'";
            $input = $this->db->conn_id->prepare($sql);
            $input->execute();
        }
    }

    function setFinish($TransactionCode) {
        $sql = "select count(*) as jumlah from wms.DetailTaskRplHistory where TransactionCode='" . $TransactionCode . "' and User_2nd is null";
        $result = $this->db->conn_id->prepare($sql);
        $result->execute();
        $result = $result->fetchAll();
        if ($result[0]['jumlah'] == 0) {
            $sql = "update wms.MasterTaskRpl set isFinish=1,isFinishMove=1 where TransactionCode='" . $TransactionCode . "'";
        
            $result = $this->db->conn_id->prepare($sql);
            if ($result->execute()) {
                return TRUE;
            }
        }
        return FALSE;
    }

    function getListTask($TransactionCode) {
        $sql = "select d.TransactionCode,d.NoUrut,d.QueueNumber,d.Qty,d.BinCode,d.SKUCode,d.ExpDate,d.SrcRackSlot,r.Name,b.Keterangan,dbo.KonversiSatuanToText(d.SKUCode,d.Qty) as QtyKonversi
                from wms.DetailTaskRplHistory d
                inner join wms.RackSlot r
                on d.SrcRackSlot=r.RackSlotCode
                inner join Barang b
                on b.Kode=d.SKUCode
                where d.User_2nd is null and d.TransactionCode='" . $TransactionCode . "'";
        $result = $this->db->conn_id->prepare($sql);
        $result->execute();
        $result = $result->fetchAll();
        return $result;
    }

    function getJmlListTask($TransactionCode) {
        $sql = "select count(*) as jumlah
                from wms.DetailTaskRpl
                where DestRackSlot is null and TransactionCode='" . $TransactionCode . "'";
        $result = $this->db->conn_id->prepare($sql);
        $result->execute();
        $result = $result->fetchAll();
        return $result[0]['jumlah'];
    }

    function getRackName($RackSlotCode) {
        $sql = "select Name as RackName from wms.RackSlot where RackSlotCode='" . $RackSlotCode . "'";

        $result = $this->db->conn_id->prepare($sql);
        $result->execute();
        $result = $result->fetchAll();
        return $result[0];
    }

    function cekRackSlotNull($RackSlotCode) {
        $sql = "select count(*) as jumlah 
                from wms.Bin 
                where RackSlotCode='" . $RackSlotCode . "'";

        $result = $this->db->conn_id->prepare($sql);
        $result->execute();
        $result = $result->fetchAll();
        if ($result[0]['jumlah'] > 0) {
            $sql = "select count(*) as jumlah 
                from wms.RackSlot 
                where RackSlotCode='" . $RackSlotCode . "' and RackLevel=1";

            $result = $this->db->conn_id->prepare($sql);
            $result->execute();
            $result = $result->fetchAll();
            if ($result[0]['jumlah'] > 0) {
                return true;
            }
            else{
                return false;
            }
        }
        else{
            return true;
        }
        
    }

    function cekBarang($BinCode, $SKUCode) {
        $sql = "select count(*) as jumlah 
                from wms.BinSKU 
                where BinCode='" . $BinCode . "' and SKUCode='" . $SKUCode . "'";

        $result = $this->db->conn_id->prepare($sql);
        $result->execute();
        $result = $result->fetchAll();
        if ($result[0]['jumlah'] > 0) {
            return TRUE;
        }
        return FALSE;
    }

    function setDetailTaskRplHistory($TransactionCode, $NoUrut, $QueueNumber, $DestRackSlot, $DestBin, $DestQty, $OperatorCode) {
        $sql = "update wms.DetailTaskRpl set DestRackSlot=" . $DestRackSlot . ",DestOnAisle=0 where TransactionCode='" . $TransactionCode . "' and NoUrut=" . $NoUrut;

        $result = $this->db->conn_id->prepare($sql);
        if ($result->execute()) {
            $sql = "update wms.DetailTaskRplHistory set DestRackSlot=" . $DestRackSlot . ",DestBin=" . $DestBin . ",DestQty=" . $DestQty . ",DestOnAisle=0,User_1st='" . $OperatorCode . "',Time_1st=getdate(),User_2nd='" . $OperatorCode . "',Time_2nd=getdate() where TransactionCode='" . $TransactionCode . "' and NoUrut=" . $NoUrut . " and QueueNumber=" . $QueueNumber;
            
            $result = $this->db->conn_id->prepare($sql);
            if ($result->execute()) {
                return TRUE;
            }
        }
        return FALSE;
    }

    function getTransactionCodeTask($OperatorCode) {

        $sql = "select count(*) as jumlah 
                from wms.MasterTaskRpl
                where isFinish=0 and CreateUserId='" . $OperatorCode . "'";

        $result = $this->db->conn_id->prepare($sql);
        $result->execute();
        $result = $result->fetchAll();
        if ($result[0]['jumlah'] > 0) {
            $sql = "select TransactionCode 
                    from wms.MasterTaskRpl
                    where isFinish=0 and CreateUserId='" . $OperatorCode . "'";

            $result = $this->db->conn_id->prepare($sql);
            $result->execute();
            $result = $result->fetchAll();
            $data = array('status' => true, 'TransactionCode' => $result[0]['TransactionCode']);
        } else {
            $data = array('status' => false);
        }
        return $data;
    }

}

?>
