<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

class Login_model extends CI_Model {

    function getLogin($username, $password) {
        $sql = "select name
                from wms.operator
                where OperatorCode='" . $username . "' and password='" . $password . "'";
        $result = $this->db->conn_id->prepare($sql);
        $result->execute();
        $result = $result->fetchAll();
        if (count($result) > 0) {
            return TRUE;
        } else {
            return FALSE;
        }
    }

    function getDataLogin($username, $password) {
        $sql = "select OperatorCode,name,siteId
                from wms.operator
                where OperatorCode='" . $username . "' and password='" . $password . "'";
        $result = $this->db->conn_id->prepare($sql);
        $result->execute();
        $result = $result->fetchAll();
        return $result[0];
    }

    function getOutstanding($OperatorCode) {//ambil jenis task yg masih outstanding menurut login operator
        $statusPCK = TRUE;
        $statusRCV = TRUE;
        $statusRPLManual = TRUE;
        $statusRPL = TRUE;
        $statusSPG = TRUE;

        if ($this->cekOutstandingPCK($OperatorCode) == FALSE) {
            $statusPCK = FALSE;
        }
        if ($this->cekOutstandingRCV($OperatorCode) == FALSE) {
            $statusRCV = FALSE;
        }
        /* if($this->cekOutstandingSPG($OperatorCode)==FALSE)
          {
          $statusSPG=FALSE;
          } */

        if ($this->cekOutstandingRPL($OperatorCode) == FALSE) {
            $statusRPL = FALSE;
        }
        if ($this->cekOutstandingSPG($OperatorCode) == FALSE) {
            $statusSPG = FALSE;
        }
        if ($this->cekOutstandingRPLManual($OperatorCode) == FALSE) {
            $statusRPLManual = FALSE;
        }
        $menuoutstanding = array('statusPCK' => $statusPCK, 'statusRCV' => $statusRCV, 'statusSPG' => $statusSPG, 'statusRPL' => $statusRPL, 'statusRPLManual' => $statusRPLManual);
        return $menuoutstanding;
    }

    function cekOutstanding($OperatorCode) {//cek apakah ada task yg masih outstanding menurut login operator

        if ($this->cekOutstandingPCK($OperatorCode) == FALSE || $this->cekOutstandingRCV($OperatorCode) == FALSE || $this->cekOutstandingRPL($OperatorCode) == False || $this->cekOutstandingRPLManual($OperatorCode) == False) { //|| $this->cekOutstandingSPG($OperatorCode)==FALSE)
            return FALSE;
        }
        return TRUE;
    }

    function cekOutstandingPCK($OperatorCode) {
        $sql = "select count(*) as jumlah from wms.DetailTaskPck where User_1st='" . $OperatorCode . "' 
            and User_2nd is null";
        $result = $this->db->conn_id->prepare($sql);
        $result->execute();
        $result = $result->fetchAll();
        $jumlah = 0;
        foreach ($result as $rowjum) {
            $jumlah = $rowjum['jumlah'];
        }
        if ($jumlah > 0) {
            return false;
        }
        return true;
    }

    function cekOutstandingRCV($OperatorCode) {
        $sql = "select count(*) as jumlah from wms.DetailTaskRcvHistory where User_1st='" . $OperatorCode . "' 
            and User_2nd is null";
        $result = $this->db->conn_id->prepare($sql);
        $result->execute();
        $result = $result->fetchAll();
        $jumlah = 0;
        foreach ($result as $rowjum) {
            $jumlah = $rowjum['jumlah'];
        }
        if ($jumlah > 0) {
            return false;
        }
        return true;
    }

    function cekOutstandingRPL($OperatorCode) {
        $sql = "select count(*) as jumlah
                        from wms.DetailTaskRplHistory d
                        inner join wms.MasterTaskRpl m
                        on m.TransactionCode=d.TransactionCode
                        inner join wms.OperatorWHRole o
                        on m.CreateUserId=o.OperatorCode
                        where User_1st='" . $OperatorCode . "' and User_2nd is null and o.WHRoleCode='10/WHR/000'";
        $result = $this->db->conn_id->prepare($sql);
        $result->execute();
        $result = $result->fetchAll();

        if ($result[0]['jumlah'] > 0) {
            return false;
        }
        return true;
    }

    function cekOutstandingRPLManual($OperatorCode) {
        $sql = "select count(*) as jumlah
                        from wms.MasterTaskRpl 
                        where CreateUserId='" . $OperatorCode . "' and isFinish=0 and isFinishMove=0";
        $result = $this->db->conn_id->prepare($sql);
        $result->execute();
        $result = $result->fetchAll();

        if ($result[0]['jumlah'] > 0) {
            return false;
        }
        return true;
    }
    function cekOutstandingSPG($OperatorCode) {
        $sql = "select count(*) as jumlah from wms.DetailTaskPckS where User_1st='" . $OperatorCode . "' and DestBin is null";
        $result = $this->db->conn_id->prepare($sql);
        $result->execute();
        $result = $result->fetchAll();
        $jumlah = 0;
        foreach ($result as $rowjum) {
            $jumlah = $rowjum['jumlah'];
        }
        if ($jumlah > 0) {
            return false;
        }
        return true;
    }

    function getRole($OperatorCode) {
        $sql = "select r.WHRoleCode,r.Name
                from wms.WHRole r
                inner join
                wms.OperatorWHRole orw
                on r.WHRoleCode=orw.WHRoleCode
                where orw.OperatorCode='" . $OperatorCode . "'";
        $result = $this->db->conn_id->prepare($sql);
        $result->execute();
        $result = $result->fetchAll();
        return $result;
    }

    function getbinimaginer($site) {
        $sql = "select *
                from wms.BinImaginer
                where SiteID='" . $site . "'";
        $result = $this->db->conn_id->prepare($sql);
        $result->execute();
        $result = $result->fetchAll();
        return $result[0];
    }

}

?>
