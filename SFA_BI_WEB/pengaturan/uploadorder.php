<?php
$dirname = "../UPLOAD/"; 
// If uploading file
if ($_FILES) {    
    print_r($_FILES);
    if ($_FILES["file"]["error"] > 0) {
        echo "Return Code: " . $_FILES["file"]["error"] . "";
    } else {
        unlink($dirname.$_FILES["file"]["name"]);
        move_uploaded_file($_FILES["file"]["tmp_name"],$dirname.$_FILES["file"]["name"]);
    }
}
?>
