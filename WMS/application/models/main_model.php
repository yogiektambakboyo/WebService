<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

class Main_model extends CI_Model{
    function __construct() {
        parent::__construct();
    }
    function  getlink($rolecode){
        //$sql = "select LinkAddress,NameLinkAddress from wms.ProjectWHRole where WHRoleCode='".$rolecode."'";
        $sql = "select * from wms.ProjectWHRole where WHRoleCode='".$rolecode."' and LinkAddress is not null";
        $result = $this->db->conn_id->prepare($sql);
        $result->execute();
        $result = $result->fetchAll();
        return $result;
    }
}
?>
