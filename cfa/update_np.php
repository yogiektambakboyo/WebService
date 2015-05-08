<?php
	include "database.php";
	$validPassword="BorwitaCP";
	//print_r($_POST);
		header("Content-Type: application/json");
		if($_POST["password"]!=$validPassword) {
			throw new Exception("Invalid password");
		}
                   
		$dbh = getConnection();
		try{
                
                $sql = "
		update BCP_CFANotification
                set nc_number=:nc_number
		where payment_datetime between :payment_datetime_bgn and :payment_datetime_end and credit_to=:credit_to or debit_from=:debit_from";

		$sth = $dbh->prepare($sql);
		$sth->execute(array(
			":nc_number"=>$_POST["nc_number"]
			,":payment_datetime_bgn"=>$_POST["payment_datetime_bgn"].' 00:00:00.000'
			,":payment_datetime_end"=>$_POST["payment_datetime_end"].' 23:59:59.000'                        
                    ,":credit_to"=>$_POST["debit_from"]
                        ,":debit_from"=>$_POST["debit_from"]
		));
               
                echo 'Data telah disimpan';
                } catch(PDOException $e)
                {
    //echo $e->getMessage();
    echo json_encode(array('msg'=>$e->getMessage()));
    }
                

    
    

		
	
?>
