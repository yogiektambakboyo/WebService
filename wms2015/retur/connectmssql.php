<?php 
$objConnect = mssql_connect("192.168.28.17","sa","8is4") or die("Error Connect to Database");
$objDB = mssql_select_db("BCP2");
echo "masuk";

?>