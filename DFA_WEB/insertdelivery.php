<?php
include 'setting/include.php';$filename = str_replace('#', ',', str_replace('~', '.', $_GET["filename"]));$delivery = str_replace('#', ',', str_replace('~', '.', $_GET["delivery"]));
$db = new DB();$koneksi = $db->connectDB("00");$arr = array();$alasanBT="";$kkpdv="";
if ($koneksi["status"]) {
    $delete = true;$fh = fopen("uploads/" . $filename, "r");
    if ($fh) {
        $i = 0;
        while ($line = fgets($fh)) {
            if ($i > 0) {$column = explode(";", $line);
                if (!$db->executeDB("delete from BCP_DFA where kodenota='" . $column[0] . "' and faktur='".$column[2]."'")) {
                    $arr[] = array("status" =>"0");
                    $delete = FALSE;break;
                }
                $db->executeDB("delete from bcp_dfatolakan where faktur='".$column[2]."'");
            }
            $i++;
        }
    } else {$arr[] = array("status" =>"0");}
    fclose($fh);
    if ($delete) {
        $fh = fopen("uploads/" . $filename, "r");
        if ($fh) {
            $i = 0;
            while ($line = fgets($fh)) {
                if ($i > 0) {
                    $column = explode(";", $line);
                    if((strlen($column[9])>2)&&$column[10]==1&&$column[6]==0){$column[10]=0;$alasanBT = $column[9];$column[9] = "";
                    }else{$alasanBT = "";}
                    if ($db->executeDB("insert into BCP_DFA(kodenota,faktur,statuskirim,tunai,BG,Stempel,TandaTerima,BGDetail,AlasanBT,CreateDate,Longitude,Latitude,StartEntry,FinishEntry) VALUES('".$column[0]."','".$column[2]."',".$column[10].",".$column[5].",".$column[6].",".$column[7].",".$column[8].",'".$column[9]."','".$alasanBT."',getdate(),".$column[11].",".$column[12].",'".$column[15]."','".$column[16]."')")) {
                        //Insert BCP_DFADetailTransfer
                        if((strlen($column[13])>=1)&&($column[13]!="null")){
                            if($db->executeDB("insert into BCP_DFADetailTransfer(KodeNota,Faktur,Bank,TglTransfer,Jml,TransferStatus,KodeBank) values('".$column[0]."','".$column[2]."','".$column[13]."',getdate(),".$column[14].",'1',NULL)")){
                                $arr[] = array("status" =>"1");
                            }else{
                                $arr[] = array("status" =>"0");
                            }
                        }

                        //Insert BCP_DFADetailCekBG
                        if((strlen($column[9])>1)&&($column[6]>0)){
                            $fin = explode("#",$column[9]);
                            if(count($fin)>=1){
                                for($i=0;$i<count($fin);$i++){
                                    $a=explode("&",$fin[$i]);
                                    $no = $a[0];
                                    $nominal = $a[1];
                                    if ($db->executeDB("insert into BCP_DFADetailCekBG(KodeNota,Faktur,Bank,NoCekBG,TglJatuhTempo,Jml,BGStatus) values('".$column[0]."','".$column[2]."','','".$no."',NULL,".$nominal.",'1')")) {
                                        $arr[] = array("status" =>"1");
                                        $s = $column[0];
                                        if($kkpdv==""){
                                            $kkpdv = "'".$s."'";
                                        } else {
                                            $kkpdv = $kkpdv .", '".$s."'";
                                        }
                                    } else {$arr[] = array("status" =>"0");break;}
                                }
                            }
                        }else{
                            $arr[] = array("status" =>"1");
                            $s = $column[0];
                            if($kkpdv==""){
                                $kkpdv = "'".$s."'";
                            } else {
                                $kkpdv = $kkpdv .", '".$s."'";
                            }
                        }

                        // Insert Tolakan
                        if(strlen($column[17])>3){
                            $fin = explode("#",$column[17]);
                            if(count($fin)>=1){
                                for($i=0;$i<count($fin);$i++){
                                    $a=explode("&",$fin[$i]);
                                    $brg = $a[0];
                                    $jml = $a[1];
                                    $reasoncode = $a[2];
                                    if ($db->executeDB("insert into BCP_DFATolakan(Tgl,Faktur,Brg,Jml,CreateBy,ReasonCode,KodeTO,Operator,StatusT) values('".date("Ymd")."','".$column[2]."','".$brg."',".$jml.",'".$delivery."','".$reasoncode."',NULL,NULL,1)")) {
                                        $arr[] = array("status" =>"1");
                                    } else {$arr[] = array("status" =>"0");break;}
                                }
                            }
                        }
                    } else {$arr[] = array("status" =>"0");break;}
                }
                $i++;
            }
            if($db->executeDB("update MasterDelivery set SudahKembali=1,TglEntry=getdate() where kodenota in (".$kkpdv.")")){
                $arr[] = array("status" =>"1");
            }else{ $arr[] = array("status" =>"0");}
        } else {$arr[] = array("status" =>"0");}
        fclose($fh);
    }
    unlink("uploads/" . $filename);
} else {
    $arr[] = array("status"=>"0");unlink("uploads/" . $filename);
}
echo json_encode(array('deliverydata'=>$arr));
?>
