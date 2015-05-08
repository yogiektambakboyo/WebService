<?php
	session_start();

//
	function getConnection(){
		$server="192.168.31.4";
		$database="bcp";		
		$user = "sa";
		$password = "8is4";
		$result=NULL;
		try{
			// Connect to MSSQL linux
			$result = new PDO ("dblib:host=".$server.";dbname=".$database, $user, $password);//, array(PDO::ATTR_PERSISTENT => true)
			// Connect to MSSQL windows
			//$result = new PDO ("mssql:host=".$server.";dbname=".$database, $user, $password);//, array(PDO::ATTR_PERSISTENT => true)
			$result->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		}
		catch(Exception $e){
			echo "Caught exception: ",  $e->getMessage(), "\n";
		}
		return $result;
	}
?>
