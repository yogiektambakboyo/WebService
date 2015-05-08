<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of penolakan_model
 *
 * @author bcp
 */
class Penolakan_model extends CI_Model
{
    function getTolakanList()
    {
        $sql = "SELECT mt.TransactionCode as TransactionCode,mt.ERPCode as ERPCode,mt.TransactionDate as TransactionDate
                FROM wms.MasterTaskRcv mt
                WHERE
                mt.isFinish=0 AND mt.isCancel=0 AND mt.ProjectCode='RJT' 
                ORDER BY mt.TransactionDate DESC
                ";
        $result = $this->db->conn_id->prepare($sql);
        $result->execute();
        $result = $result->fetchAll();
        return $result;
    }
}

?>
