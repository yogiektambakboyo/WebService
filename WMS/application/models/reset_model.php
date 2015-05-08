<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of operator_model
 *
 * @author USER
 */
class Reset_model extends CI_Model {

    //put your code here
    function isKodeBin($bin) {
        $sql = "select binCode
                from wms.bin
                where binCode='" . $bin . "'";
        $result = $this->db->conn_id->prepare($sql);
        $result->execute();
        $result = $result->fetchAll();
        if (count($result) > 0) { //Ada
            return TRUE;
        } else {
            return FALSE;
        }
    }

    function isBolehKodeBin($bin) {
        $sql = "select sum(qty) jumlah
                from wms.binSKU
                where binCode='" . $bin . "'";
        $result = $this->db->conn_id->prepare($sql);
        $result->execute();
        $result = $result->fetchAll();
        if ($result[0]['jumlah'] == 0 || $result[0]['jumlah'] == NULL) { //tidak ada quantity atau tidak ada
            return TRUE;
        } else {
            return FALSE;
        }
    }

    function updateIsUsed($bin) {
        $sql = "update wms.bin 
                set isUsed='0'
                where binCode='" . $bin . "'";
        $result = $this->db->conn_id->prepare($sql);
        $result->execute();
    }

}

?>
