<?php
	include "database.php";
	$validPassword="BorwitaCP";
	//print_r($_POST);
	try{
		header("Content-Type: application/json");
		if($_POST["password"]!=$validPassword) {
			throw new Exception("Invalid password");
		}

		$dbh = getConnection();
		
                $sql = "
		select 
		cfn.ccy
		,cfn.amount
		,cfn.debit_from_name
		,cfn.credit_to_name
		,cfn.payment_datetime
		,cfn.payment_ref		
		from BCP_CFANotification cfn
		where cfn.payment_datetime between :payment_datetime_bgn and :payment_datetime_end";

		$sth = $dbh->prepare($sql);
		$result=$sth->execute(array(
			//":member_id"=>$_POST["member_id"]
			":payment_datetime_bgn"=>$_POST["payment_datetime_bgn"].' 00:00:00.000'
			,":payment_datetime_end"=>$_POST["payment_datetime_end"].' 23:59:59.000'
		));
		if($result) {
			$results=array();
			while($result = $sth->fetch(PDO::FETCH_OBJ)) {
				array_push($results,$result);
			}
			$sth = null;
			$dbh = null;
			$result=array();
			$result["rows"]=$results;
			echo json_encode($result);
		}
		else {
			echo json_encode(array('msg'=>$sth->errorInfo()));
		}
		$sth = null;
		$dbh = null;
	}
	catch(Exception $e){
		echo json_encode(array('msg'=>$e->getMessage()));
	}
?>
