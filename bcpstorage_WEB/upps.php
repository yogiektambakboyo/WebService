<?php
session_start();
if ((!isset($_SESSION["username"]))&&(!isset($_SESSION["nik"]))&&(!isset($_SESSION["nama"]))) {
    header("location:login.php");
}
include "setting/include.php";
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Borwita Storage</title>
    <link href="bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link href="bootstrap/css/bootstrap-theme.min.css" rel="stylesheet">
    <link href="bootstrap/css/theme.css" rel="stylesheet">
    <link href="tablesorter/css/theme.bootstrap.css" rel="stylesheet">
    <script src="bootstrap/js/ie-emulation-modes-warning.js"></script>
    <script src="bootstrap/js/ie10-viewport-bug-workaround.js"></script>
    <script src="bootstrap/js/jquery-1.11.0.min.js"></script>
    <script src="bootstrap/js/bootstrap.min.js"></script>
    <link rel="Icon" href="favicon.ico">
</head>
<body>
<!-- Fixed navbar -->
<div class="navbar navbar-default navbar-fixed-top" role="navigation">
    <div class="container">
        <div class="navbar-header">
            <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
                <span class="sr-only">Toggle navigation</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>
            <a class="navbar-brand" href="#">Borwita Storage</a>
        </div>
        <div class="navbar-collapse collapse">
            <ul class="nav navbar-nav">
                <li><a href="index.php"><span class="glyphicon glyphicon-home"></span> Home</a></li>
                <li><a href="daftarfile.php"><span class="glyphicon glyphicon-list"></span> Daftar File</a></li>
                <li class="active"><a href="upps.php"><span class="glyphicon glyphicon-random"></span> Ubah Password</a></li>
                <li><a href="faq.php"><span class="glyphicon glyphicon-question-sign"></span> Bantuan & FAQ</a></li>
                <li><a href="logout.php"><span class="glyphicon glyphicon-log-out"></span> Log Out</a></li>
            </ul>
            <ul class="nav navbar-nav navbar-right">
                <li><a href="#"><span class="glyphicon glyphicon-user"></span> User : <?php echo $_SESSION["nama"];?></a></li>
            </ul>
        </div><!--/.nav-collapse -->
    </div>
</div>

<div class="container theme-showcase" role="main">
    <div class="divider-vertical-up"></div>
    <h1><span class="glyphicon glyphicon-random"></span> Ubah Password</h1>
    <div class="divider-vertical-down"></div>


    <div class="row">
        <div class="col-md-5">
            <form method="post" action="upps.php">
                <?php

                if(isset($_POST["BtnUpdatePassword"])){
                    $status="";
                    $passlama = trim($_POST["passwordlama"]);
                    $passbaru = trim($_POST["passwordbaru"]);
                    $passbaru2 = trim($_POST["passwordbaru2"]);
                    if(($passlama=="")||($passbaru=="")||($passbaru2=="")){
                        if($passlama==""){
                            $status = "Password lama tidak boleh kosong!!!";
                        }else if($passbaru==""){
                            $status = "Password baru tidak boleh kosong!!!";
                        }else{
                            $status = "Konfirmasi password baru tidak boleh kosong!!!";
                        }
                        echo "<div class='alert alert-danger' role='alert'>";
                        echo "<p> <span class=' glyphicon glyphicon-remove'></span> ".$status."</p>";
                        echo "</div>";
                    }else if($passbaru!=$passbaru2){
                        $status = "Password baru tidak sama!!!";
                        echo "<div class='alert alert-danger' role='alert'>";
                        echo "<p> <span class=' glyphicon glyphicon-remove'></span> ".$status."</p>";
                        echo "</div>";
                    }else if(strpos($passbaru,' ') !== false){
                        $status = "Password baru tidak boleh ada spasi!!!";
                        echo "<div class='alert alert-danger' role='alert'>";
                        echo "<p> <span class=' glyphicon glyphicon-remove'></span> ".$status."</p>";
                        echo "</div>";
                    }else if((strlen($passbaru))<4){
                        $status = "Password baru harus lebih dari 5 karakter!!!";
                        echo "<div class='alert alert-danger' role='alert'>";
                        echo "<p> <span class=' glyphicon glyphicon-remove'></span> ".$status."</p>";
                        echo "</div>";
                    }else{
                        $db = new DB();
                        $koneksi = $db->connectDB();
                        $result;
                        $passdb="";
                        if($koneksi["status"]){
                            $sql = "select Password from mstKaryawan where NIK='".$_SESSION["nik"]."'";
                            $result = $db->queryDB($sql);
                            if($result["jumdata"]>0){
                                while($row = mssql_fetch_assoc($result["result"])){
                                   $passdb=$row["Password"];
                                }
                                if(strtoupper($passlama)==$passdb){
                                    $sql="update MstKaryawan set Password='".strtoupper($passbaru)."' where NIK='".$_SESSION["nik"]."'";
                                    if($db->executeDB($sql)){
                                        $status = "Ganti password berhasil";
                                        echo "<div class='alert alert-success' role='alert'>";
                                        echo "<p> <span class=' glyphicon glyphicon-ok'></span> ".$status."</p>";
                                        echo "</div>";
                                    }else{
                                        $status = "Gagal Mengganti password!!";
                                        echo "<div class='alert alert-danger' role='alert'>";
                                        echo "<p> <span class=' glyphicon glyphicon-remove'></span> ".$status."</p>";
                                        echo "</div>";
                                    }
                                }else{
                                    $status = "Password lama salah!!";
                                    echo "<div class='alert alert-danger' role='alert'>";
                                    echo "<p> <span class=' glyphicon glyphicon-remove'></span> ".$status."</p>";
                                    echo "</div>";
                                }
                            }else{
                                $status = "Password lama salah!!";
                                echo "<div class='alert alert-danger' role='alert'>";
                                echo "<p> <span class=' glyphicon glyphicon-remove'></span> ".$status."</p>";
                                echo "</div>";
                            }
                        }
                    }

                }
                ?>

                <label for="passwordlama">Password Lama <sup class="text-danger">*</sup> :  </label>
                <br>
                <div class="input-group">
                    <span class="input-group-addon glyphicon glyphicon-user"></span>
                    <input type="password" name="passwordlama" id="passwordlama"  placeholder="Masukkan Password Lama" class="form-control"> <br>
                </div>
                <label for="passwordbaru">Password Baru <sup class="text-danger">**</sup> : </label>
                <br>
                <div class="input-group">
                    <span class="input-group-addon glyphicon glyphicon-asterisk"></span>
                    <input type="password" name="passwordbaru" id="passwordbaru"  placeholder="Masukkan Password Baru" class="form-control" maxlength="12"> <br>
                </div>
                <br>
                <label for="passwordbaru2">Konfirmasi Password Baru <sup class="text-danger">**</sup>: </label>
                <br>
                <div class="input-group">
                    <span class="input-group-addon glyphicon glyphicon-asterisk"></span>
                    <input type="password" name="passwordbaru2" id="passwordbaru2"  placeholder="Masukkan Password Baru Lagi" class="form-control" maxlength="12"> <br>
                </div>
                <br>
                <input type="submit" class="btn btn-primary hover-shadow" name="BtnUpdatePassword" value="Submit">
            </form>
        </div>
    </div>
    <br>

    <div class="divider-vertical-down">
        <p class="text-danger">*&nbsp; Harus diisi</p>
        <p class="text-danger">** Password baru minimal 5 karakter dan maksimal 12 karakter</p>
        <p class="text-danger">** Password baru tidak boleh menggunakan spasi</p>
    </div>

</div>


</body>
</html>