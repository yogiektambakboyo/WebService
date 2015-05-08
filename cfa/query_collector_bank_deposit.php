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
			cfn.credit_to,
			cfn.amount,
			cfn.ccy,
			cfn.credit_to_name,
			cfn.payment_datetime,
			cfn.rq_uuid		
			from BCP_CFANotification cfn
			where cfn.payment_datetime between :payment_datetime_bgn and :payment_datetime_end and cfn.payment_type=2 and cfn.debit_from=:debit_from and cfn.nc_number is null
			order by cfn.payment_datetime";
			$sth = $dbh->prepare($sql);
			$result = $sth->execute(array(
				":payment_datetime_bgn"=>$_POST["payment_datetime_bgn"].' 00:00:00.000',
				":payment_datetime_end"=>$_POST["payment_datetime_end"].' 23:59:59.000',
				":debit_from"=>$_POST["debit_from"]
			)); 
			if($result) {
				$result=$sth->fetchAll();
				if(count($result)>0){
					$results=array();
					foreach ($result as $row) {
						array_push($results,array(
						"credit_to"=>$row["credit_to"],
						"amount"=>$row["amount"],
						"ccy"=>$row["ccy"],
						"credit_to_name"=>$row["credit_to_name"],
						"payment_datetime"=>$row["payment_datetime"],
						"rq_uuid"=>$row["rq_uuid"]
						));
					}
					$result=array();
					$result["rows"]=$results;
					echo json_encode($result);
				}
				else{
					echo json_encode(array("msg"=>"no rows found"));
				}
			}
			else {
				echo json_encode(array("msg"=>$sth->errorInfo()));
			}
				$sth = null;
		$dbh = null;
	}
	catch(Exception $e){
		echo json_encode(array('msg'=>$e->getMessage()));
	}
        
?>