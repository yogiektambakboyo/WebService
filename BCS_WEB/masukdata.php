<?php
include 'setting/include.php';
$filename = str_replace('#', ',', str_replace('~', '.', $_GET["filename"]));
$user = str_replace('#', ',', str_replace('~', '.', $_GET["user"]));
$cabang = str_replace('#', ',', str_replace('~', '.', $_GET["cabang"]));
$db = new DB();
$koneksi = $db->connectDB("30");$arr = array();$kodearr=array();
if ($koneksi["status"]) {
    $delete = true;$fh = fopen("uploads/" . $filename, "r");
    if ($fh) {
        $i = 0;
        while ($line = fgets($fh)) {
            if ($i > 0) {$column = explode(";", $line);
                if (!$db->executeDB("delete from BCS_PelangganCabang where KodeCabang='".$column[0]."'")) {
                    $arr = array("status" =>"0");
                    $delete = FALSE;break;
                }
            }
            $i++;
        }
    } else {$arr = array("status" =>"0");}
    fclose($fh);
    if ($delete) {
        $fh = fopen("uploads/" . $filename, "r");
        if ($fh) {
            $i = 0;
            while ($line = fgets($fh)) {
                if ($i > 0) {
                    $column = explode(";", $line);
                    if ($db->executeDB("insert into BCS_PelangganCabang(KodeCabang,KodeDivisi,Perusahaan,Pemilik,Penghubung,Segment,SubSegment,Alamat,Kota,KodePos,Kecamatan,Kelurahan,NoTelp,NoHP,Longitude,Latitude,CreateBy,TglEntry,CreateDate) VALUES('".$column[0]."','".$column[1]."','".trim(preg_replace('/\s+/', ' ', $column[2]))."','".trim(preg_replace('/\s+/', ' ', $column[17]))."','".$column[4]."','".$column[7]."','".$column[9]."','".trim(preg_replace('/\s+/', ' ', $column[3]))."','".$column[5]."','".$column[10]."','".$column[11]."','".$column[12]."','".$column[6]."','".$column[15]."','".$column[13]."','".$column[14]."','".$user."',getdate(),'".$column[16]."')")) {
                        $arr = array("status" =>"1");
                        $kodearr[] =array("kode"=>$column[1]);
                    } else {$arr = array("status" =>"0");break;}
                }
                $i++;
            }
            $db2 = new DB();
            $koneksidb2 = $db2->connectDB($cabang);
            if($koneksidb2["status"]){
                foreach($kodearr as $rows){
                    $sql2 = "update pelanggan set moq=2 where aktif=1 and kode='".$rows["kode"]."'";
                    $db2->executeDB($sql2);
                }
            }
        } else {$arr = array("status" =>"0");}
        fclose($fh);
    }
    unlink("uploads/" . $filename);
} else {
    $arr[] = array("status"=>"0");
    unlink("uploads/" . $filename);
}
echo json_encode(array($arr));
