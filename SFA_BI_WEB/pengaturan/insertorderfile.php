<?php

include '../include/include.php';
$filename = str_replace('#', ',', str_replace('~', '.', $_GET["filename"]));
$salesname = str_replace('#', ',', str_replace('~', '.', $_GET["salesname"]));
$db = new DB();
$koneksi = $db->connectDB("01");
$arr = array();
$ExistSales=1;
$KodeSales='0';
if ($koneksi["status"]) {
    $delete = true;
    $fh = fopen("../UPLOAD/" . $filename, "r");
    if ($fh) {
        $i = 0;
        while ($line = fgets($fh)) {
            if ($i > 0) {
                $column = explode(";", $line);
                $KodeSales=$column[2];
                if (!$db->executeDB("delete from sfa_masterorder where NoOrder='" . $column[4] . "'")) {
                    $arr = array("status" => 0, "data" => "Data Gagal Perbarui");
                    $delete = FALSE;
                    break;
                }
            }
            $i++;
        }
    } else {
        $arr = array("status" => 0, "data" => "Gagal Baca Data");
    }
    fclose($fh);

    //Cek Exits Sales
    $sql="SELECT Kode FROM SFA_SalesPerson WHERE Kode='".$KodeSales."'";
    $result=$db->queryDB($sql);
    //If Not Exits Insert in SalesPerson
    $ExistSales=$result["jumdata"];
    if($result["jumdata"]==0){
        $db->executeDB("INSERT INTO SFA_SalesPerson VALUES('".$KodeSales."','".$salesname."',getdate())");
    }

    if ($delete) {
        $fh = fopen("../UPLOAD/" . $filename, "r");
        if ($fh) {
            $i = 0;
            while ($line = fgets($fh)) {
                if ($i > 0) {
                    $column = explode(";", $line);
                    if(strlen($column[11])>=29){
                        $column[11]=substr($column[11],0,28);
                    }
                    if(count($column)>=14){
                        if ($db->executeDB("INSERT INTO SFA_MasterOrder(NoOrder,Tgl,Sales,ShipTo,Brg,QtyPcs,QtyCrt,Keterangan,CreateDate,EntryTime,Longitude,Latitude) values('" . $column[4] . "','" . substr($column[5],6, 4).substr($column[5],0, 2).substr($column[5],3, 2). "','" . $column[2] . "','" . $column[3] . "','" . $column[7] . "'," . $column[8] . "," . $column[9] . ",'" . $column[11] . "',getdate(),'" . $column[12] . "'," .$column[13]. "," . $column[14] . ")")) {
                            $arr = array("status" => 1, "data" => "Data Berhasil Input");
                        } else {
                            $arr = array("status" => 0, "data" => "Data Gagal Input");
                            break;
                        }
                    }else{
                        if ($db->executeDB("INSERT INTO SFA_MasterOrder(NoOrder,Tgl,Sales,ShipTo,Brg,QtyPcs,QtyCrt,Keterangan,CreateDate,EntryTime,Longitude,Latitude) values('" . $column[4] . "','" . substr($column[5],6, 4).substr($column[5],0, 2).substr($column[5],3, 2). "','" . $column[2] . "','" . $column[3] . "','" . $column[7] . "'," . $column[8] . "," . $column[9] . ",'" . $column[11] . "',getdate(),NULL,NULL,NULL)")) {
                            $arr = array("status" => 1, "data" => "Data Berhasil Input");
                        } else {
                            $arr = array("status" => 0, "data" => "Data Gagal Input");
                            break;
                        }
                    }
                }
                $i++;
            }
        } else {
            $arr = array("status" => 0, "data" => "Gagal Baca Data");
        }
        fclose($fh);
    }
    unlink("../UPLOAD/" . $filename);
} else {
    $arr = array("status" => 0, "data" => $koneksi["data"]);
    unlink("../UPLOAD/" . $filename);
}
echo $_GET['jsoncallback'] . '(' . json_encode($arr) . ');';
?>
