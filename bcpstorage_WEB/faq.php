<?php
session_start();
if ((!isset($_SESSION["username"]))||(!isset($_SESSION["nik"]))||(!isset($_SESSION["nama"]))) {
    header("location:login.php");
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
    <script type="text/javascript" src="swfobject.js"></script>
    <script type="text/javascript">
        swfobject.registerObject("csSWF", "9.0.115", "expressInstall.swf");
    </script>
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
                <li><a href="upps.php"><span class="glyphicon glyphicon-random"></span> Ubah Password</a></li>
                <li class="active"><a href="#"><span class="glyphicon glyphicon-question-sign"></span> Bantuan & FAQ</a></li>
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
    <h1><span class="glyphicon glyphicon-question-sign"></span> Bantuan & FAQ</h1>
    <div class="divider-vertical-down"></div>
    <h3>Apa itu BCP Storage?</h3>
    <div class="row">
        <div class="col-md-8">
            <p>BCP Storage adalah media penyimpanan file dengan jangka waktu tertentu. Website ini digunakan untuk mempermudah pengiriman file dengan ukuran yang cukup besar.
                File yang di upload akan terhapus dengan sendirinya setelah berumur 30 hari dari tanggal upload file. Anda bisa menyimpan banyak file tapi kami membatasi setiap file yang akan diupload tidak melebihi 10MB</p>
        </div>
        <div class="col-md-4">
            <object classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000" width="320" height="167" id="csSWF">
                <param name="movie" value="tutorbcp_controller.swf" />
                <param name="quality" value="best" />
                <param name="bgcolor" value="#1a1a1a" />
                <param name="allowfullscreen" value="true" />
                <param name="scale" value="showall" />
                <param name="allowscriptaccess" value="always" />
                <param name="flashvars" value="autostart=true&thumb=FirstFrame.png&thumbscale=45&color=0x000000,0x000000" />
                <!--[if !IE]>-->
                <object type="application/x-shockwave-flash" data="tutorbcp_controller.swf" width="320" height="167">
                    <param name="quality" value="best" />
                    <param name="bgcolor" value="#1a1a1a" />
                    <param name="allowfullscreen" value="true" />
                    <param name="scale" value="showall" />
                    <param name="allowscriptaccess" value="always" />
                    <param name="flashvars" value="autostart=true&thumb=FirstFrame.png&thumbscale=45&color=0x000000,0x000000" />
                    <!--<![endif]-->
                    <div id="noUpdate">
                        <p>please update your version of the free Flash Player by <a href="http://www.adobe.com/go/getflashplayer">downloading here</a>.</p>
                    </div>
                    <!--[if !IE]>-->
                </object>
                <!--<![endif]-->
            </object>
        </div>
    </div>
    <h3>Fungsi dari Menu-Menu Di BCP Storage</h3>
    <img alt="tutor_1" src="tutor_1.jpg">
    <div style="text-align: center">Gb. 01</div>
    <ol>
        <li><span class="glyphicon glyphicon-home"></span> Home : Tab ini digunakan untuk mengupload file</li>
        <li><span class="glyphicon glyphicon-list"></span> Daftar File : Tab ini berisi daftar dan informasi file yang pernah di upload oleh user yang sedang aktif</li>
        <li><span class="glyphicon glyphicon-random"></span> Ubah Password : Tab ini digunakan untuk mengubah password user yang sedang aktif</li>
        <li><span class="glyphicon glyphicon-question-sign"></span> Bantuan & FAQ : Tab ini berisi dokumentasi dan pertanyaan yang sering ditanyakan juga jawabannya</li>
        <li><span class="glyphicon glyphicon-log-out"></span> Log Out : Tab ini digunakan untuk keluar dari aplikasi</li>
        <li><span class="glyphicon glyphicon-user"></span> User : XXX : Tab ini menginformasikan nama user yang sedang aktif</li>
    </ol>
    <h3>Saya ingin mengupload file, Apa yang harus saya lakukan?</h3>
    <div class="row">
        <div class="col-md-4">
            <img alt="tutor_1" src="tutor_2.jpg"  class="img-thumbnail">
            <div style="text-align: center">Gb. 02</div>
            <img alt="tutor_1" src="tutor_3.jpg"  class="img-thumbnail">
            <div style="text-align: center">Gb. 03</div>
        </div>
        <div class="col-md-8">
            <ol>
                <li>Siapkan dahulu file yang akan di upload, Pastikan ukurannya tidak melebihi 10 MB jika file melebihi cobalah gunakan aplikasi kompresi(WinRar, WinZip, 7Zip dll.) untuk mengurangi ukuran file.</li>
                <li>Buka Aplikasi BCP Storage di browser - Klik Tab Home </li>
                <li>Isi kolom keterangan file </li>
                <li>Pilih option proteksi file dengan password, Jika Ya file akan di proteksi dengan password saat akan mendownload file dan jika memilih Tidak user akan bisa langsung mendownload file </li>
                <li>Klik File Input untuk memilih file yang akan di upload </li>
                <li>Jika semua form sudah terisi klik tombol Submit untuk memulai upload file </li>
                <li>Akan muncul notifikasi tentang file yang akan di upload </li>
                <li>Macam - macam Notifikasi file : Jika yang muncul "Upload Succes" maka file berhasil di upload. Jika "Upload Gagal" silahkan cek kondisi jaringan anda apakah stabil/ tidak. Jika ada keterangan "Upload Gagal file sudah ada" nama ulang file anda lalu lakukan upload lagi </li>
                <li>Setelah muncul notifikasi "Upload Success" seperti gambar 02</li>
                <li>File anda akan bisa di akses dengan 2 URL : URL yang pertama (http://192.168.31.10:9020/xxxx) hanya bisa diakses di internal kantor BCP SBY tanpa diperlukan jaringan internet, Sedangkan URL yang kedua (http://lucia.borwita.co.id:9020/xxx) bisa diakses dari internal dan eksternal kantor BCP SBY selama ada jaringan internet. </li>
                <li>Cukup copy url tersebut dan pastekan di email, recipient akan dengan mudah menerima file tsb. Bagi recipient copy URL yang didapat dari email, buka browser(Mozilla Firefox, Chrome atau Internet Explorer) lalu pastekan URL - Enter   </li>
                <li>Tampilan yang ditemui recipient akan seperti Gb. 03</li>
                <li>Klik Download File dan File terdownload. Selesai</li>
            </ol>
        </div>
    </div>
</div>

</body>
</html>
