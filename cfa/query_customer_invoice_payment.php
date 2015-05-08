<?php
	include "database.php";
	$validPassword="BorwitaCP";
	header("Content-Type: application/json");

	try{
		if($_POST["password"]!=$validPassword) {
			throw new Exception("Invalid password");
		}
		$dbh = getConnection();
		 $sql = "
				select 
				cfn.payment_datetime,
				cfn.debit_from,
				cfn.debit_from_name,
				cfn.ccy,
				cfn.message,
				cfn.rq_uuid
				from BCP_CFANotification cfn
				where cfn.payment_datetime between :payment_datetime_bgn and :payment_datetime_end and cfn.payment_type=1 and cfn.credit_to =:credit_to and cfn.nc_number is null
				order by cfn.payment_datetime";
				 
		$sth = $dbh->prepare($sql);
		$result = $sth->execute(array(
			//":member_id"=>$_POST["member_id"]
			":payment_datetime_bgn"=>$_POST["payment_datetime_bgn"].' 00:00:00.000',
			":payment_datetime_end"=>$_POST["payment_datetime_end"].' 23:59:59.000',
			":credit_to"=>$_POST["credit_to"]
		)); 
		if($result) {
			$result=$sth->fetchAll();
			if(count($result)>0){
				$results=array();
				foreach ($result as $row) {
					$invoices=json_decode($row["message"]);
					foreach($invoices as $invoice_num => $invoice_amount) {
						array_push($results,array(
						"invoice"=>$invoice_num,
						"amount"=>$invoice_amount,
						"ccy"=>$row["ccy"],
						"debit_from"=>$row["debit_from"],
						"debit_from_name"=>$row["debit_from_name"],
						"payment_datetime"=>$row["payment_datetime"],
						"rq_uuid"=>$row["rq_uuid"]
						));
					}
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
		echo json_encode(array("msg"=>$e->getMessage()));
	}
        
?>