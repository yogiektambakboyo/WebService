<?php
include "database.php";
$validPassword="BorwitaCP";
header("Content-Type: application/json");

switch($_POST['edittype']){
    case 'load' :
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
		where cfn.payment_datetime between :payment_datetime_bgn and :payment_datetime_end and (cfn.nc_number<>NULL or cfn.nc_number<>'')";
                 $sth = $dbh->prepare($sql);
		$sth->execute(array(
			//":member_id"=>$_POST["member_id"]
			":payment_datetime_bgn"=>$_POST["payment_datetime_bgn"].' 00:00:00.000'
			,":payment_datetime_end"=>$_POST["payment_datetime_end"].' 23:59:59.000'
               )); 
                $result=$sth->fetchAll();
                echo json_encode($result);
                 
        
}
    

?>