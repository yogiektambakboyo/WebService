<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Borwita Storage - Download</title>
    <link href="bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link href="bootstrap/css/bootstrap-theme.min.css" rel="stylesheet">
    <link href="bootstrap/css/theme.css" rel="stylesheet">
    <link href="bootstrap/css/hover-min.css" rel="stylesheet">
    <script src="bootstrap/js/ie-emulation-modes-warning.js"></script>
    <script src="bootstrap/js/ie10-viewport-bug-workaround.js"></script>
    <script src="bootstrap/js/jquery-1.11.0.min.js"></script>
    <script src="bootstrap/js/bootstrap.min.js"></script>
    <link rel="Icon" href="favicon.ico">
</head>

<body oncontextmenu="return false;">
<?php
session_start();
include "setting/include.php";

$status="";
if(trim($_GET["id"]=="")){
    $id=$_POST["idfile"];
}else{
    $id=trim($_GET["id"]);
}
$owner="";
$filename="";
$filesize="";
$description="";
$password="";
if($id==""){
    $status="File Tidak Ditemukan!!!";
}else{
    $db = new DB();
    $koneksi = $db->connectDB();

    if ($koneksi["status"]) {
        if(strlen($id)>=3){
            $key=substr($id,strlen($id)-2,2);
            $idf=substr($id,0,strlen($id)-2);
            $sql = "select Id,Password,Owner,FileName,FileSize_kb,Description from MstFile where Id='".$idf."' and InKey='".$key."'";
        }else{
            $sql = "select Id,Password,Owner,FileName,FileSize_kb,Description from MstFile where Id='".$id."'";
        }
        $cabang = $db->queryDB($sql);
        if($cabang["jumdata"] == 0){
            $status="File Tidak Ditemukan!!!";
        }else{
            while ($row = mssql_fetch_assoc($cabang["result"])) {
                $owner=$row["Owner"];
                $filename=$row["FileName"];
                $filesize=$row["FileSize_kb"];
                $description=$row["Description"];
                $password=$row["Password"];
            }
        }
        // Validasi Password
        if(isset($_POST["submit"])){
            $txtpassword = trim($_POST["pfile"]);
            $txtowner = trim($_POST["ownerfile"]);
            $txtid = trim($_POST["idfile"]);
            if(strlen($txtid)>=3){
                $key=substr($txtid,strlen($txtid)-2,2);
                $txtid=substr($txtid,0,strlen($txtid)-2);
            }
            $sql = "select Password from MstFile where Owner='".$txtowner."' and id='".$txtid."'";
            $ps = $db->queryDB($sql);

            if($ps["jumdata"]==0){
                $status = "Password Salah!!!";
            }else{
                while($row=mssql_fetch_assoc($ps["result"])){
                    $passworddb = $row["Password"];
                }
                if($txtpassword==$passworddb){
                    $_SESSION["validasi"]=1;
                }else{
                    $status = "Password Salah!!!";
                }
            }
        }
    }
    else{
        $status="Koneksi Pusat Putus";
    }
}
?>

<div id="boxdownloadform">
    <div id="downloadform">
        <h1><span class="glyphicon glyphicon-cloud-download"></span> Download</h1>
        </br>
            <?php
            if($cabang["jumdata"]==0){
                echo "<p class='bg-danger'>".$status."</p>";
            }else{
                if(($password!="")&&(strlen($password)>2)&&($_SESSION["validasi"]==0)){
                    $filenames = preg_replace("/[^0-9 ]/", '*', $filename);
                    if($_SESSION["validasi"]!=1){
                        $_SESSION["validasi"]=0;
                    }
                }else{
                    $filenames = $filename;
                }
                echo "<label>Nama File   :</label></br><label style='color:#00d400'>".$filenames." </label></br>";
                echo "<label>Ukuran File :</label></br><label style='color:#00d400'>".$filesize." KB </label></br>";
                echo "<label>Keterangan  :</label></br><label style='color:#00d400'>".$description." </label>";
                if(($password!="")&&(strlen($password)>2)&&($_SESSION["validasi"]==0)){
                    echo "</br>
                    <div>
                        <form role='form' method='POST' action='download.php'>
                            <label>Password</label><input type='password' class='form-control' id='pfile' name='pfile' placeholder='Masukkan Password'  maxlength='8'>
                            <div style='margin-top:5px;'>
                            <input type='text' name='idfile' value='".$id."' hidden='true'>
                            <input type='text' name='ownerfile' value='".$owner."' hidden='true'>
                            <input type='text' name='id' value='".$id."' hidden='true'>
                            <input type='submit' name='submit' value='Generate Link' class='btn btn-success'>
                            </div>
                        </form>
                    </div>";
                }else{
                    echo "</br></br><div align='center'><a class='downloadlink' href='upload/".$filename."'>Download File</a></div>";
                    session_destroy();
                }
            }
            ?>
    </div>
</div>

</body>
</html>

