<?php
session_start();
if ((!isset($_SESSION["username"]))||(!isset($_SESSION["nik"]))||(!isset($_SESSION["nama"]))) {
    header("location:login.php");
}
include "setting/include.php";

$db = new DB();
$koneksi = $db->connectDB();
$result;
if ($koneksi["status"]) {
    $status="";
    if($_SESSION["nik"]=="IT"){
        $sql = "select CAST(Id as INT) as Id,Password,Owner,FileName,FileSize_kb,CreateDate,Description,InKey from MstFile order by Id";
    }else{
        $sql = "select CAST(Id as INT) as Id,Password,Owner,FileName,FileSize_kb,CreateDate,Description,InKey from MstFile where Owner='".$_SESSION["nik"]."'  order by Id";
    }
    $result = $db->queryDB($sql);
    if ($result["jumdata"] == 0) {
        $status="Data Kosong";
    }
} else {
    $status = "Koneksi Pusat Putus";
}
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
    <script src="tablesorter/js/jquery.tablesorter.min.js"></script>
    <script src="tablesorter/addons/pager/jquery.tablesorter.pager.min.js"></script>
    <script src="tablesorter/js/jquery.tablesorter.widgets.min.js"></script>
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
                <li  class="active"><a href="daftarfile.php"><span class="glyphicon glyphicon-list"></span> Daftar File</a></li>
                <li><a href="upps.php"><span class="glyphicon glyphicon-random"></span> Ubah Password</a></li>
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
    <h1><span class="glyphicon glyphicon-list"></span> Daftar Upload File</h1>
    <div class="divider-vertical-down"></div>

    <!-- Table View -->
    <div class="table-responsive">
        <table class="tablesorter" id="fileTable">
            <thead>
            <tr>
                <th>ID</th>
                <th>Nama File</th>
                <th>Ukuran (KB)</th>
                <th>Tgl Upload</th>
                <th>Keterangan</th>
                <th>URL Link Public</th>
                <th>URL Link</th>
                <th>Password</th>
            </tr>
            </thead>
            <tbody>
            <?php
                if($result["jumdata"]>0){
                    while($row = mssql_fetch_assoc($result["result"])){
                        ?>
                        <tr>
                            <td><?php echo $row["Id"];?></td>
                            <td><?php echo substr($row["FileName"],8+strlen($_SESSION["nik"]));?></td>
                            <td><?php echo $row["FileSize_kb"];?></td>
                            <td><?php echo $row["CreateDate"];?></td>
                            <td><?php echo $row["Description"];?></td>
                            <td><?php echo "<a href='"."http://lucia.borwita.co.id:9020/bcpstorage/download.php?id=".$row["Id"].$row["InKey"]."' target='_blank'>http://lucia.borwita.co.id:9020/bcpstorage/download.php?id=".$row["Id"].$row["InKey"]."</a>";?></td>
                            <td><?php echo "<a href='"."http://192.168.31.10:9020/bcpstorage/download.php?id=".$row["Id"].$row["InKey"]."'  target='_blank'>http://192.168.31.10:9020/bcpstorage/download.php?id=".$row["Id"].$row["InKey"]."</a>";?></td>
                            <td><?php echo $row["Password"];?></td>
                        </tr>
                        <?php
                    }
                }
            ?>
            </tbody>
            <tfoot>
            <tr>
                <th>ID</th>
                <th>Nama File</th>
                <th>Ukuran (KB)</th>
                <th>Tgl Upload</th>
                <th>Keterangan</th>
                <th>URL Link Public</th>
                <th>URL Link</th>
                <th>Password</th>
            </tr>
            <tr>
                <th colspan="9" class="ts-pager form-horizontal" style="text-align: center">
                    <button type="button" class="btn first"><i class="icon-step-backward glyphicon glyphicon-step-backward"></i>
                    </button>
                    <button type="button" class="btn prev"><i class="icon-arrow-left glyphicon glyphicon-backward"></i>
                    </button>	<span class="pagedisplay"></span>
                    <!-- this can be any element, including an input -->
                    <button type="button" class="btn next"><i class="icon-arrow-right glyphicon glyphicon-forward"></i>
                    </button>
                    <button type="button" class="btn last"><i class="icon-step-forward glyphicon glyphicon-step-forward"></i>
                    </button>
                    <select class="pagesize input-mini" title="Select page size">
                        <option selected="selected" value="10">10</option>
                        <option value="20">20</option>
                        <option value="30">30</option>
                        <option value="40">40</option>
                    </select>
                    <select class="pagenum input-mini" title="Select page number"></select>
                </th>
            </tr>
            </tfoot>
        </table>
    </div>

    <script type="text/javascript">
        $("#fileTable").tablesorter({
            theme: "bootstrap",
            widthFixed: true,
            headerTemplate: '{content} {icon}',
            widgets: ["uitheme", "filter", "zebra"]
        }).tablesorterPager({
                container: $(".ts-pager"),
                cssGoto: ".pagenum",
                output: '{startRow} - {endRow} / {filteredRows} ({totalRows})'
            });
    </script>
</div>

</body>
</html>
