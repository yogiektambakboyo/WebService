<?php
session_start();
if ((isset($_SESSION["usernamedfa"]))&&(isset($_SESSION["jabatandfa"]))&&(isset($_SESSION["cabangdfa"]))) {
    header("location:index.php");
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>DFA - Delivery Force Automation</title>
    <link href="js/bootstrap-3.2.0/css/bootstrap.min.css" rel="stylesheet">
    <link href="js/bootstrap-3.2.0/css/bootstrap-theme.min.css" rel="stylesheet">
    <link href="js/bootstrap-3.2.0/css/theme.css" rel="stylesheet">
    <link href="js/pnotify.3.0/pnotify.custom.min.css" rel="stylesheet" type="text/css" />
    <link href="js/formValidator.2.6.1/css/validationEngine.jquery.css" rel="stylesheet" type="text/css" />
    <link href="js/jquery-ui-1.11.2/jquery-ui.min.css" rel="stylesheet" type="text/css" />
    <link type="text/css" href="js/jMetro/css/jquery-ui.css" rel="stylesheet" />

    <script src="js/bootstrap-3.2.0/js/ie-emulation-modes-warning.js"></script>
    <script src="js/bootstrap-3.2.0/js/ie10-viewport-bug-workaround.js"></script>
    <script src="js/jquery-1.8.3.min.js"></script>
    <script src="js/jquery-ui-1.11.2/jquery-ui.min.js" type="text/javascript"></script>
    <script src="js/bootstrap-3.2.0/js/bootstrap.min.js"></script>
    <script src="js/pnotify.3.0/pnotify.custom.min.js"></script>
    <script src="js/formValidator.2.6.1/js/jquery.validationEngine.js"></script>
    <script src="js/formValidator.2.6.1/js/languages/jquery.validationEngine-en.js"></script>

    <script>
        $(document).ready(function(){
            $("#dialog-confirm").hide();
            $("#login").validationEngine();

            <?php
            session_start();
            include "setting/include.php";

            if(isset($_POST["BtnLogin"])){
                $username=trim($_POST["username"]);
                $password=trim($_POST["password"]);
                $cabang=trim($_POST["slcCabang"]);
                if(($username=="")||($password=="")){
                    if($username==""){
                        $status="Username harus diisi!!";
                    }else{
                        $status="Password harus diisi!!";
                    }
                }else{
                    $aktif=0;$nama="";$nik="";$pass="";$status="";
                    $db = new DB();
                    $koneksi = $db->connectDB("00");

                    if ($koneksi["status"]) {
                            $sql = "select * from master..syslogins where name='".$username."' and pwdCompare ('".$password."',password,0) = 1" ;
                            $result = $db->queryDB($sql);
                            if($result["jumdata"]>0){
                                $sql = "select kode,nama,jabatan from staff where nama='".$username."' and jabatan in ('ADMINISTRATOR','KASIR','SPV FA','INKASO','KOOR LOG','LOG','SPV LOG')" ;
                                $result = $db->queryDB($sql);
                                if($result["jumdata"]>0){
                                    while ($row = mssql_fetch_assoc($result["result"])) {
                                        $resultcabang[]=$row;
                                        //header("location:index.php");
                                        $_SESSION["kode"]=$row["kode"];
                                        $_SESSION["usernamedfa"]=$row["nama"];
                                        $_SESSION["jabatandfa"]=$row["jabatan"];
                                        echo "fnBukaSubmitDialog();";
                                    }}
                                else{
                                    $status="Username Tidak Aktif";
                                }
                            }
                            else{
                                $status='Username atau Password Salah';
                            }
                    }
                    else{
                        $status="Koneksi Pusat Putus";
                    }
                }
            }

            ?>

            function fnBukaSubmitDialog() {
                $("#dialog-confirm").show();
                // Define the Dialog and its properties.
                $("#dialog-confirm").dialog({
                    resizable: false,
                    modal: true,
                    title: "Pilih Cabang",
                    width: 400,
                    open: function(event, ui) { $(".ui-dialog-titlebar-close").hide(); },
                    buttons: {
                        "Submit": function () {
                            $(this).dialog('close');
                                $.ajax({
                                    url: 'dfaaction.php?act=gawejejak&f='+$('#slcCabangDlg option:selected').val(),
                                    type: 'POST',
                                    data : {} ,
                                    success: function (response) {
                                        window.location = window.location.origin+"/DFA/index.php";
                                    },
                                    error: function () {}
                                });
                        }
                    }
                });
            }
        });
    </script>

    <link rel="Icon" href="favicon.ico">
</head>

<body  oncontextmenu="return false">




<div id="boxloginform">
    <div id="loginform">
        <h1><strong>DFA Login</strong></h1>
        </br>
        <form method="post" action="login.php" id="login" name="login">
            <?php
            echo "<p class='bg-danger'>".$status."</p>";
            ?>
            <label for="username">Username :  </label>
            <br>
            <div class="input-group">
                <span class="input-group-addon glyphicon glyphicon-user"></span>
                <input type="text" name="username" id="username"  placeholder="Masukkan Username" class="form-control"  data-validation-engine="validate[required]"
                       data-errormessage-value-missing="Isi Username Dahulu"
                       data-errormessage="Isi Username Dahulu"
                    >
                <br>
            </div>
            <label for="password">Password : </label>
            <br>
            <div class="input-group">
                <span class="input-group-addon glyphicon glyphicon-asterisk"></span>
                <input type="password" name="password" id="password"  placeholder="Masukkan Password" class="form-control"
                       data-validation-engine="validate[required]"
                       data-errormessage-value-missing="Isi Password Dahulu"
                       data-errormessage="Isi Password Dahulu"
                    > <br>
            </div>
<!--            <label for="slcCabang">Cabang : </label>
            <br>
            <div class="input-group">
                <select id="slcCabang" class="form-control" name="slcCabang">
                    <?php
/*                        $db = new DB();
                        $koneksi = $db->connectDB("00");
                        if ($koneksi["status"]) {
                            //$sql = "select Kode,Keterangan from cabang where Kode between '01' and '25' order by Keterangan" ;
                            $sql = "select k.Cabang Kode,k.Cabang+' - '+ k.NamaCabang Keterangan from Kategori k where k.TglTransaksi>=dateadd(month,-3,getdate())";
                            $result = $db->queryDB($sql);
                            if($result["jumdata"]>0){
                                while ($row = mssql_fetch_assoc($result["result"])) {
                                    echo "<option value='".$row["Kode"]."'>".$row["Keterangan"]."</option>";
                                }
                            }
                            else{
                                echo "<option value='-1'>Data Cabang Tidak Ada</option>";
                            }
                        }
                        else{
                            echo "<option value='-1'>Koneksi Putus</option>";
                        }
                    */?>
                </select>
            </div>-->
            <br>
            <input type="submit" class="btn btn-primary" name="BtnLogin" value="Login">
        </form>
    </div>
    <div id="dialog-confirm">
        <form>
            <fieldset>
                <div class="input-group">
                    <select id="slcCabangDlg" class="form-control" name="slcCabang">
                        <?php
                        $db = new DB();
                        $koneksi = $db->connectDB("00");
                        if ($koneksi["status"]) {
                            //$sql = "select Kode,Keterangan from cabang where Kode between '01' and '25' order by Keterangan" ;
                            $sql = "select k.Cabang Kode,k.Cabang+' - '+ k.NamaCabang Keterangan from Kategori k where k.TglTransaksi>=dateadd(month,-3,getdate())";
                            $result = $db->queryDB($sql);
                            if($result["jumdata"]>0){
                                while ($row = mssql_fetch_assoc($result["result"])) {
                                    echo "<option value='".$row["Kode"]."'>".$row["Keterangan"]."</option>";
                                }
                            }
                            else{
                                echo "<option value='-1'>Data Cabang Tidak Ada</option>";
                            }
                        }
                        else{
                            echo "<option value='-1'>Koneksi Putus</option>";
                        }
                        ?>
                    </select>
                </div>
            </fieldset>
        </form>
    </div>
</div>




</body>
</html>
