<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

class Storing_model extends CI_Model {

    //put your code here

    function getBPBList($OperatorCode) {
        /* $sql = "select b.kodenota,s.kode,s.perusahaan
          from masterbeli b
          join supplier s on s.kode=b.supplier
          where tgl=convert(varchar(10),getdate(),121)"; */
        //mengambil list BPB yang menjadi project di master transaction
        $sql = "SELECT mt.TransactionCode as TransactionCode,mt.ERPCode as ERPCode,
                (case when mt.ProjectCode='BPB' then 
                (select s.perusahaan from supplier s,masterbeli m where m.supplier=s.kode and m.kodenota=mt.ERPCode )
                else 
                (select keterangan from gudang where kode=(select max(d.AsalGudang) from mastertransfer m,detailtransfer d where m.kodenota=d.kodenota and m.kodenota=mt.ERPCode)) end) as Perusahaan
                ,mt.TransactionDate as TransactionDate,mt.Note,isnull(o.Assigned,2) as Assigned 
                FROM wms.MasterTaskRcv mt
                left join wms.DetailTaskOpr o
                on mt.TransactionCode=o.TransactionCode and o.OprRole='10/WHR/002' and o.OperatorCode='".$OperatorCode."'
                WHERE
                mt.isFinish=0 AND mt.isCancel=0 AND mt.isFinishMove=0 AND (mt.ProjectCode='BPB' OR mt.ProjectCode='RTS')
                ORDER BY mt.TransactionDate DESC";
        $result = $this->db->conn_id->prepare($sql);
        $result->execute();
        $result = $result->fetchAll();
        return $result;
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
                on o.TransactionCode=mt.TransactionCode and o.OprRole='10/WHR/002' and o.OperatorCode='".$OperatorCode."'
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

        $str = "";
        $counterror = 0;
        foreach ($retur as $row) {
            //cek apakah ada opr lain yang mengambilmaster ini dengan role yg sama
            $sql2 = "SELECT COUNT(*) as Jumlah FROM wms.DetailTaskOpr WHERE TransactionCode='" . $row . "' AND OperatorCode<>'" . $OperatorCode . "' AND OprRole='" . $OprRole . "'";

            $checkexist2 = $this->db->conn_id->prepare($sql2);
            $checkexist2->execute();
            $checkexist2 = $checkexist2->fetchAll();
            $jumlah2 = 0;
            foreach ($checkexist2 as $rowjum2) {
                $jumlah2 = $rowjum2['Jumlah'];
            }
            if ($jumlah2 != 0) {
                $str.="'" . $row . "',";

                $counterror++;
            }
        }

        if ($counterror > 0) {
            $str = substr($str, 0, -1);

            $sql = "select ERPCode from wms.MasterTaskRcv where TransactionCode in (" . $str . ")";

            $result = $this->db->conn_id->prepare($sql);
            $result->execute();
            $result = $result->fetchAll();
            return $result;
        }

        return NULL;
    }

    function setDetailTransactionReturOpr($retur, $OperatorCode, $OprRole) {
        //waktu memilih pick list retur dari Master transaction maka operator tersebut akan dicatatkan sekarang sedang mengambil project apa saja dan hanya tidak boleh 1 master 2/lebih opr
        foreach ($retur as $row) {
            $sql = "SELECT COUNT(*) as Jumlah FROM wms.DetailTaskOpr WHERE TransactionCode='" . $row . "' AND OperatorCode='" . $OperatorCode . "' AND OprROle='" . $OprRole . "'";
            $checkexist = $this->db->conn_id->prepare($sql);
            $checkexist->execute();
            $checkexist = $checkexist->fetchAll();
            $jumlah = 0;
            foreach ($checkexist as $rowjum) {
                $jumlah = $rowjum['Jumlah'];
            }
            //cek apakah ada opr lain yang mengambilmaster ini dengan role yg sama
            $sql2 = "SELECT COUNT(*) as Jumlah FROM wms.DetailTaskOpr WHERE TransactionCode='" . $row . "' AND OperatorCode<>'" . $OperatorCode . "' AND OprROle='" . $OprRole . "'";
            $checkexist2 = $this->db->conn_id->prepare($sql2);
            $checkexist2->execute();
            $checkexist2 = $checkexist2->fetchAll();
            $jumlah2 = 0;
            foreach ($checkexist2 as $rowjum2) {
                $jumlah2 = $rowjum2['Jumlah'];
            }
            if ($jumlah == 0 && $jumlah2 == 0) {
                $sql = "INSERT INTO wms.DetailTaskOpr VALUES('" . $row . "','" . $OperatorCode . "','" . $OprRole . "',1)";
                $input = $this->db->conn_id->prepare($sql);
                $input->execute();
            }
        }
    }

    function setDetailTransactionOpr($bpb, $OperatorCode, $OprRole) {
        //waktu memilih BPB dari Master transaction maka operator tersebut akan dicatatkan sekarang sedang mengambil project apa saja
        foreach ($bpb as $row) {
            $sql = "SELECT COUNT(*) as Jumlah FROM wms.DetailTaskOpr WHERE TransactionCode='" . $row . "' AND OperatorCode='" . $OperatorCode . "' AND OprROle='" . $OprRole . "'";
            $checkexist = $this->db->conn_id->prepare($sql);
            $checkexist->execute();
            $checkexist = $checkexist->fetchAll();
            $jumlah = 0;
            foreach ($checkexist as $rowjum) {
                $jumlah = $rowjum['Jumlah'];
            }
            if ($jumlah == 0) {
                $sql = "INSERT INTO wms.DetailTaskOpr VALUES('" . $row . "','" . $OperatorCode . "','" . $OprRole . "','1')";
                $input = $this->db->conn_id->prepare($sql);
                $input->execute();
            }
            else{
                $sql = "Update wms.DetailTaskOpr set Assigned='1' where TransactionCode='" . $row . "' and OperatorCode='" . $OperatorCode . "' and OprRole='" . $OprRole . "'";
                $input = $this->db->conn_id->prepare($sql);
                $input->execute();
            }
        }
    }

    function getListDetailTransactionHistory($OperatorCode, $OperatorRole, $ProjectCode) {

        //menampilkan daftar outstanding all yang binnya belum diambil dan dibawa sama sekali
        $sql = "SELECT DISTINCT h.NoUrut,h.QueueNumber,m.ERPCode,h.QueueNumber,h.TransactionCode,h.BinCode,h.SKUCode,b.Keterangan,h.Qty,dbo.KonversiSatuanToText(h.SKUCode,h.Qty) as Qtykonversi,de.Ratio,de.RatioName,
                (SELECT r.Name FROM wms.RackSlot r WHERE r.RackSlotCode=d.DestRackSlot) AS DestRackSlot,
                (SELECT TOP 1 r.Name FROM wms.RackSlot r WHERE r.RackSlotCode=h.CurrRackSlot ORDER BY h.QueueNumber) AS CurrRackSlot
                FROM wms.DetailTaskRcvHistory h
                INNER JOIN 
                wms.MasterTaskRcv m
                ON h.TransactionCode=m.TransactionCode
                INNER JOIN
                wms.DetailTaskRcv d
                ON h.TransactionCode=d.TransactionCode AND h.NoUrut=d.NoUrut
                INNER JOIN
                (select distinct m.TransactionCode
                from wms.MasterTaskRcv m,wms.DetailTaskOpr d
                where m.isFinish=0 AND m.isFinishMove=0 AND m.isCancel=0 and d.TransactionCode=m.TransactionCode and d.OperatorCode='" . $OperatorCode . "' and d.OprRole='" . $OperatorRole . "') a
                ON a.TransactionCode= h.TransactionCode
                LEFT JOIN
                dbo.Barang b
                ON b.Kode=h.SKUCode
                LEFT JOIN
                wms.DetailTaskDERP de
                ON de.TransactionCode=h.TransactionCode AND de.SKUCode=h.SKUCode
                WHERE h.User_1st IS NULL
                AND h.User_2nd IS NULL 
                AND m.ProjectCode='" . $ProjectCode . "'
                ORDER BY h.QueueNumber DESC";
        $result = $this->db->conn_id->prepare($sql);
        $result->execute();
        $result = $result->fetchAll();
        return $result;
    }

    function getInformationBin($kodebin, $OperatorCode, $OperatorRole) {

        $sql = "SELECT TOP 1 h.BinCode,m.ERPCode,h.User_1st,o.Name
            FROM wms.DetailTaskRcvHistory h 
            INNER JOIN
            wms.MasterTaskRcv m
            ON m.TransactionCode=h.TransactionCode
            LEFT JOIN wms.Operator o
            ON o.OperatorCode=h.User_1st
            WHERE h.BinCode=" . $kodebin . " AND h.TransactionCode IN (select distinct m.TransactionCode
            from wms.MasterTaskRcv m,wms.DetailTaskOpr d
            where m.isFinish=0 AND m.isFinishMove=0 AND m.isCancel=0 and d.TransactionCode=m.TransactionCode and d.OperatorCode='" . $OperatorCode . "' and d.OprRole='" . $OperatorRole . "') ORDER BY h.QueueNumber DESC";
        $result = $this->db->conn_id->prepare($sql);
        $result->execute();
        $result = $result->fetchAll();
        return $result;
    }

    function getInfoSKUBin($kodebin, $SKUCode, $OperatorCode, $OperatorRole) {
        //mengambil info binberdasarkan SKU yang ada
        $sql = "SELECT TOP 1 h.BinCode,b.Keterangan,m.ERPCode,h.User_1st,o.Name,(h.Qty/dd.Ratio) as Quantity,dd.RatioName,r.Name AS DestRackSlot
            FROM wms.DetailTaskRcvHistory h 
            INNER JOIN
            wms.MasterTaskRcv m
            ON m.TransactionCode=h.TransactionCode
            LEFT JOIN wms.Operator o
            ON o.OperatorCode=h.User_1st
            LEFT JOIN wms.DetailTaskDERP dd
            ON dd.TransactionCode=h.TransactionCode AND dd.SKUCode=h.SKUCode
            LEFT JOIN wms.DetailTaskRcv d
            ON h.TransactionCode=d.TransactionCode AND h.SKUCode=d.SKUCode AND h.BinCode=d.BinCode
            LEFT JOIN wms.RackSlot r
            ON d.DestRackSlot=r.RackSlotCode
            LEFT JOIN Barang b
            ON h.SKUCode=b.Kode
            WHERE h.BinCode=" . $kodebin . " AND h.SKUCode='" . $SKUCode . "' AND h.TransactionCode IN (select distinct m.TransactionCode
            from wms.MasterTaskRcv m,wms.DetailTaskOpr d
            where m.isFinish=0 AND m.isFinishMove=0 AND m.isCancel=0 and d.TransactionCode=m.TransactionCode and d.OperatorCode='" . $OperatorCode . "' and d.OprRole='" . $OperatorRole . "') ORDER BY h.QueueNumber DESC";

        $result = $this->db->conn_id->prepare($sql);
        $result->execute();
        $result = $result->fetchAll();
        return $result;
    }

    function getSKUBarang($kodebin, $OperatorCode, $OperatorRole) {

        $sql = "SELECT h.BinCode,b.Keterangan,h.SKUCode,(h.Qty/dd.Ratio) as Qty,r.Name
                FROM wms.DetailTaskRcvHistory h 
                LEFT JOIN wms.DetailTaskDERP dd
                ON dd.TransactionCode=h.TransactionCode AND dd.SKUCode=h.SKUCode
                LEFT JOIN wms.DetailTaskRcv d
                ON h.TransactionCode=d.TransactionCode AND h.SKUCode=d.SKUCode AND h.BinCode=d.BinCode
                LEFT JOIN wms.RackSlot r
                ON d.DestRackSlot=r.RackSlotCode
                LEFT JOIN Barang b
                ON h.SKUCode=b.Kode
                WHERE h.BinCode=" . $kodebin . " AND h.User_2nd is null AND h.TransactionCode IN (select distinct m.TransactionCode
                from wms.MasterTaskRcv m,wms.DetailTaskOpr d
                where m.isFinish=0 AND m.isFinishMove=0 AND m.isCancel=0 and d.TransactionCode=m.TransactionCode and d.OperatorCode='" . $OperatorCode . "' and d.OprRole='" . $OperatorRole . "') 
                AND h.QueueNumber=
                (SELECT TOP 1 hh.QueueNumber FROM wms.DetailTaskRcvHistory hh 
                WHERE hh.TransactionCode=h.TransactionCode AND hh.BinCode=h.BinCode AND hh.SKUCode=h.SKUCode
                ORDER BY hh.QueueNumber DESC)";
        $result = $this->db->conn_id->prepare($sql);
        $result->execute();
        $result = $result->fetchAll();
        return $result;
    }

    function getCountSKUBarangInOneBin($kodebin, $TransactionCode, $NoUrut, $QueueNumber) {
        //mengecek apakah bin yg dibawa ada masih ada lebih dari 1 SKU
        /*$sql = "SELECT COUNT(h.BinCode) as Jumlah
                FROM wms.DetailTaskRcvHistory h 
                LEFT JOIN wms.DetailTaskDERP dd
                ON dd.TransactionCode=h.TransactionCode AND dd.SKUCode=h.SKUCode
                LEFT JOIN wms.DetailTaskRcv d
                ON h.TransactionCode=d.TransactionCode AND h.SKUCode=d.SKUCode AND h.BinCode=d.BinCode
                LEFT JOIN wms.RackSlot r
                ON d.DestRackSlot=r.RackSlotCode
                LEFT JOIN Barang b
                ON h.SKUCode=b.Kode
                WHERE h.BinCode=" . $kodebin . " AND h.User_2nd is null AND h.TransactionCode='" . $TransactionCode . "'
                AND h.QueueNumber='" . $QueueNumber . "' AND h.NoUrut='" . $NoUrut . "'";*/
        $sql="select count(*) as Jumlah from wms.BinSKU where BinCode='".$kodebin."' and Qty>0";
        
        $checkexist = $this->db->conn_id->prepare($sql);
        $checkexist->execute();
        $checkexist = $checkexist->fetchAll();
        $jumlah = $checkexist[0]['Jumlah'];

        if ($jumlah == 1) {
            return true;
        }
        return false;
    }

    function cekvalidasikodebin($kodebin, $OperatorCode, $OperatorRole) {
        //cek apakah bin yang di scan benar2 ada di transaksi yang sekarang operator jalankan
        $sql = "SELECT COUNT(*) as Jumlah FROM
                wms.DetailTaskRcvHistory h
                WHERE h.BinCode=" . $kodebin . " AND h.TransactionCode IN (select distinct m.TransactionCode
                from wms.MasterTaskRcv m,wms.DetailTaskOpr d
                where m.isFinish=0 AND m.isFinishMove=0 AND m.isCancel=0 and d.TransactionCode=m.TransactionCode and d.OperatorCode='" . $OperatorCode . "' and d.OprRole='" . $OperatorRole . "')";
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

    function cekvalidasirack($koderack, $kodebin) {
        $sql = "select count(*) as jumlah from wms.RackSlot where RackSlotCode='".$koderack."' and RackType in ('G','H','S')";
        //mengecek tipe rack
        $checkexist = $this->db->conn_id->prepare($sql);
        $checkexist->execute();
        $checkexist = $checkexist->fetchAll();
        if($checkexist[0]['jumlah']>0)
        {
            //cek apakah bagian dalam rak tidak kosong
            $sql = "SELECT COUNT(*) AS jumlah 
                    from wms.Bin b
                    inner join wms.BinSKU s
                    on b.BinCode=s.BinCode
                    where b.RackSlotCode=".$koderack." AND b.IsOnAisle=0 AND s.Qty>0";
            //mengecek apakah rak sekarang berada dipakai dan merupakan multiplebin
            $checkexist = $this->db->conn_id->prepare($sql);
            $checkexist->execute();
            $checkexist = $checkexist->fetchAll();
            $jumlah = $checkexist[0]['jumlah'];

            if ($jumlah > 0) {

                //apabila dipakai oleh bin apakah rak tersebut multiple bin atau tidak?
                $sql = "SELECT MultipleBin FROM wms.RackSlot WHERE RackSlotCode=" . $koderack;
                $checkexist = $this->db->conn_id->prepare($sql);
                $checkexist->execute();
                $checkexist = $checkexist->fetchAll();
                $multiplebin = $checkexist[0]['MultipleBin'];

                if ($multiplebin != 0) {
                    return true;
                } else {
                    //cek apakah bin yang menempati rak merupakan bin yang akan dimasukkan
                    $sql = "SELECT BinCode 
                    from wms.Bin
                    where RackSlotCode=" . $koderack . " AND IsOnAisle=0";
                    $result = $this->db->conn_id->prepare($sql);
                    $result->execute();
                    $result = $result->fetchAll();
                    $bin = $result[0]['BinCode'];

                    if ($bin == $kodebin) {
                        return true;
                    } else {
                        return false;
                    }
                }
            }
            return true;
        }
        return false;
    }

    function cekBinDestfull($kodebindest) {
        
        $sql = "  select count(*) as jumlah from wms.BinSKU where BinCode=" . $kodebindest." and Qty>0";
        //var_dump($sql);
        $checkexist = $this->db->conn_id->prepare($sql);
        $checkexist->execute();
        $checkexist = $checkexist->fetchAll();
        $jumlah = $checkexist[0]['jumlah'];
        if($jumlah>0)
        {
            return false;
        }
        return true;
    }

    function cekSKUBinDest($kodebindest, $SKUcode, $kodebinsrc, $TransactionCode, $NoUrut) {
        //cek apakah SKU dan ED bin tujuan sama dengan bin yang dibawa
        $sql = "SELECT COUNT(*) AS jumlah
                FROM wms.BinSKU
                WHERE 
                BinCode=" . $kodebindest . "
                AND SKUCode='" . $SKUcode . "'
                AND ExpDate=
                (SELECT ExpDate FROM wms.DetailTaskRcv WHERE TransactionCode='" . $TransactionCode . "' AND NoUrut='" . $NoUrut . "')";
        //var_dump($sql);
        $checkexist = $this->db->conn_id->prepare($sql);
        $checkexist->execute();
        $checkexist = $checkexist->fetchAll();
        $jumlah = $checkexist[0]['jumlah'];

        if ($jumlah > 0) {
            if ($this->cekWHCode_same($kodebindest, $kodebinsrc)) {//cek apakah memiliki gudang sama
                return true;
            } else {
                return false;
            }
        } else {
            //cek apakah Bin Dest benar-benar memiliki isi karena apabila tidak query atas mungkin menghasil false
            if ($this->cekBinDestfull($kodebindest)) {
                /* if ($this->cekWHCode_same($kodebindest, $kodebinsrc)) {//cek apakah memiliki gudang sama 
                  return true;
                  } else {
                  return false;
                  } */
                return true;
            }
        }
        return false;
    }

    //cek apakah WHCode sama
    function cekWHCode_same($kodebindest, $kodebinsrc) {
        $sql = "SELECT COUNT(*) AS jumlah
                FROM wms.Bin ba,wms.Bin bb
                WHERE ba.BinCode=" . $kodebindest . " 
                AND bb.BinCode=" . $kodebinsrc . " 
                AND ba.WHCode=bb.WHCode";
        $checkexist = $this->db->conn_id->prepare($sql);
        $checkexist->execute();
        $checkexist = $checkexist->fetchAll();
        $jumlah = $checkexist[0]['jumlah'];
        if ($jumlah > 0) {
            return true;
        }
        return false;
    }

    function cekvalidasirack2($koderack) {

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

    function getUser1stBinCode($kodebin, $SKUCode, $OperatorCode, $OperatorRole) {
        //mendapatkan user_1st dari detailtransaction history yang paling akhir
        $sql = "SELECT Top 1 h.User_1st AS namauser, h.Queuenumber FROM
        wms.DetailTaskRcvHistory h
        WHERE h.BinCode=" . $kodebin . " AND h.TransactionCode IN (select distinct m.TransactionCode
        from wms.MasterTaskRcv m,wms.DetailTaskOpr d
        where m.isFinish=0 AND m.isFinishMove=0 AND m.isCancel=0 and d.TransactionCode=m.TransactionCode and d.OperatorCode='" . $OperatorCode . "' and d.OprRole='" . $OperatorRole . "')  AND h.SKUCode='" . $SKUCode . "' ORDER BY h.Queuenumber DESC";
        $result = $this->db->conn_id->prepare($sql);
        $result->execute();
        $result = $result->fetchAll();
        $user = null;
        foreach ($result as $row) {
            $user = $row['namauser'];
        }
        return $user;
    }

    function getUser2ndBinCode($kodebin, $SKUCode, $OperatorCode, $OperatorRole) {
        //mendapatkan user_2nd dari detailtransaction history yang paling akhir
        $sql = "SELECT Top 1 h.User_2nd AS namauser, h.Queuenumber FROM
        wms.DetailTaskRcvHistory h
        WHERE h.BinCode=" . $kodebin . " AND h.TransactionCode IN (select distinct m.TransactionCode
        from wms.MasterTaskRcv m,wms.DetailTaskOpr d
        where m.isFinish=0 AND m.isFinishMove=0 AND m.isCancel=0 and d.TransactionCode=m.TransactionCode and d.OperatorCode='" . $OperatorCode . "' and d.OprRole='" . $OperatorRole . "') AND h.SKUCode='" . $SKUCode . "' ORDER BY h.Queuenumber DESC";
        $result = $this->db->conn_id->prepare($sql);
        $result->execute();
        $result = $result->fetchAll();
        $user = null;
        foreach ($result as $row) {
            $user = $row['namauser'];
        }
        return $user;
    }

    function setUser1stBinCode($TransactionCode, $QueueNumber, $NoUrut, $OperatorCode) {
        //melakukan update pada user_1st di detailtransactionhistory paling akhir

        $sql = "UPDATE wms.DetailTaskRcvHistory SET User_1st='" . $OperatorCode . "',Time_1st=getdate() 
                WHERE TransactionCode='" . $TransactionCode . "' AND NoUrut='" . $NoUrut . "'
                AND QueueNumber='" . $QueueNumber."'";
       
        $result = $this->db->conn_id->prepare($sql);
        $result->execute();
    }

    function setTaruhBinSKU($kodebin, $kodebindest, $koderack, $Qty, $IsOnAisle, $OperatorCode, $TransactionCode, $NoUrut, $QueueNumber) {
        //melakukan input pada detailtransactionhistory yang paling akhir

        if($kodebin==$kodebindest){
            if ($IsOnAisle == 0) {
                $sql = "UPDATE wms.DetailTaskRcv SET DestRackSlot=" . $koderack . ",DestOnAisle='".$IsOnAisle."'
                    WHERE BinCode=" . $kodebin . " AND TransactionCode='" . $TransactionCode . "'
                    AND NoUrut='" . $NoUrut . "'";
                //echo $sql;
                $result = $this->db->conn_id->prepare($sql);
                $result->execute();
            }
        }
        else{
            $sql = "UPDATE wms.DetailTaskRcv SET DestRackSlot=" . $koderack . ",DestOnAisle='".$IsOnAisle."'
                    WHERE BinCode=" . $kodebin . " AND TransactionCode='" . $TransactionCode . "'
                    AND NoUrut='" . $NoUrut . "'";
                //echo $sql;
            $result = $this->db->conn_id->prepare($sql);
            $result->execute();
        }
        $sql = "UPDATE wms.DetailTaskRcvHistory SET User_2nd='" . $OperatorCode . "',DestRackSlot=" . $koderack . ",
                        DestQty=" . $Qty . ",
                        DestOnAisle=" . $IsOnAisle . ",
                        DestBin=" . $kodebindest . ",
                        Time_2nd=getdate()
                        WHERE TransactionCode='" . $TransactionCode . "'
                        AND QueueNumber=" . $QueueNumber . " AND NoUrut='" . $NoUrut . "'";
        $result = $this->db->conn_id->prepare($sql);
        if ($result->execute()) {
            return true;
        }
        return false;
    }

    function cekQtyDest($TransactionCode, $NoUrut,$QueueNumber, $Qty) {
        //melakukan input pada detailtransactionhistory yang paling akhir
        $sql = "SELECT Qty as jumlah FROM wms.DetailTaskRcvHistory where TransactionCode='" . $TransactionCode . "' AND NoUrut='" . $NoUrut . "' AND QueueNumber='" . $QueueNumber . "'";
        
        $result = $this->db->conn_id->prepare($sql);
        $result->execute();
        $result = $result->fetchAll();
        if ($Qty > $result[0]['jumlah']) {
            return false;
        } else if ($Qty <= 0) {
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

    function getMyOutstanding($OperatorCode, $OperatorRole, $ProjectCode) {
        //mendapatkan daftar outstanding operator yang sekarang bin sedang dia bawa

        $sql = "SELECT DISTINCT h.NoUrut,h.SKUCode,m.ERPCode,h.QueueNumber,h.TransactionCode,h.BinCode,b.Keterangan,h.Qty,de.Ratio,dbo.KonversiSatuanToText(h.SKUCode,h.Qty) as Qtykonversi,de.RatioName,
                (SELECT r.Name FROM wms.RackSlot r WHERE r.RackSlotCode=d.DestRackSlot) AS DestRackSlot,
                (SELECT TOP 1 r.Name FROM wms.RackSlot r WHERE r.RackSlotCode=h.CurrRackSlot ORDER BY h.QueueNumber) AS CurrRackSlot
                FROM wms.DetailTaskRcvHistory h
                INNER JOIN 
                wms.MasterTaskRcv m
                ON h.TransactionCode=m.TransactionCode
                INNER JOIN
                wms.DetailTaskRcv d
                ON h.TransactionCode=d.TransactionCode AND h.NoUrut=d.NoUrut
                LEFT JOIN
                dbo.Barang b
                ON b.Kode=h.SKUCode
                LEFT JOIN
                wms.DetailTaskDERP de
                ON de.TransactionCode=h.TransactionCode AND de.SKUCode=h.SKUCode
                WHERE h.User_1st ='" . $OperatorCode . "'
                AND h.User_2nd IS NULL ";
        if ($ProjectCode) {
            $sql.="AND m.ProjectCode='" . $ProjectCode . "'";
        }
        $sql.=" AND h.TransactionCode IN (select distinct m.TransactionCode
                from wms.MasterTaskRcv m,wms.DetailTaskOpr d
                where m.isFinish=0 AND m.isFinishMove=0 AND m.isCancel=0 and d.TransactionCode=m.TransactionCode and d.OperatorCode='" . $OperatorCode . "' and d.OprRole='" . $OperatorRole . "')
                ORDER BY h.QueueNumber DESC";
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
    
    function getSuggestion($TransactionCode,$NoUrut,$SKUCode){
        $sql = "exec wms.spReceivingSuggestion '".$TransactionCode."','".$NoUrut."','".$SKUCode."'";

        $result = $this->db->conn_id->prepare($sql);
        $result->execute();
        $result = $result->fetchAll();
        return $result;
    }
    function cekRackSlotNull($RackSlotCode){
        $sql = "select count(*) as jumlah from wms.Bin where RackSlotCode='".$RackSlotCode."'";

        $result = $this->db->conn_id->prepare($sql);
        $result->execute();
        $result = $result->fetchAll();
        if($result[0]['jumlah']>0){
            return true;
        }
        return false;
    }

    function getQtykonversi($TransactionCode,$NoUrut,$Qty){
        $sql = "select dbo.KonversiSatuanToText(SKUCode,".$Qty.") as Qtykonversi from wms.DetailTaskRcv where TransactionCode='".$TransactionCode."' and NoUrut='".$NoUrut."'";

        $result = $this->db->conn_id->prepare($sql);
        $result->execute();
        $result = $result->fetchAll();
        return $result[0]['Qtykonversi'];
        
    }
}

?>
