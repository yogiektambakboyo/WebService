<?php
session_start();
if (!isset($_SESSION["username"])) {
    header("location:index.php");
}
if (strtoupper($_SESSION["jabatan"]) != "ADMINISTRATOR") {
    header("location:index.php");
}
include 'include/include.php';
$db = new DB();
$koneksi = $db->connectDB("01");
$err = array();
$resultcabang = array();
if ($koneksi["status"]) {
    $sql = "select * from cabang where kode='01'";
    $cabang = $db->queryDB($sql);
    if ($cabang["jumdata"] == 0) {
        $err[] = "Cabang Kosong";
    } else {
        while ($row = mssql_fetch_assoc($cabang["result"])) {
            $resultcabang[$row["Kode"]] = $row["Keterangan"];
        }
    }
} else {
    $err[] = "Koneksi Pusat Putus";
}
$statusrfs = NULL;
if (isset($_POST["btnSync"])) {
    $cabangrfs = $_POST["cabang"];
    $dbbrg = new DB();
    $koneksibrg = $dbbrg->connectDB($cabangrfs);

    if ($koneksibrg["status"]) {

        $sql = "select kode,keterangan,hint from barang";
        $resultbrg = $dbbrg->queryDB($sql);
        $barangfrom = array();
        while ($row = mssql_fetch_assoc($resultbrg["result"])) {
            $barangfrom[] = array("kode" => str_replace("'", "", $row["kode"]), "keterangan" => str_replace("'", "", $row["keterangan"]), "hint" => str_replace("'", "", $row["hint"]));
        }
        $dbmaster = new DB();
        $koneksimaster = $dbmaster->connectDB("01");
        if ($koneksimaster["status"]) {
            $statusrfs = true;
            foreach ($barangfrom as $row) {
                $sql = "select * from sfa_barang where kode='" . $row["kode"] . "'";
                $brg = $dbmaster->queryDB($sql);
                if ($brg["jumdata"] > 0) {
                    $sql = "update sfa_barang set keterangan='" . $row["keterangan"] . "',hint='" . $row["hint"] . "' where kode='" . $row["kode"] . "'";
                } else {
                    $sql = "insert into sfa_barang values('" . $row["kode"] . "','" . $row["keterangan"] . "','" . $row["hint"] . "')";
                }
                if (!$dbmaster->executeDB($sql)) {
                    //echo "insert into barang values('".$row["kode"]."','".$row["keterangan"]."','".$row["hint"]."')";
                    $statusrfs = false;
                    $err[] = "Sync Gagal";
                    break;
                }
            }
        } else {
            $err[] = "Koneksi Cabang Putus";
        }
    } else {
        $err[] = "Koneksi Cabang Putus";
    }
}
?>

<html>
    <head>
        <title>Master Barang</title>
        <?php include 'include/header.php' ?>
        <style type="text/css">
            .scroll{
                overflow: scroll;
                height: 500px;
                width: auto;
            }
            .buttonselect{
                position: absolute;
                top: 140px;
                right: 250px;
            }
        </style>
    </head>
    <body>
        <div class="navbar navbar-static-top navbar-inverse">
            <div class="navbar-inner ">

                <div class="container">
                    <h2><font color="#FFFFFF">SFA Order <?php echo $_SESSION["namacabang"]; ?></font></h2>
                    <div class="nav-collapse collapse">
                        <ul class="nav">
                            <?php
                            if (strtoupper($_SESSION["jabatan"]) == "ADMINISTRATOR") {
                                ?>
                                <li>
                                    <a href="salesorder.php"><i class="icon-home icon-white"></i> Home</a>
                                </li>
                                <li>
                                    <a href="#"><i class="icon-refresh icon-white"></i> Sync Master Barang</a>
                                </li>
                                <?php
                            }
                            ?>
                            <li>
                                <a href="logout.php"><i class="icon-user icon-white"></i> LOGOUT</a>
                            </li>
                        </ul>
                    </div><!-- /.nav-collapse -->
                </div><!-- /.container -->
            </div><!-- /.navbar-inner -->
        </div><!-- /.navbar -->
        <div class="container">
            <div class="row">
                <br>
                <?php
                if (count($err) > 0) {
                    foreach ($err as $row) {
                        ?>
                        <div class="alert alert-error"><h5><?php echo $row; ?></h5></div>
                        <?php
                    }
                }
                if ($statusrfs==true) {
                    ?>
                    <div class="alert alert-success"><h5>Sync Berhasil</h5></div>
                    <?php
                }
                ?>
                <br>
                <form action="masterbarang.php" method="POST">
                    <?php
                    if ($koneksi["status"] && $cabang["jumdata"] > 0) {
                        ?>
                        <select name="cabang">
                            <?php
                            foreach ($resultcabang as $key => $value) {
                                ?>
                                <option value="<?php echo $key ?>"><?php echo $value ?></option>
                                <?php
                            }
                            ?>
                        </select>
                        <?php
                    }
                    ?>

                    <input type="submit" class="btn btn-large btn-primary" name="btnSync" value="Sync"/>
                </form>
            </div><!-- /.row -->
        </div><!-- /.container -->

        <?php include 'include/footer.php' ?>

    </body>
</html>

