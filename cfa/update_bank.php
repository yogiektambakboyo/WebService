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
			update cfn
			set bank_code=nullif(:bank_code,'')
			from BCP_CFANotification cfn
			where cfn.rq_uuid=:rq_uuid";

		$sth = $dbh->prepare($sql);
		$sth->execute(array(
			":bank_code"=>$_POST["bank_code"]
			,":rq_uuid"=>$_POST["rq_uuid"]
		));
		echo json_encode(array('msg'=>"success"));
	} 
	catch(PDOException $e){
		echo json_encode(array('msg'=>$e->getMessage()));
    }
?>
