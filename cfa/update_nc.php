<?php
	include "database.php";
	$validPassword="BorwitaCP";
	header("Content-Type: application/json");
	if($_POST["password"]!=$validPassword) {
		throw new Exception("Invalid password");
	}
	$dbh = getConnection();
	try{
		$sql = "
			update BCP_CFANotification
			set nc_number=nullif(:nc_number,'')
			where payment_datetime between :payment_datetime_bgn and :payment_datetime_end and (credit_to=:collector or debit_from=:collector)";

		$sth = $dbh->prepare($sql);
		$sth->execute(array(
			":nc_number"=>$_POST["nc_number"]
			,":payment_datetime_bgn"=>$_POST["payment_datetime_bgn"].' 00:00:00.000'
			,":payment_datetime_end"=>$_POST["payment_datetime_end"].' 23:59:59.000'                        
			,":collector"=>$_POST["collector"]
		));
		echo json_encode(array('msg'=>"success"));
	} 
	catch(PDOException $e){
		echo json_encode(array('msg'=>$e->getMessage()));
	}
?>
