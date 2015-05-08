<?php
session_start();
if ((isset($_SESSION["username"]))&&(isset($_SESSION["nik"]))&&(isset($_SESSION["nama"]))) {
    header("location:index.php");
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Borwita Storage - Login</title>
    <link href="bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link href="bootstrap/css/bootstrap-theme.min.css" rel="stylesheet">
    <link href="bootstrap/css/theme.css" rel="stylesheet">
    <link href="bootstrap/css/hover-min.css" rel="stylesheet">
    <script src="bootstrap/js/ie-emulation-modes-warning.js"></script>
    <script src="bootstrap/js/ie10-viewport-bug-workaround.js"></script>
    <script src="bootstrap/js/jquery-1.11.0.min.js"></script>
    <script src="bootstrap/js/bootstrap.min.js"></script>
    <script src="bootstrap/js/notify.min.js"></script>
    <link rel="Icon" href="favicon.ico">
</head>

<body onload="notify()">
<?php
session_start();
include "setting/include.php";

    if(isset($_POST["BtnLogin"])){
        $username=trim($_POST["username"]);
        $password=trim($_POST["password"]);
        if(($username=="")||($password=="")){
            if($username==""){
                $status="NIK harus diisi!!";
            }else{
                $status="Password harus diisi!!";
            }
        }else{
            $aktif=0;$nama="";$nik="";$pass="";$status="";
            $db = new DB();
            $koneksi = $db->connectDB();

            if ($koneksi["status"]) {
                $sql = "select NIK,Password,Nama,Aktif from MstKaryawan where NIK='".$username."' and Password='".$password."'";
                $cabang = $db->queryDB($sql);
                if($cabang["jumdata"] == 0){
                    $status="NIK/Password Salah!!";
                }else{
                    while ($row = mssql_fetch_assoc($cabang["result"])) {
                        $nik=$row["NIK"];$nama=$row["Nama"];$aktif=$row["Aktif"];$pass=$row["Password"];
                    }
                    if($aktif==0){
                        $status="NIK tidak aktif!!";
                    }else{
                        $_SESSION["nik"]=$nik;$_SESSION["username"]=$username;$_SESSION["nama"]=$nama;

                        if($nik==$pass){
                            $_SESSION["notif"]=1;
                        }else{
                            $_SESSION["notif"]=0;
                        }

                        //Update Last Login
                        $sql = "update MstKaryawan set LastLogin='".date("Y-m-d H:i:s")."' where NIK='".$nik."'";
                        $db->executeDB($sql);

                        //AutoMate Deleting File in 30 Days
                        $sql = "select FileName from MstFile where CreateDate<'".date('Y-m-d', strtotime('today - 30 days'))."' and owner='".$_SESSION["nik"]."'";

                        $fileold=$db->queryDB($sql);
                        if($fileold["jumdata"]>0){
                            while($row = mssql_fetch_assoc($fileold["result"])){
                                $filedel = $row["FileName"];
                                if(unlink("upload/".$row["FileName"])){
                                    $sqldel = "delete from MstFile where owner='".$_SESSION["nik"]."' and FileName='".$filedel."' and CreateDate<'".date('Y-m-d', strtotime('today - 30 days'))."'";
                                    $db->executeDB($sqldel);
                                    // Insert Log
                                    $sqllog = "insert into HistoryLog values(getdate(),'".$_SESSION["nik"]."','Delete ".$filedel."')";
                                    $db->executeDB($sqllog);
                                }
                            }
                        }
                        header("location:index.php");
                    }
                }
            }
            else{
                $status="Koneksi Pusat Putus";
            }
        }
    }

?>



<div id="boxloginform">
    <div id="loginform">
        <h1>Login User</h1>
        </br>
        <form method="post" action="login.php">
                <?php
                echo "<p class='bg-danger'>".$status."</p>";
                ?>
            <label for="username">NIK :  </label>
            <br>
            <div class="input-group">
                <span class="input-group-addon glyphicon glyphicon-user"></span>
                <input type="text" name="username" id="username"  placeholder="Masukkan NIK" class="form-control"> <br>
            </div>
            <label for="password">Password : </label>
            <br>
            <div class="input-group">
                <span class="input-group-addon glyphicon glyphicon-asterisk"></span>
                <input type="password" name="password" id="password"  placeholder="Masukkan Password" class="form-control"> <br>
            </div>
            <br>
            <input type="submit" class="btn btn-primary hover-shadow" name="BtnLogin" value="Login">
        </form>
    </div>
</div>

<script type="text/javascript">
    function notify() {
        $.notify.defaults({
            autoHideDelay: 10000
        });
        $.notify("Get Error? Email to : it.yogi@borwita.co.id","info");
    }
</script>


</body>
</html>
