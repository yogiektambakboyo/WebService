<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

class Picking_model extends CI_Model {

    function getPickList($OperatorCode) {
        /* $sql = "select b.kodenota,s.kode,s.perusahaan
          from masterbeli b
          join supplier s on s.kode=b.supplier
          where tgl=convert(varchar(10),getdate(),121)"; */
        //mengambil list BPB yang menjadi project di master transaction
        $sql = "SELECT mt.TransactionCode as TransactionCode,mt.ERPCode as ERPCode,mt.TransactionDate as TransactionDate ,WHCode as WHCode,EDPanjang as EDPanjang,(case when o.Assigned is null then 2 else cast(o.Assigned as int) end) as Assigned
                FROM wms.MasterTaskPck mt
                left join wms.DetailTaskOpr o
                on mt.TransactionCode=o.TransactionCode and o.OprRole='10/WHR/003' and o.OperatorCode='".$OperatorCode."'
                WHERE
                mt.isFinish=0 AND mt.isCancel=0 AND mt.isFinishMove=0 AND (mt.ProjectCode='PCK' or mt.ProjectCode='PTS')
                ORDER BY mt.TransactionDate DESC";
        $result = $this->db->conn_id->prepare($sql);
        $result->execute();
        $result = $result->fetchAll();
        return $result;
    }

    function setDetailTransactionOpr($picklist, $OperatorCode, $OprRole) {
        //waktu memilih BPB dari Master transaction maka operator tersebut akan dicatatkan sekarang sedang mengambil project apa saja
        //foreach($picklist as $row)
        //{
        $sql = "SELECT COUNT(*) as Jumlah FROM wms.DetailTaskOpr WHERE TransactionCode='" . $picklist . "' AND OperatorCode='" . $OperatorCode . "' AND OprROle='" . $OprRole . "'";
        $checkexist = $this->db->conn_id->prepare($sql);
        $checkexist->execute();
        $checkexist = $checkexist->fetchAll();
        $jumlah = 0;
        foreach ($checkexist as $rowjum) {
            $jumlah = $rowjum['Jumlah'];
        }
        if ($jumlah == 0) {
            $sql = "INSERT INTO wms.DetailTaskOpr VALUES('" . $picklist . "','" . $OperatorCode . "','" . $OprRole . "','1')";
            $input = $this->db->conn_id->prepare($sql);
            $input->execute();
        }
        else{
            $sql = "Update wms.DetailTaskOpr set Assigned='1' where TransactionCode='" . $picklist . "' and OperatorCode='" . $OperatorCode . "' and OprRole='" . $OprRole . "'";
            $input = $this->db->conn_id->prepare($sql);
            $input->execute();
        }
        // }
    }

    function getListDetailTaskPck($TransactionCode) {

        $sql = "exec wms.PickingTask '" . $TransactionCode . "'";
        
        $result = $this->db->conn_id->prepare($sql);
        $result->execute();
        $result = $result->fetchAll();
        return $result;
    }

    function getListMyDetailTaskPck($OperatorCode) {
        $sql = "SELECT DISTINCT m.ERPCode,d.TransactionCode,d.SKUCode,
                d.NoUrut,d.QtyNeedStart,b.Keterangan,d.Qty,d.SrcRackSlot,d.SrcBin,d.DestBin,r.Name
                FROM wms.DetailTaskPck d
                INNER JOIN 
                wms.MasterTaskPck m
                ON d.TransactionCode=m.TransactionCode
                LEFT JOIN
                dbo.Barang b
                ON b.Kode=d.SKUCode
                LEFT JOIN
                wms.RackSlot r
                ON d.SrcRackSlot=r.RackSlotCode
                WHERE d.User_1st ='" . $OperatorCode . "'
                AND d.User_2nd IS NULL AND
                d.TransactionCode IN (select distinct m.TransactionCode
                from wms.MasterTaskPck m,wms.DetailTaskOpr d
                where m.isFinish=0 and m.isCancel=0 and m.isFinishMove=0 and d.TransactionCode=m.TransactionCode and d.OperatorCode='" . $OperatorCode . "')";
        $result = $this->db->conn_id->prepare($sql);
        $result->execute();
        $result = $result->fetchAll();
        return $result;
    }

    function cekexistkodebinSKU($kodebin, $OperatorCode) {
        //cek apakah barcode bin benar2 terdaftar
        $sql = "SELECT COUNT(*) AS Jumlah FROM wms.DetailTaskPck 
                WHERE DestBin=" . $kodebin . " 
                AND User_1st='" . $OperatorCode . "' 
                AND User_2nd IS NULL
                AND TransactionCode IN
                (select distinct m.TransactionCode
                from wms.MasterTaskPck m,wms.DetailTaskOpr d
                where m.isFinish=0 and m.isCancel=0 and m.isFinishMove=0 and d.TransactionCode=m.TransactionCode and d.OperatorCode='" . $OperatorCode . "')";
        $checkexist = $this->db->conn_id->prepare($sql);
        $checkexist->execute();
        $checkexist = $checkexist->fetchAll();
        $jumlah = 0;
        foreach ($checkexist as $rowjum) {
            $jumlah = $rowjum['Jumlah'];
        }
        if ($jumlah > 0) {
            return true;
        }
        return false;
    }

    function cekbinuser_1st($kodebin, $OperatorCode) {
        //cek apakah barcode bin benar2 terdaftar
        $sql = "SELECT COUNT(*) AS Jumlah FROM wms.DetailTaskPck WHERE User_1st='" . $OperatorCode . "' AND User_2nd IS NULL AND DestBin=" . $kodebin;
        $checkexist = $this->db->conn_id->prepare($sql);
        $checkexist->execute();
        $checkexist = $checkexist->fetchAll();
        $jumlah = 0;
        foreach ($checkexist as $rowjum) {
            $jumlah = $rowjum['Jumlah'];
        }
        if ($jumlah > 0) {
            return true;
        }
        return false;
    }

    function settaruhbin($TransactionCode, $kodebin, $koderack, $OperatorCode) {

        $sql = "SELECT NoUrut FROM wms.DetailTaskPck
                WHERE User_1st='" . $OperatorCode . "' AND User_2nd IS NULL AND DestBin='" . $kodebin . "'
                AND TransactionCode='" . $TransactionCode . "'";
        $result= $this->db->conn_id->prepare($sql);
        $result->execute();
        $result = $result->fetchAll();
        
        foreach($result as $row)
        {
            $sql = "UPDATE wms.DetailTaskPck
                    SET DestRackSlot='" . $koderack . "' , User_2nd='" . $OperatorCode . "', Time_2nd=getdate()
                    WHERE NoUrut='".$row['NoUrut']."'
                    AND TransactionCode='" . $TransactionCode . "'";

            $result = $this->db->conn_id->prepare($sql);
            //////////////////////////////////////////pertanyaan No_Urut Gimana?
            if (!$result->execute()) {
                return false;
            }
        }
        return true;
    }

    function cekTaskOprKosong($OperatorCode) {//cek apakah Operator masih belum menyelesaikan task
        $sql = "select count(*) as jumlah from wms.DetailTaskPck where DestRackSlot is null and User_2nd is null and User_1st='" . $OperatorCode . "'";
        $result = $this->db->conn_id->prepare($sql2);
        $result->execute();
        $result = $result->fetchAll();
        if ($result[0]['jumlah'] > 0) {
            return false;
        }
        return TRUE;
    }

    function cekvalidasiexistkodebin($kodebin) {
        //cek apakah barcode bin benar2 terdaftar
        $sql = "SELECT COUNT(*) as Jumlah FROM
                wms.Bin
                WHERE BinCode=" . $kodebin;
        $checkexist = $this->db->conn_id->prepare($sql);
        $checkexist->execute();
        $checkexist = $checkexist->fetchAll();
        $jumlah = 0;
        foreach ($checkexist as $rowjum) {
            $jumlah = $rowjum['Jumlah'];
        }
        if ($jumlah > 0) {
            return true;
        }
        return false;
    }

    function cekBinDestfull($kodebindest) {
        //cek apakah bin dest memang memiliki isi
        $sql = "SELECT COUNT(*) as jumlah FROM wms.BinSKU WHERE BinCode=" . $kodebindest . " AND Qty>0";
        $checkexist = $this->db->conn_id->prepare($sql);
        $checkexist->execute();
        $checkexist = $checkexist->fetchAll();
        $jumlah = 1;
        foreach ($checkexist as $rowjum) {
            $jumlah = $rowjum['jumlah'];
        }

        if ($jumlah == 0) {
            return true;
        }
        return false;
    }

    function cekvalidasiexistkoderack($koderack) {

        //cek apakah barcode rakslot benar2 terdaftar
        $sql = "SELECT COUNT(*) AS jumlah from wms.RackSlot where RackSlotCode=" . $koderack;
        $checkexist = $this->db->conn_id->prepare($sql);
        $checkexist->execute();
        $checkexist = $checkexist->fetchAll();
        $jumlah2 = 0;
        foreach ($checkexist as $rowjum) {
            $jumlah2 = $rowjum['jumlah'];
        }
        if ($jumlah2 == 0) {
            return false;
        }
        return true;
    }

    function cekBinSKU($kodebin, $SKUCode) {

        //cek apakah barcode rakslot benar2 terdaftar
        $sql = "SELECT COUNT(*) as jumlah FROM wms.BinSKU WHERE BinCode=" . $kodebin . " AND Qty>0 AND SKUCode='" . $SKUCode . "'";
        ;
        $checkexist = $this->db->conn_id->prepare($sql);
        $checkexist->execute();
        $checkexist = $checkexist->fetchAll();
        $jumlah2 = 0;
        foreach ($checkexist as $rowjum) {
            $jumlah2 = $rowjum['jumlah'];
        }
        if ($jumlah2 == 0) {
            return false;
        }
        return true;
    }

    function cekBinRack($kodebin, $koderack) {

        //cek apakah barcode rakslot benar2 terdaftar
        $sql = "SELECT COUNT(*) as jumlah FROM wms.Bin WHERE BinCode=" . $kodebin . " AND RackSlotCode='" . $koderack . "'";
        ;
        $checkexist = $this->db->conn_id->prepare($sql);
        $checkexist->execute();
        $checkexist = $checkexist->fetchAll();
        $jumlah2 = 0;
        foreach ($checkexist as $rowjum) {
            $jumlah2 = $rowjum['jumlah'];
        }
        if ($jumlah2 == 0) {
            return false;
        }
        return true;
    }

    function cekQtyBinSKU($kodebin, $SKUCode, $jumlah) {

        //cek apakah barcode rakslot benar2 terdaftar
        $sql = "SELECT Qty as jumlah FROM wms.BinSKU WHERE BinCode=" . $kodebin . " AND SKUCode='" . $SKUCode . "'";
        //echo $sql;
        $checkexist = $this->db->conn_id->prepare($sql);
        $checkexist->execute();
        $checkexist = $checkexist->fetchAll();
        $jumlah2 = 0;
        foreach ($checkexist as $rowjum) {
            $jumlah2 = $rowjum['jumlah'];
        }
        if ($jumlah2 < $jumlah) {
            return false;
        }
        return true;
    }

    function getsatuan($SKUCode) {
        $sql = "SELECT Rasio,Satuan FROM satuan where Brg='" . $SKUCode . "' AND SatuanAktif=1";
        $result = $this->db->conn_id->prepare($sql);
        $result->execute();
        $result = $result->fetchAll();
        return $result;
    }

    function getTempBinOutstanding($OperatorCode) {
        $sql = "select top 1 TransactionCode,DestBin from wms.DetailTaskPck where User_1st='" . $OperatorCode . "' 
            and User_2nd is null";
        //var_dump($sql);
        $result = $this->db->conn_id->prepare($sql);
        $result->execute();
        $result = $result->fetchAll();
        return $result[0];
    }

    function setambilbarang($TransactionCode, $SKUCode, $kodebin, $kodebinbawa, $koderack, $jumlah, $OperatorCode, $QtyNeedStart, $QtyNeedNow, $AddTask,$NoUrut) {
        //update bahwa barang telah diambil
        /* $sql = "UPDATE wms.DetailTaskPck
          SET SrcBin=" . $kodebin . " , SrcRackSlot=" . $koderack . " , Qty=" . $jumlah . " , User_1st='" . $OperatorCode . "', DestBin=" . $kodebinbawa . ", Time_1st=getdate()
          WHERE TransactionCode='" . $TransactionCode . "' AND NoUrut=" . $NoUrut; */
        $sql = "SELECT ExpDate FROM wms.BinSKU WHERE BinCode='".$kodebin."' AND SKUCode='".$SKUCode."'";
        $result = $this->db->conn_id->prepare($sql);
        $result->execute();
        $result = $result->fetchAll();
        $ExpDate=$result[0]['ExpDate'];
        
        $sql = "SELECT max(NoUrut) as jumlah FROM wms.DetailTaskPck where TransactionCode='" . $TransactionCode . "'";
        $result = $this->db->conn_id->prepare($sql);
        $result->execute();
        $result = $result->fetchAll();
        if ($AddTask == 1) {
             $sql = "UPDATE wms.DetailTaskPck set ExpDate='".$ExpDate."',Qty='" . $jumlah . "',SrcRackSlot='" . $koderack . "',Srcbin='" . $kodebin . "',Destbin='" . $kodebinbawa . "',User_1st='" . $OperatorCode . "',Time_1st=getdate() WHERE TransactionCode='".$TransactionCode."' and NoUrut='".$NoUrut."'";
        } else {
             $sql = "INSERT INTO wms.DetailTaskPck(ExpDate,TransactionCode,NoUrut,SKUCode,QtyNeedStart,QtyNeedNow,Qty,SrcRackSlot,SrcBin,DestBin,User_1st,Time_1st) 
            values('".$ExpDate."','" . $TransactionCode . "','" . ($result[0]['jumlah'] + 1) . "','" . $SKUCode . "','" . $jumlah . "','" . $jumlah . "','" . $jumlah . "','" . $koderack . "','" . $kodebin . "','" . $kodebinbawa . "','" . $OperatorCode . "',getdate())";
        }
       
        $result = $this->db->conn_id->prepare($sql);
        // var_dump($sql);
        if ($result->execute()) {
            return TRUE;
        }
        return FALSE;
    }

    /* function cekUser_1st($TransactionCode, $NoUrut) {
      $sql = "SELECT COUNT(*) as jumlah FROM wms.DetailTaskPck
      WHERE TransactionCode='" . $TransactionCode . "'
      AND NoUrut=" . $NoUrut . " AND User_1st IS NULL";

      $checkexist = $this->db->conn_id->prepare($sql);
      $checkexist->execute();
      $checkexist = $checkexist->fetchAll();
      $jumlah2 = 0;
      foreach ($checkexist as $rowjum) {
      $jumlah2 = $rowjum['jumlah'];
      }
      if ($jumlah2 == 0) {
      return false;
      }
      return true;
      }

      function cekUser_2nd($TransactionCode, $NoUrut) {
      $sql = "SELECT COUNT(*) as jumlah FROM wms.DetailTaskPck
      WHERE TransactionCode='" . $TransactionCode . "'
      AND NoUrut=" . $NoUrut . " AND User_2nd IS NULL";
      $checkexist = $this->db->conn_id->prepare($sql);
      $checkexist->execute();
      $checkexist = $checkexist->fetchAll();
      $jumlah2 = 0;
      foreach ($checkexist as $rowjum) {
      $jumlah2 = $rowjum['jumlah'];
      }
      if ($jumlah2 == 0) {
      return false;
      }
      return true;
      } */

    function cekBinPenampungTransaksi($TransactionCode, $BinNow, $OperatorCode) {//cek apakah bin sedang dipakai untuk task picklist lain
        $sql = "select count(*) as jumlah from wms.DetailTaskPck where Destbin=" . $BinNow . " 
            and TransactionCode<>'" . $TransactionCode . "' and User_1st='" . $OperatorCode . "' and User_2nd is null";
        $checkexist = $this->db->conn_id->prepare($sql);
        $checkexist->execute();
        $checkexist = $checkexist->fetchAll();
        $jumlah2 = 0;
        foreach ($checkexist as $rowjum) {
            $jumlah2 = $rowjum['jumlah'];
        }
        if ($jumlah2 > 0) {
            return false;
        }
        return true;
    }
    function getERPCode($TransactionCode)
    {
        $sql = "select ERPCode from wms.MasterTaskPck where TransactionCode='".$TransactionCode."'";
        //var_dump($sql);
        $result = $this->db->conn_id->prepare($sql);
        $result->execute();
        $result = $result->fetchAll();
        return $result[0];
    }
    function getSuggestion($SKUCode,$Qty,$WHCode,$EDPanjang)
    {
        $sql = "exec wms.spPickingSuggestion '".$SKUCode."','".$EDPanjang."','".$Qty."','".$WHCode."'";
        $result = $this->db->conn_id->prepare($sql);
        $result->execute();
        $result = $result->fetchAll();
        return $result;
    }

}

?>
