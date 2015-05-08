<?php session_start();
	if(isset($_SESSION["divisi"])){
		echo "true";
	}else{
		echo "false";
	}
?>