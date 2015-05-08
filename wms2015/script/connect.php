<?php 
if(session_id() == '') {
    session_start();
}

try{
$hostname=$_SESSION['server'];
$dbname=$_SESSION['dbase'];
$username='sa';
$pw='8is4';

$dbh = new PDO ("sqlsrv:server=$hostname;Database=$dbname","$username","$pw");
//$dbh = new PDO("dblib:host=$hostname;dbname=$dbname", $username, $pw);   ==> Aktifkan jika diupload ke Server
}catch (PDOException $e) {
    echo "Failed to get DB handle: " . $e->getMessage() . "\n";
    exit;
}
//$hostname may need to be configured as either...
//$hostname.':'.$port;
