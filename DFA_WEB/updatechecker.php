<?php
include 'setting/include.php';$filename = str_replace('#', ',', str_replace('~', '.', $_GET["filename"]));
$db = new DB();$koneksi = $db->connectDB("00");$arr = array();
if ($koneksi["status"]) {
        $fh = fopen("uploads/" . $filename, "r");
        if ($fh) {
            $i = 0;
            while ($line = fgets($fh)) {
                if ($i > 0) {
                    $column = explode(";", $line);
                    if ($db->executeDB("update BCP_DFATolakan set Jml=".$column[2].",ReasonCode='".$column[4]."',Operator='".$column[5]."',StatusT='".$column[6]."' where Faktur='".$column[0]."' and Brg='".$column[1]."'")) {
                        if($db->executeDB("insert into BCP_DFATolakan(tgl,faktur,brg,jml,createby,reasoncode,kodeto,operator,statust) select CONVERT(VARCHAR(16),dateadd(day,datediff(day,0,getdate()),0),121),'".$column[0]."','".$column[1]."',".$column[2].",'".$column[3]."','".$column[4]."',NULL,'".$column[5]."',".$column[6]." where not exists (select * from BCP_DFATolakan where faktur='".$column[0]."' and brg='".$column[1]."')")){
                            $arr[] = array("status" =>"1");
                        }else{
                            $arr[] = array("status" =>"0");
                            break;
                        }
                    } else {
                        $arr[] = array("status" =>"0");
                        break;
                    }
                }
                $i++;
            }
        } else {
            $arr[] = array("status" =>"0");
        }
        fclose($fh);
    unlink("uploads/" . $filename);
} else {
    $arr[] = array("status"=>"0");
    unlink("uploads/" . $filename);
}
echo json_encode(array('deliverydata'=>$arr));
?>
