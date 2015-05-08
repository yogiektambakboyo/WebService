<?php
    session_start();
    if ((!isset($_SESSION["username"]))||(!isset($_SESSION["nik"]))||(!isset($_SESSION["nama"]))) {
        header("location:login.php");
    }
    include "setting/include.php";
    //get unique id
    $up_id = uniqid();
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
    <script src="bootstrap/js/ie-emulation-modes-warning.js"></script>
    <script src="bootstrap/js/ie10-viewport-bug-workaround.js"></script>
    <script src="bootstrap/js/jquery-1.11.0.min.js"></script>
    <script src="bootstrap/js/bootstrap.min.js"></script>
    <script src="bootstrap/js/jquery.zclip.js"></script>
    <!--Progress Bar and iframe Styling-->
    <link href="bootstrap/css/style_progress.css" rel="stylesheet" type="text/css" />
    <link rel="Icon" href="favicon.ico">
    <script>
        $(document).ready(function(){
            $('#passwordfile').hide();

            $('a#copy-url').zclip({
                path:'bootstrap/js/ZeroClipboard.swf',
                copy:$('p#url').text()
            });

            $('a#copy-urlpublic').zclip({
                path:'bootstrap/js/ZeroClipboard.swf',
                copy:$('p#urlpublic').text()
            });

            $('#isPasswordYes').click(function(){
                $('#passwordfile').show();
                $('#passwordfile').val("");
            });

            $('#isPasswordNo').click(function(){
                $('#passwordfile').hide();
            });

            //show the progress bar only if a file field was clicked
            var show_bar = 0;
            $('input[type="file"]').click(function(){
                show_bar = 1;
            });

            //show iframe on form submit
            $("#form1").submit(function(){

                if (show_bar === 1) {
                    $('#upload_frame').show();
                    function set () {
                        $('#upload_frame').attr('src','upload_frame.php?up_id=<?php echo $up_id; ?>');
                    }
                    setTimeout(set);
                }
            });


        });
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
                <li class="active"><a href="index.php"><span class="glyphicon glyphicon-home"></span> Home</a></li>
                <li><a href="daftarfile.php"><span class="glyphicon glyphicon-list"></span> Daftar File</a></li>
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
        <?php
        if($_SESSION["notif"]==1){
            echo "<div class='alert alert-warning' role='alert'>";
            echo "<p><span class='glyphicon glyphicon-warning-sign'></span> NIK dan Password anda masih sama. Untuk menjaga keamanan password anda, ubahlah password anda. Klik link berikut untuk mengubah password : <a href='upps.php'>Ubah Password</a> </p>";
            echo "</div>";
        }
        ?>
    <div class="divider-vertical-up"></div>
    <h1><span class="glyphicon glyphicon-cloud-upload"></span> Upload File</h1>
    <div class="divider-vertical-down"></div>

    <div class="row">
        <div class="col-md-6"><form role="form" method="POST" action="#" enctype="multipart/form-data"  name="form1" id="form1">
                <div class="form-group">
                    <label for="txtKeterangan">Keterangan <sup class="text-danger">*</sup> (Maks. 25 karakter)</label>
                    <input type="text" name="txtKeterangan" class="form-control" id="txtKeterangan" placeholder="Masukkan Keterangan" maxlength="25" autocomplete="off">
                </div>
                <div class="form-group">
                    <label>Proteksi File Dengan Password <sup class="text-danger">** </sup> :</label>
                    <div class="radio">
                        <label>
                            <input type="radio" name="isPassword" id="isPasswordNo" value="0" checked>
                            Tidak
                        </label>
                    </div>
                    <div class="radio">
                        <label>
                            <input type="radio" name="isPassword" id="isPasswordYes" value="1">
                            Ya
                        </label>
                    </div>
                    <div id="passwordfile">
                        <input  class="form-control" type="password" name="passwordfile" placeholder="Masukkan Password Untuk File" maxlength="8" autocomplete="off">
                    </div>
                </div>
                <div class="form-group">
                    <label for="InputFile">File Input <sup class="text-danger">*</sup> (Maks. 10 MB)</label>
                    <input type="file" id="InputFile" name="InputFile">
                    <p class="help-block">Pilih file yang ingin diupload.</p>
                </div>
                <div class="form-group">
                    <!--APC hidden field-->
                    <input type="hidden" name="APC_UPLOAD_PROGRESS" id="progress_key" value="<?php echo $up_id; ?>"/>
                    <!---->
                    <!--Include the iframe-->
                    <br />
                    <iframe id="upload_frame" name="upload_frame" frameborder="0" border="0" src="" scrolling="no" scrollbar="no" > </iframe>
                    <br />
                    <!---->
                </div>
                <input type="submit" name="submit" value="Submit" class="btn btn-success">
            </form>
        </div>
        <div class="col-md-6">
            <?php
            if(isset($_POST["submit"])){
                $keterangan = trim($_POST["txtKeterangan"]);
                $isPassword = $_POST["isPassword"];
                $passwordfile = trim($_POST["passwordfile"]);
                if($keterangan==""){
                    echo "<h2><p class='bg-danger'>Upload Gagal!</p></h2>";
                    echo "<p class='bg-danger'>Keterangan tidak boleh kosong</p>";
                }else if(($isPassword==1)&&(($passwordfile=="")||(strlen($passwordfile)<2))){
                    echo "<h2><p class='bg-danger'>Upload Gagal!</p></h2>";
                    if($passwordfile==""){
                        echo "<p class='bg-danger'>Password tidak boleh kosong</p>";
                    }else{
                        echo "<p class='bg-danger'>Password minimal 3 karakter</p>";
                    }
                }else if($_FILES["InputFile"]["error"] == 4){
                    echo "<h2><p class='bg-danger'>Upload Gagal!</p></h2>";
                    echo "<p class='bg-danger'>File tidak boleh kosong</p>";
                }else{
                    if (($_FILES['InputFile']['size'] == 0)&&($_FILES['InputFile']['error'] == 0)){
                        echo "<p class='bg-danger'><h2>Upload Gagal!</h2></p>";
                        echo "<p class='bg-danger'>Masukkan File Yang akan di Upload</p>";
                    }else{
                        $today = date("ymd");
                        $temp = explode(".",$_FILES["InputFile"]["name"]);
                        $newfilename = $today."_".$_SESSION["nik"]."_". $temp[0] .".".end($temp);
                        $newfilename = str_replace(" ","_",$newfilename);
                        if (file_exists("upload/".$newfilename)) {
                            echo "<h2><p class='bg-danger'>Upload Gagal!</p></h2>";
                            echo $_FILES["InputFile"]["name"] . " sudah ada!!. Gantilah nama file anda dan coba upload lagi. ";
                        } else {
                            // Input to DB
                            $db=new DB();
                            $koneksi = $db->connectDB();
                            $nourut = 0;
                            $keyarr = array("a","a","b","c","d","e","f","g","h","i","j","k","l","m","n","o","p","q","r","s","t","u","v","w","x","y","z","a","b","c");


                            if($koneksi["status"]){
                                $sql = "select max(CAST(Id as INT)) as Id from MstFile";
                                $getlastnumber = $db->queryDB($sql);
                                if($getlastnumber["jumdata"]==0){
                                    $nourut = 1;
                                }else{
                                    while($row=mssql_fetch_assoc($getlastnumber["result"])){
                                        $nourut=$row["Id"]+1;
                                    }
                                }
                                $rnd_index = rand(1,27);
                                $rnd_index2 = rand(1,27);
                                $key = $keyarr[$rnd_index].$keyarr[$rnd_index2];

                                $sql = "insert into MstFile(Id,Owner,Password,FileName,FileType,FileSize_kb,CreateDate,Description,InKey) values('".$nourut."','".$_SESSION["nik"]."','".$passwordfile."','".$newfilename."','".$_FILES["InputFile"]["type"]."','".ceil($_FILES["InputFile"]["size"]/1024) ."',getdate(),'".$keterangan."','".$key."')";
                                if($db->executeDB($sql)){
                                    echo "<h2><p class='bg-success'>Upload Success!</p></h2>";
                                    echo "Upload    : " . $_FILES["InputFile"]["name"] . "<br>";
                                    //echo "Type      : " . $_FILES["InputFile"]["type"] . "<br>";
                                    echo "Size      : " . ($_FILES["InputFile"]["size"] / 1024) . " kB<br>";
                                    //echo "Temp file : " . $_FILES["InputFile"]["tmp_name"] . "<br>";
                                    move_uploaded_file($_FILES["InputFile"]["tmp_name"],
                                        "upload/" . $newfilename);
                                    echo "Stored local in: <br>";
                                    echo "<p id='url'>http://192.168.31.10:9020/bcpstorage/download.php?id=".$nourut.$key."</p>";
                                    echo "<a href='#' id='copy-url'>Copy URL</a><br>";
                                    echo "Stored public in: <br> ";
                                    //echo "<p id='urlpublic'>http://web.borwita.co.id:9020/bcpstorage/download.php?id=".$nourut.$key."</p>";
                                    echo "<p id='urlpublic'>http://lucia.borwita.co.id:9020/bcpstorage/download.php?id=".$nourut.$key."</p>";
                                    echo "<a href='#' id='copy-urlpublic'>Copy URL Public</a>";
                                    // Insert Log
                                    $sql = "insert into HistoryLog values(getdate(),'".$_SESSION["nik"]."','Upload ".$newfilename."')";
                                    $db->executeDB($sql);
                                }else{
                                    echo "<h2><p class='bg-danger'>Upload Gagal!</p></h2>";
                                    echo "<h2><p class='bg-danger'>Server Terputus!</p></h2>";
                                }
                            }
                        }
                    }
                }
            }
            ?>
        </div>
    </div>

    <br>
    <div class="divider-vertical-down">
        <p class="text-danger">*&nbsp; Harus diisi</p>
        <p class="text-danger">** Password minimal 3 karakter</p>
        <p class="text-warning">***  Sistem akan menghapus file yang di upload setelah 30 hari dihitung dari tgl upload file jadi pastikan anda sudah mempunyai cadangan file yang akan di upload</p>
    </div>
</div>

</body>
</html>

