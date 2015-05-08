<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
class Admin_model extends CI_Model{
    function getSummaryOutstanding()
    {
        $sql = "select ProjectCode,count(*) as outstanding from wms.MasterTaskRcv where isFinish=0 and isCancel=0 group by ProjectCode having count(*)>0
                Union
                select ProjectCode,count(*) as outstanding from wms.MasterTaskPck where isFinish=0 and isCancel=0 group by ProjectCode having count(*)>0
                Union
                select ProjectCode,count(*) as outstanding from wms.MasterTaskRpl where isFinish=0 and isCancel=0 group by ProjectCode having count(*)>0";
        $result = $this->db->conn_id->prepare($sql);
        $result->execute();
        $result = $result->fetchAll();
        return $result;
    }
}
?>
