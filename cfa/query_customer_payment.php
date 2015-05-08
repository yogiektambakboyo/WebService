<?php
include "database.php";
	$validPassword="BorwitaCP";

try{
		header("Content-Type: application/json");
		if($_POST["password"]!=$validPassword) {
			throw new Exception("Invalid password");
		}
                $dbh = getConnection();
                 $sql = "
		select 
                cfn.ccy,
                cfn.amount,
		cfn.credit_to,
                cfn.credit_to_name,
                cfn.debit_from,
                cfn.debit_from_name,
                cfn.message,
                cfn.payment_ref,
                cfn.payment_datetime
		from BCP_CFANotification cfn
		where cfn.payment_datetime between :payment_datetime_bgn and :payment_datetime_end and cfn.credit_to = :credit_to and (cfn.nc_number=NULL or cfn.nc_number='')";
                 
                $sth = $dbh->prepare($sql);
		$sth->execute(array(
			//":member_id"=>$_POST["member_id"]
			":payment_datetime_bgn"=>$_POST["payment_datetime_bgn"].' 00:00:00.000'
			,":payment_datetime_end"=>$_POST["payment_datetime_end"].' 23:59:59.000'
                        ,":credit_to"=>$_POST["credit_to"]
		)); 
                //print_r($sth->fetchAll());
                $result=$sth->fetchAll();
                if(count($result)>0){
                    $results=array();
                    foreach ($result as $row) {
                        
                    
                        array_push($results,$row);
                    }
                    //print_r($results);
                    $result=array();
		    $result["rows"]=$results;
                    echo json_encode($result);
                }else{
                    echo json_encode(array('msg'=>$sth->errorInfo()));
                }
                $sth = null;
		$dbh = null;
                
}

catch(Exception $e){
    echo json_encode(array('msg'=>$e->getMessage()));
    
}
        
?>