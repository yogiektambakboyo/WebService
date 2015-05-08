<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of admin_replenish_model
 *
 * @author USER
 */
class admin_replenish_model extends CI_Model {

    //put your code here

    function getoutstandingbatas() {

        $batasmin = 15;
        $sql = "select RackType,Name,case when RackType='G' then 3
                when RackType='H' then 2
                when RackType='S' then 1 else 4 end as Lvl,
                a.*,brg.Keterangan,(brg.caseperpallet*s.rasio) as caseperpallet,cast(a.jml as numeric(18,2))/(brg.caseperpallet*s.rasio)*100 as persen
                from
                (select b.RackSlotCode,s.SKUCode,sum(Qty) Jml
                from wms.Bin b,wms.BinSKU s
                where s.BinCode=b.BinCode and s.Qty<>0
                group by b.RackSlotCode,s.SKUCode) a
                join (select b.RackSlotCode
                from wms.Bin b,wms.BinSKU s
                where s.BinCode=b.BinCode and s.Qty<>0
                group by b.RackSlotCode
                having count(distinct s.SKUCode)=1) b on a.RackSlotCode=b.RackSlotCode
                join barang brg on a.SKUCode=brg.kode
                join (select brg,max(rasio) as rasio from satuan where satuanaktif=1 group by brg) s on s.brg=a.SKUCode
                join wms.RackSlot r on r.RackSlotCode=a.RackSlotCode
                where cast(a.jml as numeric(18,2))/(brg.caseperpallet*s.rasio)*100<" . $batasmin . "
                and r.RackType in ('G','H','S') and r.RackLevel=1
                and not exists (select * from wms.DetailTaskRpl where Status2=0 and DestRackSlot=a.RackSlotCode)
                order by lvl";
        $result = $this->db->conn_id->prepare($sql);
        $result->execute();
        $result = $result->fetchAll();
        return $result;
    }

    function getSrcRack($RackType, $RackSlotCode, $SKUCode) {
        $sql = "select b.BinCode,r.RackSlotCode,b.WHCode,bsku.ExpDate
                from wms.Bin b
                inner join wms.BinSKU bsku
                on bsku.BinCode=b.BinCode
                inner join wms.RackSlot r
                on r.RackSlotCode=b.RackSlotCode
                where r.RackSlotCode='" . $RackSlotCode . "' and bsku.SKUCode='" . $SKUCode . "' and bsku.Qty>0";
        $result = $this->db->conn_id->prepare($sql);
        $result->execute();
        $result = $result->fetchAll();
        $BinCode = $result[0]['BinCode'];
        $WHCode = $result[0]['WHCode'];
        $ExpDate = $result[0]['ExpDate'];
        $strtyperack = "'G'";
        if ($RackType == 'S') {
            $strtyperack = "'G,H'";
        }
        if ($RackType == 'H') {
            //Exp Date harus sama klo half pallet
            $sql = "select r.RackSlotCode,b.BinCode,bsku.SKUCode,bsku.ExpDate,r.RackType,bsku.Qty,b.WHCode,r.Name,brg.Keterangan
                    from wms.Bin b
                    inner join wms.BinSKU bsku
                    on bsku.BinCode=b.BinCode
                    inner join wms.RackSlot r
                    on r.RackSlotCode=b.RackSlotCode
                    inner join barang brg
                    on brg.Kode=bsku.SKUCode 
                    where r.RackSlotCode in (select b.RackSlotCode
                    from wms.Bin b,wms.BinSKU s
                    where s.BinCode=b.BinCode and s.Qty<>0
                    group by b.RackSlotCode
                    having count(distinct s.SKUCode)=1) 
                    and b.isOnAisle=0 
                    and bsku.SKUCode='" . $SKUCode . "' 
                    and b.WHCode='" . $WHCode . "'
                    and b.RackSlotCode<>'" . $RackSlotCode . "'
                    and r.RackType in (" . $strtyperack . ")
                    and bsku.SKUCode='" . $ExpDate . "'";
        } else {
            $sql = "select r.RackSlotCode,b.BinCode,bsku.SKUCode,bsku.ExpDate,r.RackType,bsku.Qty,b.WHCode,r.Name,brg.Keterangan
                    from wms.Bin b
                    inner join wms.BinSKU bsku
                    on bsku.BinCode=b.BinCode
                    inner join wms.RackSlot r
                    on r.RackSlotCode=b.RackSlotCode
                    inner join barang brg
                    on brg.Kode=bsku.SKUCode 
                    where r.RackSlotCode in (select b.RackSlotCode
                    from wms.Bin b,wms.BinSKU s
                    where s.BinCode=b.BinCode and s.Qty<>0
                    group by b.RackSlotCode
                    having count(distinct s.SKUCode)=1) 
                    and b.isOnAisle=0 
                    and bsku.SKUCode='" . $SKUCode . "' 
                    and b.WHCode='" . $WHCode . "'
                    and b.RackSlotCode<>'" . $RackSlotCode . "'
                    and r.RackType in (" . $strtyperack . ")";
        }

        $result = $this->db->conn_id->prepare($sql);
        $result->execute();
        $result = $result->fetchAll();
        return $result;
    }

    function cekJumlahBarang($BinCode, $SKUCode, $ExpDate, $jumlah) {
        $sql = "select Qty from wms.BinSKU where BinCode='" . $BinCode . "' and SKUCode='" . $SKUCode . "' and ExpDate='" . $ExpDate . "'";
        $result = $this->db->conn_id->prepare($sql);
        $result->execute();
        $result = $result->fetchAll();

        if ($result[0]['Qty'] < $jumlah) {
            return false;
        }
        return true;
    }

    function setDetailTaskRpl($BinCode, $SKUCode, $ExpDate, $CurrRackSlot, $DestRackSlot, $jumlah) {
        $sql = "select count(*) as jumlah from wms.DetailTaskRpl";
        $result = $this->db->conn_id->prepare($sql);
        $result->execute();
        $result = $result->fetchAll();
        $jumnol = 6 - strlen(strval($result[0]['jumlah']));
        $str = '10/' . date('y') . '/L/';
        //$str = "0/13/R/";
        for ($i = 0; $i < $jumnol; $i++) {
            $str.="0";
        }
        $str.=strval(($result[0]['jumlah'] + 1));
        $sql = "insert into wms.DetailTaskRpl(TransactionCode,NoUrut,BinCode,SKUCode,Qty,ExpDate,SrcRackSlot,DestRackSlot)
                values('" . $str . "',1,'" . $BinCode . "','" . $SKUCode . "','" . $jumlah . "','" . $ExpDate . "','" . $CurrRackSlot . "','" . $DestRackSlot . "')";
        $result = $this->db->conn_id->prepare($sql);

        if ($result->execute()) {
            return true;
        }
        return false;
    }

    function getRackSlot() {
        $sql = "select Name from wms.RackSlot";
        $result = $this->db->conn_id->prepare($sql);
        $result->execute();
        $result = $result->fetchAll();
        return $result;
    }

    function cekRackSlotName($name) {
        $sql = "select RackSlotCode from wms.RackSlot where Name='" . $name . "'";
        $result = $this->db->conn_id->prepare($sql);
        $result->execute();
        $result = $result->fetchAll();

        if (count($result) > 0) {
            return array('status' => true, 'RackSlotCode' => $result[0]['RackSlotCode']);
        }
        return array('status' => false, 'RackSlotCode' => '');
    }

    function getBinRackSlot($RackSlotCode) {
        $sql = "select BinCode from wms.Bin where RackSlotCode='" . $RackSlotCode . "'";
        $result = $this->db->conn_id->prepare($sql);
        $result->execute();
        $result = $result->fetchAll();
        return $result;
    }

    function getBinSKU($BinCode) {
        $sql = "select b.Kode,b.Keterangan,dbo.KonversiSatuanToText(b.Kode,bs.Qty) as Qtykonversi,bs.Qty,bi.WHCode 
            from wms.Bin bi
            inner join wms.BinSKU bs on bi.BinCode=bs.BinCode
            inner join Barang b on bs.SKUCode=b.Kode
            where bi.BinCode='" . $BinCode . "'";
        $result = $this->db->conn_id->prepare($sql);
        $result->execute();
        $result = $result->fetchAll();
        return $result[0];
    }

    function getsatuan($SKUCode) {
        $sql = "SELECT Rasio,Satuan FROM satuan where Brg='" . $SKUCode . "' AND SatuanAktif=1";

        $result = $this->db->conn_id->prepare($sql);
        $result->execute();
        $result = $result->fetchAll();
        return $result;
    }

    function getBarang() {
        $sql = "select b.Kode,b.Keterangan,dbo.KonversiSatuanToText(b.Kode,bs.Qty) as Qtykonversi,bs.Qty,bi.WHCode,g.Keterangan as WHName,r.Name as RackName,bs.ExpDate,bi.RackSlotCode,bi.BinCode 
            from wms.Bin bi
            inner join wms.BinSKU bs on bi.BinCode=bs.BinCode
            inner join Barang b on bs.SKUCode=b.Kode
            left join wms.RackSlot r on bi.RackSlotCode=r.RackSlotCode
            left join Gudang g on bi.WHCode=g.Kode";

        $result = $this->db->conn_id->prepare($sql);
        $result->execute();
        $result = $result->fetchAll();
        return $result;
    }

    function getQtykonversi($SKUCode, $Qty) {
        $sql = "select dbo.KonversiSatuanToText('" . $SKUCode . "'," . $Qty . ") as Qtykonversi";

        $result = $this->db->conn_id->prepare($sql);
        $result->execute();
        $result = $result->fetchAll();
        return $result[0]['Qtykonversi'];
    }

    /* function cekWHRack($SrcRack, $DestRack) {

      $sql = "select count(*) as jumlah from wms.Bin where WHCode in(select WHCode from wms.bin where RackSlotCode='" . $DestRack . "') and RackSlotCode='" . $SrcRack . "'";

      $result = $this->db->conn_id->prepare($sql);
      $result->execute();
      $result = $result->fetchAll();
      if ($result[0]['jumlah'] > 0) {
      return true;
      }
      return false;
      } */

    function getWH($Rack) {
        $sql = "select count(WHCode) as jumlah from wms.Bin where RackSlotCode='" . $Rack . "'";

        $result = $this->db->conn_id->prepare($sql);
        $result->execute();
        $result = $result->fetchAll();
        if($result[0]['jumlah']>0){
            $sql = "select WHCode from wms.Bin where RackSlotCode='" . $Rack . "'";

            $result = $this->db->conn_id->prepare($sql);
            $result->execute();
            $result = $result->fetchAll();
            return $result[0]['WHCode'];
        }
        return 'kosong';
    }

    function cekWHSKURack($SrcRack, $DestRack, $BinCode, $SKUCode) {
        $sql = "select count(*) as jumlah from wms.Bin bi inner join wms.BinSKU bs on bi.BinCode=bs.BinCode where bi.RackSlotCode='" . $DestRack . "' and bs.Qty>0";

        $result = $this->db->conn_id->prepare($sql);
        $result->execute();
        $result = $result->fetchAll();
        if ($result[0]['jumlah'] > 0) {//destination Rack kosong
            $sql = "select count(*) as jumlah from wms.RackSlot where RackSlotCode='" . $DestRack . "' and multiplebin=1";

            $result = $this->db->conn_id->prepare($sql);
            $result->execute();
            $result = $result->fetchAll();
            if ($result[0]['jumlah'] > 0) {//cek multiplebin
                //if(cekWHRack($SrcRack, $DestRack))//cek gudang
                //{
                return array('status' => true);
                //}
                //return array('status'=>false,'msg'=>'Gudang Beda');
            } else {
                //if(cekWHRack($SrcRack, $DestRack))//cek gudang
                //{
                $sql = "select count(*) as jumlah from wms.Bin bi
                            inner join wms.BinSKU bs
                            on bi.BinCode=bs.BInCode
                            where bs.SKUCode='" . $SKUCode . "' and 
                            bs.BinCode<>'" . $BinCode . "'
                            and bs.ExpDate=(select ExpDate from wms.BinSKU where BinCode='" . $BinCode . "' and SKUCode='" . $SKUCode . "')";

                $result = $this->db->conn_id->prepare($sql);
                $result->execute();
                $result = $result->fetchAll();
                if ($result[0]['jumlah'] > 0) {//cek SKU dan ED
                    return array('status' => true);
                }
                return array('status' => false, 'msg' => 'SKU atau ED beda');

                //}
                //return array('status'=>false,'msg'=>'Gudang Beda');
            }
        }
        return array('status' => true);
    }

    function getTransactionCode() {
        $sql = "select count(*) as jumlah from wms.MasterTaskRpl";

        $result = $this->db->conn_id->prepare($sql);
        $result->execute();
        $result = $result->fetchAll();
        $kode = '10/' . date('y') . '/L/';
        for ($i = 0; $i < (6 - strlen(((String) $result[0]['jumlah']+1))); $i++) {
            $kode.='0';
        }
        $kode.=(String) ($result[0]['jumlah']+1);
        return $kode;
    }

    function setMasterTaskRpl($TransactionCode,$WHSrc,$WHDest, $OperatorCode) {

        $sql = "insert into wms.MasterTaskRpl(TransactionCode,ProjectCode,WHCodeSrc,WHCodeDest,TransactionDate,CreateUserId,CreateTime)
                values('" . $TransactionCode . "','RPL','".$WHSrc."','".$WHDest."',getdate(),'" . $OperatorCode . "',getdate())";
        
        
        $result = $this->db->conn_id->prepare($sql);
        if ($result->execute()) {
            return true;
        }
        return false;
    }

    function setDetailTaskRplmanual($TransactionCode, $NoUrut, $BinCode, $SKUCode, $QtyNeed, $SrcRackSlot, $DestRackSlot) {
        
        $sql = "select ExpDate from wms.BinSKU where SKUCode='" . $SKUCode . "' and BinCode='" . $BinCode . "'";
        $result = $this->db->conn_id->prepare($sql);
        $result->execute();
        $result = $result->fetchAll();
        
        $sql = "insert into wms.DetailTaskRpl(TransactionCode,NoUrut,BinCode,SKUCode,ExpDate,QtyNeedStart,QtyNeedNow,SrcRackSlot,DestRackSlot,CreateTime)
                values('" . $TransactionCode . "','" . $NoUrut . "','" . $BinCode . "','" . $SKUCode . "','".$result[0]['ExpDate']."','" . $QtyNeed . "','" . $QtyNeed . "','" . $SrcRackSlot . "','" . $DestRackSlot . "',getdate())";
        
        $result = $this->db->conn_id->prepare($sql);
        if ($result->execute()) {
            return true;
        }
        return false;
    }
    function setDetailTaskDERP($TransactionCode,$SKUCode,$Qty)
    {
        $sql = "select Satuan from Satuan where Rasio=1 and SatuanAktif=1 and Brg='" . $SKUCode . "'";
        $result = $this->db->conn_id->prepare($sql);
        $result->execute();
        $result = $result->fetchAll();
        
        $sql = "insert into wms.DetailTaskDERP(TransactionCode,SKUCode,Qty,Ratio,RatioName)
                values('" . $TransactionCode . "','" . $SKUCode . "','".$Qty."','1','".$result[0]['Satuan']."')";
        
        $result = $this->db->conn_id->prepare($sql);
        if ($result->execute()) {
            return true;
        }
        return false;
    }
    function getTransaksiRPLList() {
        $sql = "select t.TransactionDate,t.isFinishMove,t.TransactionCode,t.Note,t.isFinish,t.isCancel,t.ERPCode,(select Keterangan from Gudang where Kode=t.WHCodeSrc) as GudangSrc,(select Keterangan from Gudang where Kode=t.WHCodeDest) as GudangDest
                from wms.MasterTaskRpl t
                where t.isFinish=0 and t.isCancel=0 and t.ProjectCode='RPL'
                order by TransactionDate desc";
        $result = $this->db->conn_id->prepare($sql);
        $result->execute();
        $result = $result->fetchAll();
        return $result;
    }

    function getDetailTransaction($TransactionCode) { //keterangan transaksi
        $sql = "select t.transactionCode,t.projectCode,t.note,t.isFinish,t.isCancel,t.ERPCode
                from wms.MasterTaskRpl t
                where t.TransactionCode = '" . $TransactionCode . "'";
        $result = $this->db->conn_id->prepare($sql);
        $result->execute();
        $result = $result->fetchAll();
        return $result[0];
    }

    function getDetailTransaction2($TransactionCode) { //detail transaksi
        $sql = "select (select Name from wms.RackSlot where RackSlotCode=dt.SrcRackSlot) as RackSlotAsal,(select Name from wms.RackSlot where RackSlotCode=dt.DestRackSlot) as RackSlotTujuan,dt.*,dbo.KonversiSatuanToText(dt.SKUCode,dt.QtyNeedNow) as Qtykonversi,convert(varchar(10),dt.ExpDate,121) tanggalExp,b.keterangan
                from wms.DetailTaskRpl dt
                join barang b 
                on dt.SKUCode = b.Kode
                where dt.TransactionCode =  '" . $TransactionCode . "' and dt.BinCode<>'".$this->session->userdata('ReplenishProblem')."'";
        $result = $this->db->conn_id->prepare($sql);
        $result->execute();
        $result = $result->fetchAll();
        return $result;
    }

    function getDetailTransaction4($TransactionCode, $NoUrut) { //detail transaksi dengan bin
        $sql = "select dt.*,dbo.KonversiSatuanToText(dt.SKUCode,dt.QtyNeedNow) as Qtykonversi,(select Name from wms.RackSlot where RackSlotCode=dt.SrcRackSlot)as SrcRackName,(select Name from wms.RackSlot where RackSlotCode=dt.DestRackSlot)as DestRackName,convert(varchar(10),dt.ExpDate,121) tanggalExp,CONVERT(VARCHAR(8),dt.CreateTime,108) waktuBuat,b.Keterangan
                from wms.DetailTaskRpl dt
                join barang b on dt.SKUCode = b.Kode
                where dt.TransactionCode =  '" . $TransactionCode . "' and dt.NoUrut='" . $NoUrut . "'";
        $result = $this->db->conn_id->prepare($sql);
        $result->execute();
        $result = $result->fetchAll();
        return $result[0];
    }

    function getDetailTransaction3($TransactionCode, $NoUrut) { //detail transaksi history
        $sql = "select dth.*,
            (select Name from wms.RackSlot where RackSlotCode=dth.SrcRackSlot) as CurrRackName,
            (select Name from wms.RackSlot where RackSlotCode=dth.DestRackSlot) as DestRackName,
            (select Name from wms.Operator where OperatorCode=dth.User_1st) as UserPertama,
            (select Name from wms.Operator where OperatorCode=dth.User_2nd) as UserKedua
            ,b.Keterangan,dth.Time_1st Waktu1,dth.Time_2nd Waktu2
                from wms.DetailTaskRplHistory dth
                join barang b on dth.SKUCode = b.Kode
                where dth.TransactionCode='" . $TransactionCode . "' and dth.NoUrut = '" . $NoUrut . "'";
        $result = $this->db->conn_id->prepare($sql);
        $result->execute();
        $result = $result->fetchAll();
        return $result;
    }
    function setDestRackSlot($TransactionCode, $NoUrut, $RackSlotCode) {

        $sql = "update wms.DetailTaskRpl set DestRackSlot=" . $RackSlotCode . "
                where TransactionCode='" . $TransactionCode . "' and NoUrut = '" . $NoUrut . "'";
        $result = $this->db->conn_id->prepare($sql);
        if ($result->execute()) {
            return true;
        }
        return false;
    }
    function getNote($TransactionCode, $NoUrut) {
        $sql = "select dt.TransactionCode,dt.NoUrut,dt.Note,dt.BinCode,t.ERPCode 
                from wms.DetailTaskRpl dt 
                inner join wms.MasterTaskRpl t
                on t.TransactionCode=dt.TransactionCode
                where dt.TransactionCode='" . $TransactionCode . "' and dt.NoUrut = '" . $NoUrut . "'";

        $result = $this->db->conn_id->prepare($sql);
        $result->execute();
        $result = $result->fetchAll();
        return $result[0];
    }
    function setNote($TransactionCode, $NoUrut, $Note) {
        if (trim($Note, " ") != "") {
            $sql = "update wms.DetailTaskRpl set Note='" . $Note . "'
                where TransactionCode='" . $TransactionCode . "' and NoUrut = '" . $NoUrut . "'";
        } else {
            $sql = "update wms.DetailTaskRpl set Note=NULL
                where TransactionCode='" . $TransactionCode . "' and NoUrut = '" . $NoUrut . "'";
        }
        $result = $this->db->conn_id->prepare($sql);
        if ($result->execute()) {
            return true;
        }
        return false;
    }
    function getNoteMaster($TransactionCode) {
        $sql = "select * 
                from wms.MasterTaskRpl 
                where TransactionCode='" . $TransactionCode . "'";

        $result = $this->db->conn_id->prepare($sql);
        $result->execute();
        $result = $result->fetchAll();
        return $result[0];
    }
    function getstatustask($TransactionCode) {
        $sql = "select isFinishMove,isFinish,isCancel
                from wms.MasterTaskRpl
                where TransactionCode='" . $TransactionCode . "'";

        $result = $this->db->conn_id->prepare($sql);
        $result->execute();
        $result = $result->fetchAll();
        return $result[0];
    }
    function setNoteMaster($TransactionCode, $Note) {
        if (trim($Note, " ") != "") {
            $sql = "update wms.MasterTaskRpl set Note='" . $Note . "'
                where TransactionCode='" . $TransactionCode . "'";
        } else {
            $sql = "update wms.MasterTaskRpl set Note=NULL
                where TransactionCode='" . $TransactionCode . "'";
        }
        $result = $this->db->conn_id->prepare($sql);
        if ($result->execute()) {
            return true;
        }
        return false;
    }
    function cekOutstandingFinishMove($TransactionCode) {
        $sql = "select h.*,o.Name as OperatorName,b.Keterangan,
                (select Name from wms.RackSlot where RackSlotCode=h.SrcRackSlot) as CurrRack
                from wms.DetailTaskRplHistory h
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
    function setFinishMove($TransactionCode, $OperatorCode) {
        $sql = "update wms.MasterTaskRpl set isFinishMove=1,UpdateUserId='" . $OperatorCode . "', UpdateTime=getdate()
                where TransactionCode='" . $TransactionCode . "'";
        $result = $this->db->conn_id->prepare($sql);
        if ($result->execute()) {
            return true;
        }
        return false;
    }
    function setNoteMasterPembatalan($TransactionCode, $Note, $OperatorCode) {

        $sql = "update wms.MasterTaskRpl set Note='" . $Note . "',isCancel=1,UpdateUserId='" . $OperatorCode . "', UpdateTime=getdate()
                where TransactionCode='" . $TransactionCode . "'";
        $result = $this->db->conn_id->prepare($sql);
        if ($result->execute()) {
            return true;
        }
        return false;
    }
    
    function setNotePembatalan($TransactionCode, $NoUrut, $QueueNumber, $OperatorCode) {

        $sql = "update wms.DetailTaskRpl set DestRackSlot=SrcRackSlot
                where TransactionCode='" . $TransactionCode . "' and NoUrut='" . $NoUrut . "'";

        $result = $this->db->conn_id->prepare($sql);

        if ($result->execute()) {
            $sql = "update wms.DetailTaskRplhistory set User_2nd='" . $OperatorCode . "', Time_2nd=getdate(), DestRackSlot=SrcRackSlot, DestOnAisle=0, DestBin='".$this->session->userdata('ReplenishProblem')."', DestQty=Qty
                where TransactionCode='" . $TransactionCode . "' and NoUrut='" . $NoUrut . "' and QueueNumber='" . $QueueNumber . "'";

            $result = $this->db->conn_id->prepare($sql);

            if ($result->execute()) {
                return true;
            }
            return false;
        }
        return false;
        
    }
    function setFinishAdmin($TransactionCode, $OperatorCode) {
        $sql = "update wms.MasterTaskRpl set isFinish=1,UpdateUserId='" . $OperatorCode . "', UpdateTime=getdate()
                where TransactionCode='" . $TransactionCode . "'";
        $result = $this->db->conn_id->prepare($sql);
        if ($result->execute()) {
            return true;
        }
        return false;
    }

}

?>
