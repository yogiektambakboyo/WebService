<?php

class Assigned_model extends CI_Model {
    
    public function getAllAssignment($OperatorCode)
    {
        $sql = "select distinct m.ProjectCode,o.OprRole,p.LinkAddress,p.NameLinkAddress,r.Name as RoleName
                from wms.DetailTaskOpr o
                inner join wms.MasterTaskRcv m
                on m.TransactionCode=o.TransactionCode 
                inner join wms.ProjectWHRole p
                on m.ProjectCode=p.ProjectCode and p.WHRoleCode=o.OprRole
                inner join wms.WHRole r
                on r.WHRoleCode=o.OprRole
                where o.OperatorCode='".$OperatorCode."' and o.Assigned=0 and m.ProjectCode<>'RTS' 
                union all
                select distinct m.ProjectCode,o.OprRole,p.LinkAddress,p.NameLinkAddress ,r.Name as RoleName 
                from wms.DetailTaskOpr o
                inner join wms.MasterTaskPck m
                on m.TransactionCode=o.TransactionCode 
                inner join wms.ProjectWHRole p
                on m.ProjectCode=p.ProjectCode and p.WHRoleCode=o.OprRole
                inner join wms.WHRole r
                on r.WHRoleCode=o.OprRole
                where o.OperatorCode='".$OperatorCode."' and o.Assigned=0 and m.ProjectCode<>'PTS'
                union all
                select distinct m.ProjectCode,o.OprRole,p.LinkAddress,p.NameLinkAddress ,r.Name as RoleName 
                from wms.DetailTaskOpr o
                inner join wms.MasterTaskRpl m
                on m.TransactionCode=o.TransactionCode 
                inner join wms.ProjectWHRole p
                on m.ProjectCode=p.ProjectCode and p.WHRoleCode=o.OprRole
                inner join wms.WHRole r
                on r.WHRoleCode=o.OprRole
                where o.OperatorCode='".$OperatorCode."' and o.Assigned=0";
        
        $result = $this->db->conn_id->prepare($sql);
        $result->execute();
        $result = $result->fetchAll();
        return $result;
        
    }
}
?>
