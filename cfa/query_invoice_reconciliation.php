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
				cfn.nc_number,
				cfn.rq_uuid
				from BCP_CFANotification cfn
				where cfn.payment_type=1 and cfn.nc_number=:nc_number and cfn.reconcile_date is null
				order by cfn.payment_datetime";
				 
		$sth = $dbh->prepare($sql);
		$result = $sth->execute(array(
			":nc_number"=>$_POST["nc_number"]
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
						"nc_number"=>$row["nc_number"],
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