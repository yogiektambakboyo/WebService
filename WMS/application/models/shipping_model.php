<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of shipping_model
 *
 * @author USER
 */
class shipping_model extends CI_Model {

    //put your code here
    function getShipping($OperatorCode) {
        $sql = "SELECT mt.TransactionCode as TransactionCode,mt.ERPCode as ERPCode,mt.TransactionDate as TransactionDate ,(case when o.Assigned is null then 2 else cast(o.Assigned as int) end) as Assigned
                FROM wms.MasterTaskPck mt
                left join wms.DetailTaskOpr o
                on mt.TransactionCode=o.TransactionCode and o.OprRole='10/WHR/004' and o.OperatorCode='".$OperatorCode."'
                WHERE
                mt.isFinish=0 AND mt.isCancel=0 AND (mt.ProjectCode='PCK' or mt.ProjectCode='PTS')
                AND (select count(*) from wms.DetailTaskPckS ds where ds.TransactionCode=mt.TransactionCode)>0
                --AND (select count(*) from wms.DetailTaskOpr op where op.TransactionCode=mt.TransactionCode and OprRole='10/WHR/004')=0
                ORDER BY mt.TransactionDate DESC";
        $result = $this->db->conn_id->prepare($sql);
        $result->execute();
        $result = $result->fetchAll();
        return $result;
    }

    function cekDetailTransactionOpr($transactioncode, $OperatorCode, $OprRole) {
        //waktu memilih pick list retur dari Master transaction maka operator tersebut akan dicatatkan sekarang sedang mengambil project apa saja dan hanya tidak boleh 1 master 2/lebih opr
        //cek apakah ada opr lain yang mengambilmaster ini dengan role yg sama
        $sql2 = "SELECT COUNT(*) as Jumlah FROM wms.DetailTaskOpr WHERE TransactionCode='" . $transactioncode . "' AND OperatorCode<>'" . $OperatorCode . "' AND OprRole='" . $OprRole . "'";

        $checkexist2 = $this->db->conn_id->prepare($sql2);
        $checkexist2->execute();
        $checkexist2 = $checkexist2->fetchAll();
        $jumlah2 = 0;
        foreach ($checkexist2 as $rowjum2) {
            $jumlah2 = $rowjum2['Jumlah'];
        }
        if ($jumlah2 > 0) {
            return FALSE;
        }
        return TRUE;
    }

    function getTransactionERPCode($OperatorCode) {
        $sql = "select distinct ds.TransactionCode,ms.ERPCode
                from wms.DetailTaskPckS ds
                inner join wms.MasterTaskPck ms
                on ds.TransactionCode=ms.TransactionCode
                where DestBin is null and User_1st='" . $OperatorCode . "'";
        $result = $this->db->conn_id->prepare($sql);
        $result->execute();
        $result = $result->fetchAll();
        return $result;
    }

    function setDetailTransactionOpr($transactioncode, $OperatorCode, $OprRole) {

        $sql = "SELECT COUNT(*) as Jumlah FROM wms.DetailTaskOpr WHERE TransactionCode='" . $transactioncode . "' AND OperatorCode='" . $OperatorCode . "' AND OprROle='" . $OprRole . "'";
        $checkexist = $this->db->conn_id->prepare($sql);
        $checkexist->execute();
        $checkexist = $checkexist->fetchAll();
        $jumlah = 0;
        foreach ($checkexist as $rowjum) {
            $jumlah = $rowjum['Jumlah'];
        }
        if ($jumlah == 0) {
            $sql = "INSERT INTO wms.DetailTaskOpr VALUES('" . $transactioncode . "','" . $OperatorCode . "','" . $OprRole . "','1')";
            $input = $this->db->conn_id->prepare($sql);
            $input->execute();
            /* $sql = "UPDATE wms.DetailTaskPckS Set User_1st='" . $OperatorCode . "' WHERE TransactionCode='" . $transactioncode . "'";
              $input = $this->db->conn_id->prepare($sql);
              $input->execute(); */
        }else{
            $sql = "Update wms.DetailTaskOpr set Assigned='1' where TransactionCode='" . $transactioncode . "' and OperatorCode='" . $OperatorCode . "' and OprRole='" . $OprRole . "'";
            $input = $this->db->conn_id->prepare($sql);
            $input->execute();
        }
    }

    function setQtySKU($transactioncode, $NoUrut, $BinDest, $Qty, $OperatorCode) {
        $sql = "UPDATE wms.DetailTaskPckS Set Qty=" . $Qty . ",DestBin=" . $BinDest . ",Time_1st=getdate(),User_1st='" . $OperatorCode . "'
                WHERE TransactionCode='" . $transactioncode . "' 
                AND NoUrut=" . $NoUrut . " AND DestBin is null";

        $input = $this->db->conn_id->prepare($sql);
        if ($input->execute())
            return true;
        return false;
    }

    function getListSKUBin($BinCode, $TransactionCode) {//daftar barang yg akan masuk van
        $sql = "select ds.TransactionCode,ds.BinCode,ds.SKUCode,dbo.KonversiSatuanToText(ds.SKUCode,sum(ds.QtyNeedNow)) as QtyNeedNow,b.Keterangan
                from wms.DetailTaskPckS ds
                inner join barang b
                on ds.SKUCode=b.Kode
                where BinCode=" . $BinCode . " and DestBin is null and TransactionCode='" . $TransactionCode . "' group by ds.SKUCode,ds.TransactionCode,ds.BinCode,b.Keterangan";

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

    function getListBinBelumShipping($TransactionCode) {//bin yg belum masuk van
        $sql = "select distinct ds.BinCode
                from wms.DetailTaskPckS ds
                where DestBin is null and TransactionCode='" . $TransactionCode . "'";
        $result = $this->db->conn_id->prepare($sql);
        $result->execute();
        $result = $result->fetchAll();
        return $result;
    }

    function cekBinDestNull($TransactionCode) {//apakah binshipping / van belum ditetapkan di master
        $sql = "select BinShipping 
                from wms.MasterTaskPck
                where TransactionCode='" . $TransactionCode . "'";
        $checkexist = $this->db->conn_id->prepare($sql);
        $checkexist->execute();
        $checkexist = $checkexist->fetchAll();
        $BinShipping = NULL;
        foreach ($checkexist as $row) {
            $BinShipping = $row['BinShipping'];
        }
        if ($BinShipping == NULL) {
            return TRUE;
        }
        return FALSE;
    }

    function cekBinOutstanding($BinCode, $TransactionCode) {//cek apakah bin tersebut masih outstanding
        $sql2 = "select count(*) as Jumlah
                from wms.DetailTaskPckS
                where BinCode=" . $BinCode . " and DestBin is null and TransactionCode='" . $TransactionCode . "'";

        $checkexist2 = $this->db->conn_id->prepare($sql2);
        $checkexist2->execute();
        $checkexist2 = $checkexist2->fetchAll();
        $jumlah2 = 0;
        foreach ($checkexist2 as $rowjum2) {
            $jumlah2 = $rowjum2['Jumlah'];
        }
        if ($jumlah2 > 0) {
            return TRUE;
        }
        return FALSE;
    }

    function cekBinSrctoBinDest($BinCode, $BinDest, $TransactionCode) { //cek apakah bindest/van benar menurut transactioncode
        if ($this->cekBinDestNull($TransactionCode)) {
            $sql = "update wms.MasterTaskPck set BinShipping=" . $BinDest . " where TransactionCode='" . $TransactionCode . "'";

            $result = $this->db->conn_id->prepare($sql);
            $result->execute();
            return TRUE;
        } else {
            $sql = "select BinShipping from wms.MasterTaskPck where TransactionCode='" . $TransactionCode . "'";

            $result = $this->db->conn_id->prepare($sql);
            $result->execute();
            $result = $result->fetchAll();
            $BinShipping = null;
            foreach ($result as $row) {
                $BinShipping = $row['BinShipping'];
            }
            if ($BinDest == $BinShipping) {
                return TRUE;
            }
            return FALSE;
        }
    }

    function getListDetailSKUPicking($TransactionCode, $BinCode, $SKUCode) {
        $sql = "select * from wms.DetailTaskPcks where TransactionCode='" . $TransactionCode . "' 
                and BinCode='" . $BinCode . "' and SKUCode='" . $SKUCode . "' and DestBin is null";
        $result = $this->db->conn_id->prepare($sql);
        $result->execute();
        $result = $result->fetchAll();
        return $result;
    }

    function getListDetailSKUPicking2($TransactionCode) {
        //jumlah berdasarkan apa yg picker bawa lihat triger update detailtaskpck
        $sql = "select ds.TransactionCode,ds.BinCode,ds.SKUCode,sum(ds.QtyNeedNow) as QtyNeedNow,b.Keterangan
                from wms.DetailTaskPckS ds
                inner join barang b
                on ds.SKUCode=b.Kode
                where DestBin is null and TransactionCode='" . $TransactionCode . "' 
                group by ds.SKUCode,ds.TransactionCode,ds.BinCode,b.Keterangan";
        $result = $this->db->conn_id->prepare($sql);
        $result->execute();
        $result = $result->fetchAll();
        return $result;
    }

    function getListDetailSKUPicking3($TransactionCode) {
        $sql = "select m.WHCode,ds.*,b.Keterangan
                from wms.DetailTaskPckS ds
                inner join barang b
                on ds.SKUCode=b.Kode
                inner join wms.MasterTaskPck m
                on m.TransactionCode=ds.TransactionCode
                where ds.DestBin is null and ds.TransactionCode='" . $TransactionCode . "'";


        $result = $this->db->conn_id->prepare($sql);
        $result->execute();
        $result = $result->fetchAll();
        return $result;
    }

    function setShippingBermasalah($TransactionCode, $SKUCode, $BinCode, $OperatorCode,$ShippingProblem) {
        $sql = "insert into wms.DetailTaskPck(TransactionCode,NoUrut,SKUCode,QtyNeedStart,QtyNeedNow,Addtask)
                select a.TransactionCode,(select max(NoUrut)+1 from wms.DetailTaskPck where TransactionCode='" . $TransactionCode . "') as Urutan,
                a.SKUCode,sum(a.QtyNeedNow),sum(a.QtyNeedNow),1 from wms.DetailTaskPcks a where a.TransactionCode='" . $TransactionCode . "' 
                and a.SKUCode='" . $SKUCode . "' and a.BinCode='" . $BinCode . "' and DestBin is NULL group by a.TransactionCode,a.SKUCode";

        $result = $this->db->conn_id->prepare($sql);
        if ($result->execute()) {
            $sql = "select NoUrut from wms.DetailTaskPcks where TransactionCode='" . $TransactionCode . "' and SKUCode='" . $SKUCode . "' and BinCode='" . $BinCode . "' and DestBin is NULL";
            $result = $this->db->conn_id->prepare($sql);
            $result->execute();
            $result = $result->fetchAll();

            for ($i = 0; $i < count($result); $i++) {
                $sql = "update wms.DetailTaskPcks set Qty=QtyNeedNow,DestBin='" . $ShippingProblem . "',User_1st='" . $OperatorCode . "', Time_1st=getdate(),Status2='P' 
                    where TransactionCode='" . $TransactionCode . "' and NoUrut='" . $result[$i]['NoUrut'] . "'";
                $result2 = $this->db->conn_id->prepare($sql);
                if (!$result2->execute()) {
                    return false;
                }
            }
            return true;
        }
        return false;
    }

    function setShippingKembali($TransactionCode, $NoUrut, $DestBin, $Qty, $OperatorCode) {
        $sql = "select count(*) as jumlah from wms.DetailTaskPckS
                where TransactionCode='" . $TransactionCode . "' and NoUrut='" . $NoUrut . "' and Status2='E'";
        $result = $this->db->conn_id->prepare($sql);
        $result->execute();
        $result = $result->fetchAll();
        if ($result[0]['jumlah'] > 0) {
            $sql = "update wms.DetailTaskPcks set DestBin=BinCode, Qty=" . $Qty . ",User_1st='" . $OperatorCode . "',Time_1st=getdate(),Note='#".$DestBin."'
                where TransactionCode='" . $TransactionCode . "' and NoUrut='" . $NoUrut . "'";
        } else {
            $sql = "update wms.DetailTaskPcks set DestBin='" . $DestBin . "', Qty=" . $Qty . ",User_1st='" . $OperatorCode . "',Time_1st=getdate(),Status2='R'
                where TransactionCode='" . $TransactionCode . "' and NoUrut='" . $NoUrut . "'";
        }
        $result = $this->db->conn_id->prepare($sql);
            if ($result->execute()) {
                return TRUE;
            }
            return FALSE;
    }

    function getSuggestionKembali($TransactionCode, $SKUCode, $BinCode, $ExpDate) {
        $sql = "select distinct d.SrcBin,b.Keterangan,(select Name from wms.RackSlot where RackSlotCode=d.SrcRackSlot) as SrcRackSlot from
                wms.DetailTaskPck d
                inner join Barang b
                on b.Kode=d.SKUCode
                where d.TransactionCode='" . $TransactionCode . "' and d.SKUCode='" . $SKUCode . "' and d.Destbin='" . $BinCode . "' and ExpDate='" . $ExpDate . "'";

        $result = $this->db->conn_id->prepare($sql);
        $result->execute();
        $result = $result->fetchAll();
        return $result;
    }

    function cekBinvalidation($BinCode) {
        $sql = "select count(*) as jumlah from wms.Bin where BinCode='" . $BinCode . "'";

        $result = $this->db->conn_id->prepare($sql);
        $result->execute();
        $result = $result->fetchAll();
        if ($result[0]['jumlah'] > 0) {
            return TRUE;
        }
        return FALSE;
    }

    function cekRackvalidation($RackSlotCode) {
        $sql = "select count(*) as jumlah from wms.RackSlot where RackSlotCode='" . $RackSlotCode . "'";

        $result = $this->db->conn_id->prepare($sql);
        $result->execute();
        $result = $result->fetchAll();
        if ($result[0]['jumlah'] > 0) {
            return TRUE;
        }
        return FALSE;
    }

    function cekRackSlotNull($RackSlotCode) {
        $sql = "select count(*) as jumlah from wms.Bin where RackSlotCode='" . $RackSlotCode . "'";
        // var_dump($sql);
        $result = $this->db->conn_id->prepare($sql);
        $result->execute();
        $result = $result->fetchAll();
        if ($result[0]['jumlah'] == 0) {
            return TRUE;
        }
        return FALSE;
    }

    function cekWHCodeRack($RackSlotCode, $WHCode) {
        $sql = "select count(*) as jumlah from wms.Bin b where b.RackSlotCode='" . $RackSlotCode . "' and b.WHCode='" . $WHCode . "'";

        $result = $this->db->conn_id->prepare($sql);
        $result->execute();
        $result = $result->fetchAll();
        if ($result[0]['jumlah'] > 0) {
            return TRUE;
        }
        return FALSE;
    }

    function cekSKUCode($RackSlotCode, $SKUCode, $ExpDate, $BinCode) {
        $sql = "select count(*) as jumlah from wms.Bin b
                inner join wms.BinSKU s
                on b.BinCode=s.BinCode
                where b.RackSlotCode='" . $RackSlotCode . "' and b.BinCode='" . $BinCode . "' and s.SKUCode='" . $SKUCode . "' and s.ExpDate='" . $ExpDate . "'";

        $result = $this->db->conn_id->prepare($sql);
        $result->execute();
        $result = $result->fetchAll();
        if ($result[0]['jumlah'] > 0) {
            return TRUE;
        }
        return FALSE;
    }

    function cekMultipleBin($RackSlotCode) {
        $sql = "select MultipleBin from wms.RackSlot b where b.RackSlotCode='" . $RackSlotCode . "'";

        $result = $this->db->conn_id->prepare($sql);
        $result->execute();
        $result = $result->fetchAll();
        if ($result[0]['MultipleBin'] == 0) {
            return FALSE;
        }
        return TRUE;
    }

    function cekBinRackSlot($BinCode, $RackSlotCode, $SKUCode, $WHCode, $ExpDate) {
        $sql = "select RackSlotCode from wms.Bin where BinCode='" . $BinCode . "'";

        $result = $this->db->conn_id->prepare($sql);
        $result->execute();
        $result = $result->fetchAll();
        if ($result[0]['RackSlotCode'] == NULL) {
            if ($this->cekRackSlotNull($RackSlotCode)) {//bila rackslot tujuan kosong
                return array('status' => TRUE, 'msg' => '');
            } else {
                //bila rackslot tujuan tidak null
                if ($this->cekWHCodeRack($RackSlotCode, $WHCode)) {
                    if ($this->cekMultipleBin($RackSlotCode)) {
                        return array('status' => TRUE, 'msg' => '');
                    }
                    return array('status' => FALSE, 'msg' => 'Rack Tidak Multiple');
                }
                return array('status' => FALSE, 'msg' => 'Gudang Salah');
            }
        } else {
            if ($this->cekWHCodeRack($RackSlotCode, $WHCode)) {
                if ($this->cekSKUCode($RackSlotCode, $SKUCode, $ExpDate, $BinCode)) {//cek apakah rak, bin,sku, dan ed sama
                    return array('status' => TRUE, 'msg' => '');
                }
                return array('status' => FALSE, 'msg' => 'SKU atau ED Tidak Sama');
            }
            return array('status' => FALSE, 'msg' => 'Gudang Salah');
        }
    }

    function cari_barang($TransactionCode, $cari) {
        $sql = "select b.Kode,b.Keterangan,convert(varchar(10),d.ExpDate ,111) ExpDate
                from wms.DetailTaskPckS d 
                inner join Barang b on d.SKUCode=b.Kode
                where d.TransactionCode='" . $TransactionCode . "' and d.BinCode='" . $cari . "'";
        $result = $this->db->conn_id->prepare($sql);
        $result->execute();
        $result = $result->fetchAll();
        if (count($result) == 0) {
            $sql = "select b.Kode,b.Keterangan,convert(varchar(10),getdate(),111) ExpDate 
                    from barang b
                    where 
                        (b.ItemBarcode='" . $cari . "' 
                            or b.ShipperBarcode='" . $cari . "' 
                                or b.BundleBarcode='" . $cari . "'
                                    or b.keterangan like '%" . $cari . "%')";
            $result = $this->db->conn_id->prepare($sql);
            $result->execute();
            $result = $result->fetchAll();
        }
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

    function setTambahBarang($TransactionCode, $BinCode, $SKUCode, $ExpDate, $jumlah) {
        $sql = "select max(NoUrut) as jumlah
                from wms.DetailTaskPckS
                where TransactionCode='" . $TransactionCode . "'";
        $result = $this->db->conn_id->prepare($sql);
        $result->execute();
        $result = $result->fetchAll();
        $sql = "insert into wms.DetailTaskPckS(TransactionCode,NoUrut,SKUCode,BinCode,QtyNeedStart,QtyNeedNow,ExpDate,CreateTime,Status2) 
                values('" . $TransactionCode . "','" . ($result[0]['jumlah']+1) . "','" . $SKUCode . "','" . $BinCode . "','" . $jumlah . "','" . $jumlah . "','" . $ExpDate . "',getdate(),'E')";
        $result = $this->db->conn_id->prepare($sql);
        if($result->execute())
        {
            return TRUE;
        }
        return FALSE;
    }
    function konversiQty($SKUCode, $Total){
        $sql = "select dbo.KonversiSatuanToText('".$SKUCode."',".$Total.") as Qtykonversi";
        $result = $this->db->conn_id->prepare($sql);
        $result->execute();
        $result = $result->fetchAll();
        return $result[0]['Qtykonversi'];
        
    }
    function cari_barang_ajax($cari) {
        $sql = "select b.Kode,b.Keterangan 
                from barang b
                where (b.ItemBarcode='".$cari."' 
                        or b.ShipperBarcode='".$cari."' 
                            or b.BundleBarcode='".$cari."')";
        $result = $this->db->conn_id->prepare($sql);
        $result->execute();
        $result = $result->fetchAll();
        return $result;
    }

}

?>
