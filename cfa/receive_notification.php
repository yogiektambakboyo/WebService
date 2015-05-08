<?php
	include "database.php";
	$validPassword="BorwitaCP";
	$success_flag="0";
	$error_message="Success";
	$order_id="";
	$message="";
	try{
		if($_POST["password"]!=$validPassword) {
			throw new Exception("Invalid password");
		}
		$notEmtpyColumns=array("comm_code","payment_type","ccy","amount","payment_datetime","payment_ref");
		foreach($notEmtpyColumns as $notEmptyColumn) {
			if(strlen(trim($_POST[$notEmptyColumn]))==0) {
				throw new Exception(sprintf("%s is empty",$notEmptyColumn));
			}
		}
		//agar tidak rancu hanya payment_type=1 yang disimpan json nya
		if($_POST["payment_type"]=="1") {
			$message=$_POST["message"];
		}
		$dbh = getConnection();
		$sql = "
		INSERT INTO dbo.BCP_CFANotification
				   (rq_uuid
				   ,rq_datetime
				   ,comm_code
				   ,payment_type
				   ,ccy
				   ,amount
				   ,debit_from
				   ,debit_from_name
				   ,credit_to
				   ,credit_to_name
				   ,[message]
				   ,payment_datetime
				   ,payment_ref
				   )
			 VALUES
				   (:rq_uuid
				   ,:rq_datetime
				   ,:comm_code
				   ,:payment_type
				   ,:ccy
				   ,:amount
				   ,:debit_from
				   ,:debit_from_name
				   ,:credit_to
				   ,:credit_to_name
				   ,:message
				   ,:payment_datetime
				   ,:payment_ref
				   );
		";
		$sth = $dbh->prepare($sql);
		$result=$sth->execute(array(
			":rq_uuid"=>$_POST["rq_uuid"],
			":rq_datetime"=>$_POST["rq_datetime"],
			":comm_code"=>$_POST["comm_code"],
			":payment_type"=>$_POST["payment_type"],
			":ccy"=>$_POST["ccy"],
			":amount"=>$_POST["amount"],
			":debit_from"=>$_POST["debit_from"],
			":debit_from_name"=>$_POST["debit_from_name"],
			":credit_to"=>$_POST["credit_to"],
			":credit_to_name"=>$_POST["credit_to_name"],
			":message"=>$message,
			":payment_datetime"=>$_POST["payment_datetime"],
			":payment_ref"=>$_POST["payment_ref"]
			
		));
		if(!$result) {
			throw new Exception($sth->errorInfo());
		}
		$sth = null;
		$dbh = null;
	}
	catch(Exception $e){
		$success_flag="1";
		$error_message=$e->getMessage();//substr($e->getMessage(),0,-32);
	}
	$reconcile_id=$_POST["payment_ref"];
	$reconcile_datetime=date("Y-m-d H:i:s");
        $error_code='';
        //$newerrormsg=$error_message;
        if (strpos($error_message,'2627') !== false) {
            $error_code='0';
            $newerrormsg= 'Failed (duplicate payment_ref)';
        }else{
            $newerrormsg=$error_message;
        }
	printf("%s,%s,%s,%s",$success_flag,$newerrormsg,$reconcile_id,$reconcile_datetime);
?>
