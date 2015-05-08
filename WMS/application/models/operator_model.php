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
class operator_model extends CI_Model {

    //put your code here
    function getTugas() {
        $sql = "select id,whRoleName
                from wms.whRole
                where isDisable='false'";
        $result = $this->db->conn_id->prepare($sql);
        $result->execute();
        $result = $result->fetchAll();
        return $result;
    }

    function getZone() {
        $sql = "select id,zoneName
                from wms.zone";
        $result = $this->db->conn_id->prepare($sql);
        $result->execute();
        $result = $result->fetchAll();
        return $result;
    }

    function getOperatorWhRole($whRole) {
        $sql = "select o.id,o.staffCode
                from wms.operator o
                join wms.operatorWhRole r on o.id=r.operatorId
                where r.whRoleId='" . $whRole . "'";
        $result = $this->db->conn_id->prepare($sql);
        $result->execute();
        $result = $result->fetchAll();
        return $result;
    }

    function setTeam($siteId, $teamDate, $shift, $whRoleId, $zoneId, $notes) {
        $sql = "insert into wms.team(siteId,teamDate,shift,whRoleId,zoneId,notes) values('" . $siteId . "','" . $teamDate . "','" . $shift . "','" . $whRoleId . "','" . $zoneId . "','" . $notes . "')";
        $result = $this->db->conn_id->prepare($sql);
        $result->execute();
        //echo $this->db->conn_id->errorInfo();
        return $this->db->lastInsertId();
    }

    function setTeamOperator($siteId,$teamId,$operatorId) {
        $sql = "insert into wms.teamOperator(siteId,teamId,operatorId) values('".$siteId."','".$teamId."','".$operatorId."')";
        $result = $this->db->conn_id->prepare($sql);
        $result->execute();
    }

}

?>
