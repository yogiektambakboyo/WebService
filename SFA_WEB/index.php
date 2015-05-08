<?php
session_start();
include 'include/include.php';

$db = new DB();
$koneksi = $db->connectDB("01");
$err=array();
$resultcabang=array();
if ($koneksi["status"]) {
    $sql = "select * from cabang where kode between '01' and '25' order by keterangan";
    $cabang = $db->queryDB($sql);
    if($cabang["jumdata"] == 0){
        $err[]="Cabang Kosong";
    }else{
        while ($row = mssql_fetch_assoc($cabang["result"])) {
            $resultcabang[$row["Kode"]]=$row["Keterangan"];
        }
    }
}
else{
    $err[]="Koneksi Pusat Putus";
}
if(isset($_POST["btnLogin"])){
    $username=trim($_POST["username"]);
    $password=trim($_POST["password"]);
    if($username!="" && $password!=""){
        $cabanginput=$_POST["cabang"];
        $db = new DB();
        $koneksi2 = $db->connectDB($cabanginput);
        if($koneksi2["status"]){
            $sql = "select * from master..syslogins where name='".$username."' and pwdCompare ('".$password."',password,0) = 1" ;
            $user = $db->queryDB($sql);
            if($user["jumdata"]==0){
                $err[]="Username/Password Salah";
            }else{
                $sql = "select nama,jabatan from staff where nama='".$username."' and jabatan in('CSR','SPV CSR','ADMINISTRATOR')" ;
                $user = $db->queryDB($sql);
                if($user["jumdata"]==0){
                    $err[]="Username Tidak Aktif";
                }else{
                    $_SESSION["username"]=$username;
                    $_SESSION["cabang"]=$cabanginput;
                    while ($row = mssql_fetch_assoc($user["result"])) {
                        $_SESSION["jabatan"]=$row["jabatan"];
                    }
                    $sql = "select * from cabang where kode='".$cabanginput."'";
                    $namacabang = $db->queryDB($sql);
                    while ($row = mssql_fetch_assoc($namacabang["result"])) {
                        $_SESSION["namacabang"]=$row["Keterangan"];
                    }
                    header("location:salesorder.php");
                }
            }
        }else{
            $err[]="Koneksi Cabang Putus";
        }
    }else{
        $err[]="Tolong Isi Lengkap";
    }
}
?>

<html>
    <head>
        <title>SFA</title>
        <?php include 'include/header.php' ?>
        <style type="text/css">
            body {
                padding-top: 40px;
                padding-bottom: 40px;
                background-color: #f5f5f5;
            }

            .form-signin {
                max-width: 300px;
                padding: 19px 29px 29px;
                margin: 0 auto 20px;
                background-color: #fff;
                border: 1px solid #e5e5e5;
                -webkit-border-radius: 5px;
                -moz-border-radius: 5px;
                border-radius: 5px;
                -webkit-box-shadow: 0 1px 2px rgba(0,0,0,.05);
                -moz-box-shadow: 0 1px 2px rgba(0,0,0,.05);
                box-shadow: 0 1px 2px rgba(0,0,0,.05);
            }
            .form-signin .form-signin-heading,
            .form-signin .checkbox {
                margin-bottom: 10px;
            }
            .form-signin input[type="text"],
            .form-signin input[type="password"] {
                font-size: 16px;
                height: auto;
                margin-bottom: 15px;
                padding: 7px 9px;
            }

        </style>
    </head>
    <body>
        <div class="container">
            <div class="row">
                <div class="span12 center">
                    <?php
                    if(count($err)>0){
                        foreach ($err as $row){
                            ?>
                            <div class="alert alert-error"><h5><?php echo $row; ?></h5></div>
                        <?php
                        }
                    }
                    ?>

                    <form class="form-signin" action="index.php" method="POST">
                        <h2 class="form-signin-heading">Login</h2>
                        <input type="text" class="input-block-level" placeholder="Username" name="username">
                        <input type="password" class="input-block-level" placeholder="Password" name="password">
                        <?php
                        if ($koneksi["status"] && $cabang["jumdata"] > 0) {
                            ?>
                            <select name="cabang">
                                <?php
                                foreach($resultcabang as $key => $value) {
                                    ?>
                                    <option value="<?php echo $key ?>"><?php echo $value ?></option>
                                    <?php
                                }
                                ?>
                            </select>
                            <?php
                        }
                        ?>

                        <input type="submit" class="btn btn-large btn-primary" name="btnLogin" value="Login"/>
                    </form>
                </div><!-- /.span4 -->
            </div><!-- /.row -->
        </div><!-- /.container -->
        <?php include 'include/footer.php' ?>
    </body>
</html>
