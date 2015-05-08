<?php
session_start();
if ((!isset($_SESSION["usernamedfa"]))||(!isset($_SESSION["jabatandfa"]))) {
    header("location:login.php");
}
include "setting/include.php";$cabang="00";$db=new DB();$resultcabang=array();$koneksi=$db->connectDB($cabang);$Sopir=$_GET["f"];$Brg=$_GET["g"];$today = date("Ymd");

try{
    if($_GET["act"]=="cekinputkasirvsdelivery"){
        if($koneksi["status"]){
            $sql="select a.Kodenota,a.JmlKasir,b.JmlDelivery from (select Kodenota,SUM(Jml) as JmlKasir from BCP_DFARekapanKKPDV where kodenota in (".$Sopir.") and tipe in ('Tunai','Transfer') and StatusT=1 group by Kodenota) a join (select b.kodenota,sum(b.Tunai)+sum(COALESCE(t.Jml,0)) as JmlDelivery from bcp_dfa b left join bcp_dfadetailtransfer t on t.kodenota=b.kodenota and t.faktur=b.faktur where b.NC IS NULL and  b.Kodenota in (".$Sopir.") group by b.Kodenota) b on b.Kodenota=a.Kodenota";
            $result=$db->queryDB($sql);
            if($result["jumdata"]>0){
                while ($row = mssql_fetch_assoc($result["result"])) {
                    $resultcabang[]=$row;
                }
            }
            else{
                $resultcabang[]=array("Kodenota"=>"0","JmlKasir"=>"0","JmlDelivery"=>"0");
            }
        }
        else{
            $resultcabang[]=array("Kodenota"=>"0","JmlKasir"=>"0","JmlDelivery"=>"0");
        }
        print json_encode($resultcabang);

    }

    if($_GET["act"]=="inputtolakan"){
        $key = $_SESSION["cabangdfa"]."/TO/";
        $key = stripslashes($key);
        $maxKode = "";
        $rs=array();
        if($koneksi["status"]){
            $sql="select RIGHT(MAX(kodenota),6) as MaxNumber from bcp_tolakandms where LEFT(kodenota,9)='".$key."'";
            $result=$db->queryDB($sql);
            if($result["jumdata"]>0){
                while ($row = mssql_fetch_assoc($result["result"])) {
                    $resultcabang=$row["MaxNumber"];
                    $maxKode = $_SESSION["cabangdfa"]."/TO/".date("Y")."/".sprintf("%06s", $resultcabang+1);
                    //$rs[] = $row;
                }
                $sql = "select Faktur,Brg,Jml from bcp_dfatolakan where Operator IS NOT NULL and KodeTO IS NULL and StatusT=1 and Jml>0 and Faktur=".$Sopir;
                $result=$db->queryDB($sql);
                if($result["jumdata"]>0){
                    while ($row = mssql_fetch_assoc($result["result"])) {
                        $sql2 = "insert into bcp_tolakandms(Kodenota,kodeinvoice,Brg,Jml,hrgsatuan,disc1,disc2,disc3,disc4,tgl,alasan,discvaluepost,reasoncode) select '".$maxKode."',dj.kodenota,dj.brg,b.jml,dj.hrgsatuan,dj.Disc1,dj.Disc2,dj.Disc3,dj.Disc4,CONVERT(VARCHAR(16),dateadd(day,datediff(day,0,getdate()),0),121),a.keterangan,dj.DiscValuePost,b.ReasonCode from detailjual dj join bcp_dfatolakan b on b.faktur=dj.kodenota and b.brg=dj.brg join alasanretur a on a.kode=b.ReasonCode where dj.kodenota='".$row["Faktur"]."' and dj.brg='".$row["Brg"]."' and b.Operator IS NOT NULL and b.Jml>0 and b.KodeTO IS NULL and b.StatusT=1";
                        if($db->executeDB($sql2)){
                            //Update DetailJual
                            $sql3 = "update detailjual set jml=Jml-".$row["Jml"]." where Kodenota='".$row["Faktur"]."' and Brg='".$row["Brg"]."' and Jml>0 and Jml>=".$row["Jml"]." ";
                            if($db->executeDB($sql3)){
                                $sql4 = "update bcp_dfatolakan set KodeTO='".$maxKode."' where Faktur='".$row["Faktur"]."' and Brg='".$row["Brg"]."'";
                                $db->executeDB($sql4);
                                $rs[]=array("KodeTO"=>$maxKode,"Status"=>"1");
                            }else{
                                $sql5 = "select Faktur,Brg,Jml from bcp_dfatolakan where Operator IS NOT NULL and KodeTO IS NOT NULL and StatusT=1 and Jml>0 and Faktur=".$row["Faktur"];
                                $result3=$db->queryDB($sql5);
                                if($result3["jumdata"]>0){
                                    while ($row2 = mssql_fetch_assoc($result3["result"])) {
                                        $sql6 = "update detailjual set jml=jml+".$row2["Jml"]." where Kodenota='".$row2["Faktur"]."' and Brg='".$row2["Brg"]."'";
                                        $db->executeDB($sql6);
                                        $rs[]=array("KodeTO"=>"Gagal3 - Input Tolakan","Status"=>"0");
                                    }
                                }else{
                                    $rs[]=array("KodeTO"=>"Gagal2 - Input Tolakan","Status"=>"0");
                                }
                                $sql3 = "update bcp_dfatolakan set KodeTO=NULL where Faktur='".$row["Faktur"]."'";
                                $db->executeDB($sql3);
                                $sql3 = "delete from bcp_tolakandms where kodenota='".$maxKode."' and kodeinvoice='".$Sopir."'";
                                $db->executeDB($sql3);
                                break;
                            }
                        } else{
                            $sql5 = "select Faktur,Brg,Jml from bcp_dfatolakan where Operator IS NOT NULL and KodeTO IS NOT NULL and StatusT=1 and Jml>0 and Faktur=".$row["Faktur"];
                            $result3=$db->queryDB($sql5);
                            if($result3["jumdata"]>0){
                                while ($row2 = mssql_fetch_assoc($result3["result"])) {
                                    $sql6 = "update detailjual set jml=jml+".$row2["Jml"]." where Kodenota='".$row2["Faktur"]."' and Brg='".$row2["Brg"]."'";
                                    $db->executeDB($sql6);
                                    $rs[]=array("KodeTO"=>"Gagal4 - Input Tolakan","Status"=>"0");
                                }
                            }else{
                                $rs[]=array("KodeTO"=>"Gagal5 - Input Tolakan","Status"=>"0");
                            }
                            $sql3 = "update bcp_dfatolakan set KodeTO=NULL where Faktur='".$row["Faktur"]."'";
                            $db->executeDB($sql3);
                            $sql3 = "delete from bcp_tolakandms where kodenota='".$maxKode."' and kodeinvoice='".$Sopir."'";
                            $db->executeDB($sql3);
                            break;
                        }
                    }
                }else{
                    $rs[]=array("KodeTO"=>"Gagal - Faktur Tidak Ada","Status"=>"0");
                }
            }
            else{
                $rs[]=array("KodeTO"=>"Gagal - Mendapatkan Kode Tolakan Terakhir","Status"=>"0");
            }
        }
        else{
            $rs[]=array("KodeTO"=>"Gagal - Koneksi Server Terputus","Status"=>"0");
        }
        print json_encode($rs);
        //print $maxKode;
    }

    if($_GET["act"]=="inputtolakantr"){
        $key = $_SESSION["cabangdfa"]."/TO/";
        $key = stripslashes($key);
        $maxKode = "";
        $rs=array();
        if($koneksi["status"]){
            $sql="select RIGHT(MAX(kodenota),6) as MaxNumber from bcp_tolakandms where LEFT(kodenota,9)='".$key."'";
            $result=$db->queryDB($sql);
            if($result["jumdata"]>0){
                while ($row = mssql_fetch_assoc($result["result"])) {
                    $resultcabang=$row["MaxNumber"];
                    $maxKode = $_SESSION["cabangdfa"]."/TO/".date("Y")."/".sprintf("%06s", $resultcabang+1);
                    //$rs[] = $row;
                }
                $sql = "select Faktur,Brg,Jml from bcp_dfatolakan where Operator IS NOT NULL and KodeTO IS NULL and StatusT=1 and Jml>0 and Faktur=".$Sopir;
                $result=$db->queryDB($sql);
                if($result["jumdata"]>0){
                    while ($row = mssql_fetch_assoc($result["result"])) {
                        $db->executeDB("BEGIN TRAN");
                        $sql2 = "insert into bcp_tolakandms(Kodenota,kodeinvoice,Brg,Jml,hrgsatuan,disc1,disc2,disc3,disc4,tgl,alasan,discvaluepost,reasoncode) select '".$maxKode."',dj.kodenota,dj.brg,b.jml,dj.hrgsatuan,dj.Disc1,dj.Disc2,dj.Disc3,dj.Disc4,CONVERT(VARCHAR(16),dateadd(day,datediff(day,0,getdate()),0),121),a.keterangan,dj.DiscValuePost,b.ReasonCode from detailjual dj join bcp_dfatolakan b on b.faktur=dj.kodenota and b.brg=dj.brg join alasanretur a on a.kode=b.ReasonCode where dj.kodenota='".$row["Faktur"]."' and dj.brg='".$row["Brg"]."' and b.Operator IS NOT NULL and b.Jml>0 and b.KodeTO IS NULL and b.StatusT=1";
                        if($db->executeDB($sql2)){
                            //Update DetailJual
                            $sql3 = "update detailjual set jml=Jml-".$row["Jml"]." where Kodenota='".$row["Faktur"]."' and Brg='".$row["Brg"]."' and Jml>0 and Jml>=".$row["Jml"]." ";
                            if($db->executeDB($sql3)){
                                $sql4 = "update bcp_dfatolakan set KodeTO='".$maxKode."' where Faktur='".$row["Faktur"]."' and Brg='".$row["Brg"]."'";
                                if($db->executeDB($sql4)){
                                    $rs[]=array("KodeTO"=>$maxKode,"Status"=>"1");
                                    $db->executeDB("COMMIT");
                                }else{
                                    $db->executeDB("ROLLBACK");
                                    $rs[]=array("KodeTO"=>"Gagal - Update DFA Tolakan","Status"=>"0");
                                    break;
                                }
                            }else{
                                $db->executeDB("ROLLBACK");
                                $rs[]=array("KodeTO"=>"Gagal - Update Detail Jual","Status"=>"0");
                                break;
                            }
                        } else{
                            $db->executeDB("ROLLBACK");
                            $rs[]=array("KodeTO"=>"Gagal - Input Tolakan DMS","Status"=>"0");
                            break;
                        }
                    }
                }else{
                    $rs[]=array("KodeTO"=>"Gagal - Faktur Tidak Ada","Status"=>"0");
                }
            }
            else{
                $rs[]=array("KodeTO"=>"Gagal - Mendapatkan Kode Tolakan Terakhir","Status"=>"0");
            }
        }
        else{
            $rs[]=array("KodeTO"=>"Gagal - Koneksi Server Terputus","Status"=>"0");
        }
        print json_encode($rs);
        //print $maxKode;
    }

    if($_GET["act"]=="inputtolakantrpdo"){
        $key = $_SESSION["cabangdfa"]."/TO/";
        $key = stripslashes($key);
        $maxKode = "";
        $rs=array();
        try{
            $link="192.168.31.4";$user="sa";$pass="8is4";$DB="BCP2";
            $koneksi = new PDO ("sqlsrv:server=$link;Database=$DB","$user","$pass");
            try{
                $sql="select RIGHT(MAX(kodenota),6) as MaxNumber from bcp_tolakandms where LEFT(kodenota,9)='".$key."'";
                $st=$koneksi->prepare($sql);
                $result = $st->execute();
                $result = $st->fetchAll();
                $koneksi->beginTransaction();
                if(count($result)>0){
                    foreach ($result as $row) {
                        $resultcabang=$row["MaxNumber"];
                        $maxKode = $_SESSION["cabangdfa"]."/TO/".date("Y")."/".sprintf("%06s", $resultcabang+1);
                    }
                    $sql = "select Faktur,Brg,Jml from bcp_dfatolakan where Operator IS NOT NULL and KodeTO IS NULL and StatusT=1 and Jml>0 and Faktur=".$Sopir;
                    $st=$koneksi->prepare($sql);
                    $result = $st->execute();
                    $result=$st->fetchAll();
                    if(count($result)>0){
                        foreach ($result as $row) {
                            $sql2 = "insert into bcp_tolakandms(Kodenota,kodeinvoice,Brg,Jml,hrgsatuan,disc1,disc2,disc3,disc4,tgl,alasan,discvaluepost,reasoncode) select '".$maxKode."',dj.kodenota,dj.brg,b.jml,dj.hrgsatuan,dj.Disc1,dj.Disc2,dj.Disc3,dj.Disc4,CONVERT(VARCHAR(16),dateadd(day,datediff(day,0,getdate()),0),121),a.keterangan,dj.DiscValuePost,b.ReasonCode from detailjual dj join bcp_dfatolakan b on b.faktur=dj.kodenota and b.brg=dj.brg join alasanretur a on a.kode=b.ReasonCode where dj.kodenota='".$row["Faktur"]."' and dj.brg='".$row["Brg"]."' and b.Operator IS NOT NULL and b.Jml>0 and b.KodeTO IS NULL and b.StatusT=1";
                            $st2 = $koneksi->prepare($sql2);
                            $st2->execute();

                            $sql3 = "update detailjual set jml=Jml-".$row["Jml"]." where Kodenota='".$row["Faktur"]."' and Brg='".$row["Brg"]."' and Jml>0 and Jml>=".$row["Jml"]." ";
                            $st3 = $koneksi->prepare($sql3);
                            $st3->execute();

                            $sql4 = "update bcp_dfatolakan set KodeTO='".$maxKode."' where Faktur='".$row["Faktur"]."' and Brg='".$row["Brg"]."'";
                            $st4 = $koneksi->prepare($sql4);
                            $st4->execute();

                            $rs[]=array("KodeTO"=>$maxKode,"Status"=>"1");
                        }
                    }else{
                        $rs[]=array("KodeTO"=>"Gagal - Faktur Tidak Ada","Status"=>"0");
                    }
                    $koneksi->commit();
                }
                else{
                    $rs[]=array("KodeTO"=>"Gagal - Mendapatkan Kode Tolakan Terakhir","Status"=>"0");
                }
            }catch (Exception $e){
                $koneksi->rollBack();
                $rs[]=array("KodeTO"=>"Gagal - ".$e->getMessage(),"Status"=>"0");
            }
        }catch (PDOException $e) {
            $rs[]=array("KodeTO"=>"Gagal - Koneksi Server Terputus","Status"=>"0");
        }
        print json_encode($rs);
        //print $maxKode;
    }

    if($_GET["act"] == "updatekkpdvnotunai"){
        $Result = "Gagal Memperbarui Data";
        if($koneksi["status"]){
            $sql="update bcp_dfa set NC='-' where faktur in (".$Sopir.")";
            if($result=$db->executeDB($sql)){
                $Result = "Berhasil Memperbarui Data";
            }
            else{$Result = "Gagal Memperbarui Data.";}
        }
        else{$Result="Koneksi Server Terputus";}
        print $Result;
    }


    if($_GET["act"] == "listnotunai"){
        if($koneksi["status"]){
            //$sql="select distinct m.Tgl,b.Kodenota,b.Faktur,m.Sopir,c.Nama,b.Stempel,b.TandaTerima,b.StatusKirim from bcp_dfa b join masterdelivery m on m.kodenota=b.kodenota join collector c on c.Kode=m.Sopir where b.NC IS NULL  and LEFT(b.kodenota,5)='".$_SESSION["cabangdfa"]."' and m.sudahkembali=1 and (b.Stempel<>0 or b.TandaTerima<>0 or b.StatusKirim=0) ".$Sopir." order by c.Nama";
            $sql="select distinct m.Tgl,b.Kodenota,b.Faktur,m.Sopir,c.Nama,b.Stempel,b.TandaTerima,b.StatusKirim from bcp_dfa b join masterdelivery m on m.kodenota=b.kodenota join collector c on c.Kode=m.Sopir where b.NC IS NULL  and LEFT(b.kodenota,5)='".$_SESSION["cabangdfa"]."' and m.sudahkembali=1 and (b.Stempel<>0 or b.TandaTerima<>0 or b.StatusKirim=0 or b.StatusKirim=1) and b.kodenota not in (select kodenota from bcp_dfa where Tunai>0 or BG>0) and b.kodenota not in (select kodenota from bcp_dfadetailtransfer where Jml>0) ".$Sopir." order by c.Nama";
            $result=$db->queryDB($sql);
            if($result["jumdata"]>0){while ($row = mssql_fetch_assoc($result["result"])) {$resultcabang[]=$row;}}
            else{$resultcabang[]=array();}
        }
        else{$resultcabang[]=array();}
        $jTableResult = array();$jTableResult['Result'] = "OK";$jTableResult['Records'] = $resultcabang;
        print json_encode($jTableResult);
    }

    if($_GET["act"] == "listtolakan"){
        if($koneksi["status"]){
            $sql="select m.Tgl,b.Faktur,p.Perusahaan,COUNT(b.Brg) as SKU,CASE WHEN b.StatusT=1 THEN 'Valid' ELSE 'Tidak Valid' END as StatusT  from bcp_dfatolakan b join masterjual m on m.kodenota=b.faktur join pelanggan p on p.Kode=m.ShipTo where b.KodeTO IS NULL and b.Operator IS NOT NULL and LEFT(Faktur,5)='".$_SESSION["cabangdfa"]."' ".$Sopir."   group by m.Tgl,b.Faktur,p.Perusahaan,b.StatusT ";
            $result=$db->queryDB($sql);
            if($result["jumdata"]>0){while ($row = mssql_fetch_assoc($result["result"])) {$resultcabang[]=$row;}}
            else{$resultcabang[]=array();}
        }
        else{$resultcabang[]=array();}
        $jTableResult = array();$jTableResult['Result'] = "OK";$jTableResult['Records'] = $resultcabang;
        print json_encode($jTableResult);
    }

    if($_GET["act"] == "detailbrgtolakan"){
        if($koneksi["status"]){
            $sql="select b.Faktur,b.Brg,br.Keterangan,b.Jml,a.Keterangan as Alasan,b.ReasonCode,CASE WHEN b.StatusT=1 THEN 'true' ELSE 'false' END as StatusT from bcp_dfatolakan b join barang br on br.kode=b.Brg join alasanretur a on a.kode=b.ReasonCode where b.faktur=".$Sopir." and b.KodeTO IS NULL ";
            $result=$db->queryDB($sql);
            if($result["jumdata"]>0){while ($row = mssql_fetch_assoc($result["result"])) {$resultcabang[]=$row;}}
            else{$resultcabang[]=array();}
        }
        else{$resultcabang[]=array();}
        $jTableResult = array();$jTableResult['Result'] = "OK";$jTableResult['Records'] = $resultcabang;
        print json_encode($jTableResult);
    }

    if($_GET["act"] == "deletetolakan"){
        $jTableResult = array();
        if($koneksi["status"]){
            $sql="delete bcp_dfatolakan where faktur in (".$Sopir.") and Brg='".$_POST["Brg"]."'";
            if($result=$db->executeDB($sql)){
                $jTableResult['Result'] = "OK";
            }
            else{$jTableResult['Result'] = "ERROR";}
        }
        else{$jTableResult['Result'] = "ERROR";}
        print json_encode($jTableResult);
    }

    if($_GET["act"] == "getnamabanktransfer" ){
        if($koneksi["status"]){
            $sql = "select * from (select m.Nomor as [Value],m.Nomor+' - '+m.Keterangan as DisplayText from kategori k join masterperkiraan m on m.Nomor like k.Bank where k.cabang='".$_SESSION["cabangdfa"]."') a union all select distinct [Value]='',ISNULL(KodeBank,'') as DisplayText from bcp_dfadetailtransfer where kodenota like '".$_SESSION["cabangdfa"]."%'";
            $result=$db->queryDB($sql);
            if($result["jumdata"]>0){while ($row = mssql_fetch_assoc($result["result"])) {$resultcabang[]=$row;}}
            else{$resultcabang[]=array();}
        }
        else{$resultcabang[]=array("Nomor"=>"","Bank"=>"Tidak Ada");}
        $jTableResult = array();
        $jTableResult['Result'] = "OK";$jTableResult['Options'] = $resultcabang;
        print json_encode($jTableResult);
    }

    if($_GET["act"] == "getnamabank" ){
        if($koneksi["status"]){
            $sql = "select distinct * from (select distinct (LTRIM(Bank)) as DisplayText,(LTRIM(Bank)) as [Value] from BCP_DFARekapanKKPDV where Tipe='CekBG' union all select distinct (LTRIM(Bank)) as DisplayText,(LTRIM(Bank)) as [Value] from detailcollectorcekbg) a order by DisplayText";
            $result=$db->queryDB($sql);
            if($result["jumdata"]>0){while ($row = mssql_fetch_assoc($result["result"])) {$resultcabang[]=$row;}}
            else{$resultcabang[]=array();}
        }
        else{$resultcabang[]=array("Bank"=>"Tidak Ada");}
        $jTableResult = array();
        $jTableResult['Result'] = "OK";$jTableResult['Options'] = $resultcabang;
        print json_encode($jTableResult);
    }

    if($_GET["act"] == "getreasoncode" ){
        if($koneksi["status"]){
            $sql = "select Kode as [Value],Kode+' - '+Keterangan as DisplayText from alasanretur where kode like 'A%' and aktif=1 order by Kode";
            $result=$db->queryDB($sql);
            if($result["jumdata"]>0){while ($row = mssql_fetch_assoc($result["result"])) {$resultcabang[]=$row;}}
            else{$resultcabang[]=array();}
        }
        else{$resultcabang[]=array("Bank"=>"Tidak Ada");}
        $jTableResult = array();
        $jTableResult['Result'] = "OK";$jTableResult['Options'] = $resultcabang;
        print json_encode($jTableResult);
    }

    if($_GET["act"] == "getsku" ){
        if($koneksi["status"]){
            $sql = "select d.Brg as [Value],d.Brg+' - '+b.Keterangan as DisplayText from detailjual d join barang b on b.kode=d.Brg where kodenota=".$Sopir." and brg not in (select brg from bcp_dfatolakan where faktur=".$Sopir.")";
            $result=$db->queryDB($sql);
            if($result["jumdata"]>0){while ($row = mssql_fetch_assoc($result["result"])) {$resultcabang[]=$row;}}
            else{$resultcabang[]=array();}
        }
        else{$resultcabang[]=array("Bank"=>"Tidak Ada");}
        $jTableResult = array();
        $jTableResult['Result'] = "OK";$jTableResult['Options'] = $resultcabang;
        print json_encode($jTableResult);
    }

    if($_GET["act"] == "gawejejak" ){
        $_SESSION["cabangdfa"]= $Sopir;
    }

    if($_GET["act"] == "getnamacabang" ){
        if($koneksi["status"]){
            $sql = "select NamaCabang from kategori where Cabang='".$Sopir."'";
            $result=$db->queryDB($sql);
            if($result["jumdata"]>0){while ($row = mssql_fetch_assoc($result["result"])) {$resultcabang[]=$row;}}
            else{$resultcabang[]=array();}
        }
        else{$resultcabang[]=array("NamaCabang"=>"Tidak Ada");}
        print json_encode($resultcabang);
    }
    if($_GET["act"] == "generatenc"){
        $KodeSopir=$_GET["g"];$NamaSopir=$_GET["h"];
        if($koneksi["status"]){
            $key = $_SESSION["cabangdfa"]."/NC/";
            $key = stripslashes($key);
            $sql= "select RIGHT(MAX(kodenota),6) as KodeNota from mastercollector where LEFT(Kodenota,9)='".$key."'";
            $result=$db->queryDB($sql);
            if($result["jumdata"]>0){
                while ($row = mssql_fetch_assoc($result["result"])) {
                    $resultcabang =$row["KodeNota"];
                    $resultcabang = $_SESSION["cabangdfa"]."/NC/".date("Y")."/".sprintf("%06s", $resultcabang+1);
                    $sql = "INSERT INTO mastercollector(KodeNota,Tgl,Collector,MataUang,Keterangan,IsKanvas,Operator,CreateBy,EditBy) VALUES('".$resultcabang."','".date("Ymd")."','".$KodeSopir."','IDR','".$NamaSopir."',0,'".$_SESSION["kode"]."','".$_SESSION["kode"]."','".$_SESSION["kode"]."')";
                    if($db->executeDB($sql)){
                        // Insert Tunai
                        $sql = "select Kodenota,sum(Jml) as Tunai from BCP_DFARekapanKKPDV where Jml>0  and Kodenota in (".$Sopir.") and Tipe='Tunai' group by Kodenota";
                        $result=$db->queryDB($sql);
                        if($result["jumdata"]>0){
                            while($row = mssql_fetch_assoc($result["result"])){
                                $sql = "insert into detailcollectortunai(KodeNota,NP,Jml,Terpenuhi) values('".$resultcabang."','".$row["Kodenota"]."',".$row["Tunai"].",0)";
                                if($db->executeDB($sql)){
                                    $sql = "update bcp_dfa set NC='".$resultcabang."' where Kodenota in (".$Sopir.")";
                                    if($db->executeDB($sql)){
                                        $hasil = "Data Berhasil Di Simpan NC :".$resultcabang;
                                    }else{
                                        $hasil = "Gagal Update Data Status";
                                        $sql = "delete from mastercollector where Kodenota='".$resultcabang."'";
                                        $db->executeDB($sql);
                                        $sql = "update bcp_dfa set NC=NULL where Kodenota in (".$Sopir.")";
                                        $db->executeDB($sql);
                                    }
                                }else{
                                    $hasil = "Gagal Menambahkan Data Tunai";
                                    $sql = "delete from mastercollector where Kodenota='".$resultcabang."'";
                                    $db->executeDB($sql);
                                }
                            }
                        }else{
                            $hasil = "OK - 1";
                        }
                        // Insert Cek BG
                        $sql = "select Kodenota as NP,Bank,Nomor as NoCekBG,Tgl as TglJatuhTempo,Jml from BCP_DFARekapanKKPDV where Jml>0  and Kodenota in (".$Sopir.") and Tipe='CekBG' and StatusT=1";
                        $result=$db->queryDB($sql);
                        if($result["jumdata"]>0){
                            while($row = mssql_fetch_assoc($result["result"])){
                                $sql = "insert into detailcollectorcekbg(KodeNota,NP,Bank,NoCekBG,TglJatuhTempo,Jml,BGStatus,TglCair,SetorKeBank,Terpenuhi,TglSetor) values('".$resultcabang."','".$row["NP"]."','".$row["Bank"]."','".$row["NoCekBG"]."','".$row["TglJatuhTempo"]."',".$row["Jml"].",'BELUM',NULL,NULL,0,NULL)";
                                if($db->executeDB($sql)){
                                    $sql = "update bcp_dfa set NC='".$resultcabang."' where Kodenota in (".$Sopir.")";
                                    if($db->executeDB($sql)){
                                        $hasil = "Data Berhasil Di Simpan NC :".$resultcabang;
                                    }else{
                                        $hasil = "Gagal Update Data Status";
                                        $sql = "delete from mastercollector where Kodenota='".$resultcabang."'";
                                        $db->executeDB($sql);
                                        $sql = "update bcp_dfa set NC=NULL where Kodenota in (".$Sopir.")";
                                        $db->executeDB($sql);
                                    }
                                }else{
                                    $hasil = "Gagal Menambahkan Data Cek BG";
                                    $sql = "delete from mastercollector where Kodenota='".$resultcabang."'";
                                    $db->executeDB($sql);
                                }
                            }
                            // Insert Detail Transfer
                            $sql= "select Kodenota,Bank as KodeBank,Tgl as TglTransfer,SUM(Jml) as Jml from BCP_DFARekapanKKPDV where Jml>0  and Kodenota in (".$Sopir.") and Tipe='Transfer' and StatusT=1 group by Kodenota,Bank,Tgl";
                            $result=$db->queryDB($sql);
                            if($result["jumdata"]>0){
                                $NoUrut = 1;
                                while($row = mssql_fetch_assoc($result["result"])){
                                    $sql = "insert into detailcollectortransfer(KodeNota,NP,Bank,NoUrut,TglTransfer,Jml,Terpenuhi) values('".$resultcabang."','".$row["Kodenota"]."','".$row["KodeBank"]."','0".$NoUrut."','".$row["TglTransfer"]."',".$row["Jml"].",0)";
                                    if($db->executeDB($sql)){
                                        $sql = "update bcp_dfa set NC='".$resultcabang."' where Kodenota in (".$Sopir.")";
                                        if($db->executeDB($sql)){
                                            $hasil = "Data Berhasil Di Simpan NC :".$resultcabang;
                                        }else{
                                            $hasil = "Gagal Update Data Status";
                                            $sql = "delete from mastercollector where Kodenota='".$resultcabang."'";
                                            $db->executeDB($sql);
                                            $sql = "update bcp_dfa set NC=NULL where Kodenota in (".$Sopir.")";
                                            $db->executeDB($sql);
                                        }
                                    }else{
                                        $hasil = "Gagal Menambahkan Data Transfer - 1";
                                        $sql = "delete from mastercollector where Kodenota='".$resultcabang."'";
                                        $db->executeDB($sql);
                                        $sql = "update bcp_dfa set NC=NULL where Kodenota in (".$Sopir.")";
                                        $db->executeDB($sql);
                                    }
                                    $NoUrut++;
                                }
                            }

                        }else{
                            // Insert Detail Transfer
                            $sql= "select Kodenota,Bank as KodeBank,Tgl as TglTransfer,SUM(Jml) as Jml from BCP_DFARekapanKKPDV where Jml>0  and Kodenota in (".$Sopir.") and Tipe='Transfer'  and StatusT=1 group by Kodenota,Bank,Tgl";
                            $result=$db->queryDB($sql);
                            if($result["jumdata"]>0){
                                $NoUrut = 1;
                                while($row = mssql_fetch_assoc($result["result"])){
                                    $sql = "insert into detailcollectortransfer(KodeNota,NP,Bank,NoUrut,TglTransfer,Jml,Terpenuhi) values('".$resultcabang."','".$row["Kodenota"]."','".$row["KodeBank"]."','0".$NoUrut."','".$row["TglTransfer"]."',".$row["Jml"].",0)";
                                    if($db->executeDB($sql)){
                                        $sql = "update bcp_dfa set NC='".$resultcabang."' where Kodenota in (".$Sopir.")";
                                        if($db->executeDB($sql)){
                                            $hasil = "Data Berhasil Di Simpan NC :".$resultcabang;
                                        }else{
                                            $hasil = "Gagal Update Data Status";
                                            $sql = "delete from mastercollector where Kodenota='".$resultcabang."'";
                                            $db->executeDB($sql);
                                            $sql = "update bcp_dfa set NC=NULL where Kodenota in (".$Sopir.")";
                                            $db->executeDB($sql);
                                        }
                                    }else{
                                        $hasil = "Gagal Menambahkan Data Transfer - 2";
                                        $sql = "delete from mastercollector where Kodenota='".$resultcabang."'";
                                        $db->executeDB($sql);
                                        $sql = "update bcp_dfa set NC=NULL where Kodenota in (".$Sopir.")";
                                        $db->executeDB($sql);
                                    }
                                    $NoUrut++;
                                }
                            }
                        }

                    }else{
                        $hasil = "Gagal Input Data";
                    }
                }
            }else{
                $hasil = "Gagal - Mendapatkan Kode Terakhir";
            }
        }else{
            $hasil = "Gagal - Koneksi Server Terputus";
        }
        print $hasil;
    }

    if($_GET["act"] == "proseskkpdv"){
        if($koneksi["status"]){
            $sql = "delete from BCP_DFARekapanKKPDV where KodeNota in (".$Sopir.")";
            if($result=$db->executeDB($sql)){
                $sql="select KodeNota,Tipe,Bank,Nomor,Tgl,Jml from (select KodeNota,Tipe='Tunai',Bank='',Nomor='',Tgl=NULL,SUM(Tunai) as Jml from bcp_dfa where Kodenota in (".$Sopir.") group by Kodenota
                        union all
                        select KodeNota,Tipe='CekBG',Bank,NoCekBG as Nomor,TglJatuhTempo as Tgl,SUM(Jml) as Jml from bcp_dfadetailcekbg where BGStatus=1 and Jml>0 and  Kodenota in (".$Sopir.") group by KodeNota,Bank,NoCekBG,TglJatuhTempo
                        union all
                        select KodeNota,Tipe='Transfer',Bank='',Nomor=NULL,CONVERT(VARCHAR(16),dateadd(day,datediff(day,0,TglTransfer),0),121) as Tgl,SUM(Jml) as Jml from BCP_DFADetailTransfer where Jml>1 and Kodenota in (".$Sopir.") group by KodeNota,CONVERT(VARCHAR(16),dateadd(day,datediff(day,0,TglTransfer),0),121)) a";
                $result=$db->queryDB($sql);
                if($result["jumdata"]>0){
                    while ($row = mssql_fetch_assoc($result["result"])) {
                        if($row["Tipe"]=="Tunai"){
                            $sql2="insert into BCP_DFARekapanKKPDV(KodeNota,Tipe,Bank,Nomor,Tgl,Jml,StatusT) values ('".$row["KodeNota"]."','".$row["Tipe"]."',NULL,NULL,NULL,".$row["Jml"].",1)";
                        }else if($row["Tipe"]=="CekBG"){
                            $sql2="insert into BCP_DFARekapanKKPDV(KodeNota,Tipe,Bank,Nomor,Tgl,Jml,StatusT) values ('".$row["KodeNota"]."','".$row["Tipe"]."','".$row["Bank"]."','".$row["Nomor"]."',NULL,".$row["Jml"].",1)";
                        }else if($row["Tipe"]=="Transfer"){
                            $sql2="insert into BCP_DFARekapanKKPDV(KodeNota,Tipe,Bank,Nomor,Tgl,Jml,StatusT) values ('".$row["KodeNota"]."','".$row["Tipe"]."','".$row["Bank"]."',NULL,'".$row["Tgl"]."',".$row["Jml"].",1)";
                        }else{
                            $sql2="";
                        }
                        $result2=$db->executeDB($sql2);
                    }
                    $sql="select distinct COALESCE(a.Jml,0) as Tunai,COALESCE(b.Jml,0) as BG,COALESCE(c.Jml,0) as [Transfer] from BCP_DFARekapanKKPDV d left join  (select Kodenota,SUM(Jml) as Jml from BCP_DFARekapanKKPDV where tipe='Tunai' and Kodenota in (".$Sopir.") Group by Kodenota) a on a.Kodenota=d.Kodenota left join  (select Kodenota,SUM(Jml) as Jml from BCP_DFARekapanKKPDV where tipe='CekBG'  and Kodenota in (".$Sopir.") Group by Kodenota) b on b.Kodenota=d.Kodenota left join   (select Kodenota,SUM(Jml) as Jml from BCP_DFARekapanKKPDV where tipe='Transfer'  and Kodenota in (".$Sopir.") Group by Kodenota) c on c.Kodenota=d.Kodenota where d.Kodenota in (".$Sopir.")";
                    $result=$db->queryDB($sql);
                    if($result["jumdata"]>0){
                        while ($row = mssql_fetch_assoc($result["result"])) {
                            $resultcabang[]=$row;
                        }
                    }
                    else{
                        $resultcabang[]=array();
                    }
                }
                else{
                    $resultcabang[]=array();
                }
            }else{
                $resultcabang[]=array();
            }
        }
        else{$resultcabang[]=array();}
        $jTableResult = array();$jTableResult = $resultcabang;
        print json_encode($jTableResult);
    }

    if($_GET["act"] == "gettotaltransfervalid"){
        if($koneksi["status"]){
            $sql="select sum(Jml) as Transfer from BCP_DFARekapanKKPDV where Kodenota in (".$Sopir.") and StatusT='1' and Tipe='Transfer'";
            $result=$db->queryDB($sql);
            if($result["jumdata"]>0){while ($row = mssql_fetch_assoc($result["result"])) {$resultcabang[]=$row;}}
            else{$resultcabang[]=array();}
        }
        else{$resultcabang[]=array();}
        $jTableResult = array();$jTableResult = $resultcabang;
        print json_encode($jTableResult);
    }

    if($_GET["act"] == "gettotalcekbgvalid"){
        if($koneksi["status"]){
            $sql="select sum(Jml) as BG from BCP_DFARekapanKKPDV where Tipe='CekBG' and Kodenota in (".$Sopir.") and StatusT='1'";
            $result=$db->queryDB($sql);
            if($result["jumdata"]>0){while ($row = mssql_fetch_assoc($result["result"])) {$resultcabang[]=$row;}}
            else{$resultcabang[]=array();}
        }
        else{$resultcabang[]=array();}
        $jTableResult = array();$jTableResult = $resultcabang;
        print json_encode($jTableResult);
    }

    if($_GET["act"] == "cekvaliddatadetailtranfer"){
        if($koneksi["status"]){
            $sql="select TOP 1 count(Kodenota) Jml,Kodenota as Faktur from BCP_DFARekapanKKPDV where Tipe='Transfer' and (Bank='' or Tgl IS NULL) and  (Kodenota in (".$Sopir.") and StatusT='1') GROUP BY Kodenota";
            $result=$db->queryDB($sql);
            if($result["jumdata"]>0){
                while ($row = mssql_fetch_assoc($result["result"])) {
                    $resultcabang[]=$row;
                }
            }
            else{
                $sql = "select TOP 1 Jml=1,Faktur='yogixxaditya' from bcp_dfa where NC IS NOT NULL and kodenota in (".$Sopir.")";
                $result=$db->queryDB($sql);
                if($result["jumdata"]>0){
                    while ($row = mssql_fetch_assoc($result["result"])) {
                        $resultcabang[]=$row;
                    }
                }else{
                    //$sql = "select TOP 1 Jml=1,NoCekBG='yogixxaditya' from bcp_dfa where Tunai<=0 and BG<=0 and NC IS NULL and kodenota in (".$Sopir.")";
                    $sql = "select TOP 1 Jml=1,Faktur='yogixxxaditya' from BCP_DFARekapanKKPDV where kodenota in (".$Sopir.") having sum(Jml)<=0";
                    $result=$db->queryDB($sql);
                    if($result["jumdata"]>0){
                        while ($row = mssql_fetch_assoc($result["result"])) {
                            $resultcabang[]=$row;
                        }
                    }else{
                        $resultcabang[]=array("Jml"=>0,"Faktur"=>"");
                    }
                }
            }
        }
        else{$resultcabang[]=array();}
        $jTableResult = array();
        $jTableResult = $resultcabang;
        print json_encode($jTableResult);
    }

    if($_GET["act"] == "cekvaliddatacekbg"){
        if($koneksi["status"]){
            $sql="select TOP 1 count(Kodenota) Jml,Nomor as NoCekBG from BCP_DFARekapanKKPDV where Tipe='CekBG' and (Bank='' or Tgl IS NULL) and  (Kodenota in (".$Sopir.") and StatusT='1') GROUP BY Nomor";
            $result=$db->queryDB($sql);
            if($result["jumdata"]>0){
                while ($row = mssql_fetch_assoc($result["result"])) {
                    $resultcabang[]=$row;
                }
            }
            else{
                $sql = "select TOP 1 Jml=1,NoCekBG='yogixxaditya' from bcp_dfa where NC IS NOT NULL and kodenota in (".$Sopir.")";
                $result=$db->queryDB($sql);
                if($result["jumdata"]>0){
                    while ($row = mssql_fetch_assoc($result["result"])) {
                        $resultcabang[]=$row;
                    }
                }else{
                    $sql = "select TOP 1 Jml=1,NoCekBG='yogixxxaditya' from BCP_DFARekapanKKPDV where kodenota in (".$Sopir.") having sum(Jml)<=0";
                    $result=$db->queryDB($sql);
                    if($result["jumdata"]>0){
                        while ($row = mssql_fetch_assoc($result["result"])) {
                            $resultcabang[]=$row;
                        }
                    }else{
                    $resultcabang[]=array("Jml"=>0,"NoCekBG"=>"");
                    }
                }
            }
        }
        else{$resultcabang[]=array();}
        $jTableResult = array();
        $jTableResult = $resultcabang;
        print json_encode($jTableResult);
    }

    if($_GET["act"] == "getdetailtunai"){
        $results = "Tidak Ada";
        if($koneksi["status"]){
            $sql="select Kode,Kodenota,Jml as Tunai from bcp_dfaRekapanKKPDV where Tipe='Tunai' and Kodenota in (".$Sopir.")";
            $result=$db->queryDB($sql);
            if($result["jumdata"]>0){
                while ($row = mssql_fetch_assoc($result["result"]))
                {$resultcabang[]=$row;}
            }
            else{$resultcabang[]=array();}
        }
        else{$resultcabang[]=array();}
        $jTableResult = array();
        $jTableResult['Result'] = "OK";$jTableResult['Records'] = $resultcabang;
        print json_encode($jTableResult);
    }

    if($_GET["act"] == "cekbrgtolakan"){
        if($koneksi["status"]){
            $sql="select count(t.Faktur) as jml from BCP_DFATolakan t join detailjual dj on dj.kodenota=t.faktur and dj.brg=t.brg where t.KodeTO IS NULL and t.StatusT=1 and t.faktur=".$Sopir." and dj.jml<t.jml";
            $result=$db->queryDB($sql);
            if($result["jumdata"]>0){
                while ($row = mssql_fetch_assoc($result["result"]))
                {
                    $resultcabang[]=$row;
                }
            }
            else{$resultcabang[]=array("jml"=>1);}
        }
        else{$resultcabang[]=array("jml"=>1);}
        print json_encode($resultcabang);
    }

    if($_GET["act"] == "updatedetailtunai"){
        $jTableResult = array();
        if($koneksi["status"]){
            $sql="update bcp_dfaRekapanKKPDV set Jml=".$_POST["Tunai"]." where KodeNota='".$_POST["Kodenota"]."' and Kode=".$_POST["Kode"]." and Tipe='Tunai'";
            if($result=$db->executeDB($sql)){$jTableResult['Result'] = "OK";}
            else{$resultcabang[]=array();}
        }
        else{$resultcabang[]=array();}
        print json_encode($jTableResult);
    }

    if($_GET["act"] == "updatedetailtolakan"){
        if($_POST["StatusT"]==true){
            $_POST["StatusT"]=1;
        }else{
            $_POST["StatusT"]=0;
        }
        $jTableResult = array();
        if($koneksi["status"]){
            $sql="update bcp_dfaTolakan set TglEntry=getdate(),Jml=".$_POST["Jml"].",ReasonCode='".$_POST["ReasonCode"]."',StatusT=".$_POST["StatusT"]." where Faktur='".$_POST["Faktur"]."' and Brg='".$_POST["Brg"]."' ";
            if($result=$db->executeDB($sql)){
                $jTableResult['Result'] = "OK";
            }
            else{
                $jTableResult['Result'] = "ERROR";
            }
        }
        else{
            $jTableResult['Result'] = "ERROR";
        }
        print json_encode($jTableResult);
    }

    if($_GET["act"] == "tambahbrgtolakan"){
        $jTableResult = array();
        if($koneksi["status"]){
            $sql="insert into bcp_dfatolakan(Tgl,Faktur,Brg,Jml,CreateBy,ReasonCode,KodeTO,Operator,StatusT) values ('".date("Y-m-d")."',".$Sopir.",'".$_POST["Brg"]."',".$_POST["Jml"].",'".$_SESSION["usernamedfa"]."','".$_POST["ReasonCode"]."',NULL,'".$_SESSION["usernamedfa"]."',1)";
            if($result=$db->executeDB($sql)){
                $jTableResult['Result'] = "OK";
                $sql="select Brg,Jml,ReasonCode from bcp_dfatolakan where Faktur=".$Sopir." and brg='".$_POST["Brg"]."'";
                $result=$db->queryDB($sql);
                if($result["jumdata"]>0){
                    while ($row = mssql_fetch_assoc($result["result"]))
                    {
                        $resultcabang["Jml"] = $row["Jml"];
                        $resultcabang["Brg"] = $row["Brg"];
                        $resultcabang["ReasonCode"] = $row["ReasonCode"];
                    }
                }
                $jTableResult['Record'] = $resultcabang;

            }
            else{
                $jTableResult['Result'] = "ERROR";
                $resultcabang[] = array();
            }
        }
        else{
            $jTableResult['Result'] = "ERROR";
            $resultcabang[] = array();
            $jTableResult['Record'] = $resultcabang;
        }
        print json_encode($jTableResult);
    }

    if($_GET["act"] == "getdetailtransfer"){
        $results = "Tidak Ada";
        if($koneksi["status"]){
            $sql="select Kode,KodeNota,Jml,Tgl as TglTransfer,ISNULL(Bank,'') as KodeBank,CASE WHEN StatusT='1' THEN 'true' ELSE 'false' END as TransferStatus from bcp_dfaRekapanKKPDV where jml>0 and Tipe='Transfer' and Kodenota in (".$Sopir.")";
            $result=$db->queryDB($sql);
            if($result["jumdata"]>0){
                while ($row = mssql_fetch_assoc($result["result"]))
                {
                    $resultcabang[] = $row;
                }
            }
            else{$resultcabang[]=array();}
        }
        else{$resultcabang[]=array();}
        $jTableResult = array();
        $jTableResult['Result'] = "OK";
        $jTableResult['Records'] = $resultcabang;
        print json_encode($jTableResult);
    }

    if($_GET["act"] == "updatetransfer"){
        $jTableResult = array();
        if($koneksi["status"]){
            if(empty($_POST["TransferStatus"])){
                $_POST["TransferStatus"] = 0;
            }else{
                $_POST["TransferStatus"] = 1;
            }
            $sql="update bcp_dfaRekapanKKPDV set Tgl='".$_POST["TglTransfer"]."',Jml=".$_POST["Jml"].",StatusT=".$_POST["TransferStatus"].",Bank='".$_POST["KodeBank"]."' where KodeNota='".$_POST["KodeNota"]."' and Kode=".$_POST["Kode"]." and Tipe='Transfer'";
            if($result=$db->executeDB($sql)){$jTableResult['Result'] = "OK";}
            else{$resultcabang[]=array();}
        }
        else{$resultcabang[]=array();}
        print json_encode($jTableResult);
    }

    if($_GET["act"] == "getdetailcekbg"){
        $results = "Tidak Ada";
        if($koneksi["status"]){
            $sql="select Kode,KodeNota,Jml,Tgl as TglJatuhTempo,ISNULL(Nomor,'') as NoCekBG,ISNULL(Bank,'') as Bank,CASE WHEN StatusT='1' THEN 'true' ELSE 'false' END as BGStatus from bcp_dfaRekapanKKPDV where jml>0 and Tipe='CekBG' and Kodenota in (".$Sopir.")";
            $result=$db->queryDB($sql);
            if($result["jumdata"]>0){
                while ($row = mssql_fetch_assoc($result["result"]))
                {
                    $resultcabang[] = $row;
                }
            }
            else{$resultcabang[]=array();}
        }
        else{$resultcabang[]=array();}
        $jTableResult = array();
        $jTableResult['Result'] = "OK";
        $jTableResult['Records'] = $resultcabang;
        print json_encode($jTableResult);
    }

    if($_GET["act"] == "updatecekbg"){
        $jTableResult = array();
        if($koneksi["status"]){
            if(empty($_POST["BGStatus"])){
                $_POST["BGStatus"] = '0';
            }else{
                $_POST["BGStatus"] = '1';
            }
            $sql="update BCP_DFARekapanKKPDV set Tgl='".$_POST["TglJatuhTempo"]."',Jml=".$_POST["Jml"].",StatusT=".$_POST["BGStatus"].",Bank='".$_POST["Bank"]."',Nomor='".$_POST["NoCekBG"]."' where KodeNota='".$_POST["KodeNota"]."' and Kode=".$_POST["Kode"]." and Tipe='CekBG'";
            if($result=$db->executeDB($sql)){$jTableResult['Result'] = "OK";}
            else{$resultcabang[]=array();}
        }
        else{$resultcabang[]=array();}
        print json_encode($jTableResult);
    }

     if($_GET["act"] == "getbgdetail"){
         $results = "Tidak Ada";
         if($koneksi["status"]){
             $sql="select Faktur,BGDetail,NoBG='',Nominal='' from bcp_dfa where Faktur='".$Sopir."'";
             $result=$db->queryDB($sql);
             if($result["jumdata"]>0){
                 while ($row = mssql_fetch_assoc($result["result"]))
                 {
                     $fin = explode("#",$row["BGDetail"]);
                     if(count($fin)>=1){
                         for($i=0;$i<count($fin);$i++){
                             $a=explode("&",$fin[$i]);$no = $a[0];$nominal = $a[1];
                             $resultcabang[]=array("Faktur"=>$row["Faktur"],"BGDetail"=>$row["BGDetail"],"NoBG"=>$no,"Nominal"=>$nominal);
                         }
                     }
                 }
             }
             else{$resultcabang[]=array();}
         }
         else{
             $resultcabang[]=array();
         }
         $jTableResult = array();
         $jTableResult['Result'] = "OK";
         $jTableResult['Records'] = $resultcabang;
         print json_encode($jTableResult);
     }

    if($_GET["act"] == "getsopir"){
        $results = "Tidak Ada";
        if($koneksi["status"]){
            $sql="select Nama from collector where kode='".$Sopir."'";
            $result=$db->queryDB($sql);
            if($result["jumdata"]>0){while ($row = mssql_fetch_assoc($result["result"])) {$results=$row["Nama"];}}
            else{$results= "Tidak Ada";}
        }
        else{$results="Tidak Ada";}
        print $results;
    }

    if($_GET["act"] == "getdetailsopir"){
        if($koneksi["status"]){
            $sql="select distinct m.Sopir,c.Nama from bcp_dfa b join masterdelivery m on m.kodenota=b.kodenota join collector c on c.Kode=m.Sopir where b.NC IS NULL  and LEFT(b.kodenota,5)='".$_SESSION["cabangdfa"]."' and m.sudahkembali=1 order by c.Nama ";
            $result=$db->queryDB($sql);
            if($result["jumdata"]>0){while ($row = mssql_fetch_assoc($result["result"])) {$resultcabang[]=$row;}}
            else{$resultcabang[]=array();}
        }
        else{$resultcabang[]=array();}
        $jTableResult = array();$jTableResult = $resultcabang;
        print json_encode($jTableResult);
    }

    if($_GET["act"] == "getdetailsopirnotunai"){
        if($koneksi["status"]){
            $sql="select distinct m.Sopir,c.Nama from bcp_dfa b join masterdelivery m on m.kodenota=b.kodenota join collector c on c.Kode=m.Sopir where b.NC IS NULL and LEFT(b.kodenota,5)='".$_SESSION["cabangdfa"]."' and m.sudahkembali=1 and (b.Stempel=1 or b.TandaTerima=1 or b.StatusKirim=0) order by c.Nama ";
            $result=$db->queryDB($sql);
            if($result["jumdata"]>0){while ($row = mssql_fetch_assoc($result["result"])) {$resultcabang[]=$row;}}
            else{$resultcabang[]=array();}
        }
        else{$resultcabang[]=array();}
        $jTableResult = array();$jTableResult = $resultcabang;
        print json_encode($jTableResult);
    }

    if($_GET["act"] == "getdetailcheckertolakan"){
        if($koneksi["status"]){
            $sql="select distinct b.Operator as Sopir,b.Operator as Nama from bcp_dfatolakan b where LEFT(b.faktur,5)='".$_SESSION["cabangdfa"]."' and b.Operator IS NOT NULL and b.KodeTO IS NULL order by b.Operator";
            $result=$db->queryDB($sql);
            if($result["jumdata"]>0){while ($row = mssql_fetch_assoc($result["result"])) {$resultcabang[]=$row;}}
            else{$resultcabang[]=array();}
        }
        else{$resultcabang[]=array();}
        $jTableResult = array();$jTableResult = $resultcabang;
        print json_encode($jTableResult);
    }

    if($_GET["act"] == "getnamabanks" ){
        if($koneksi["status"]){
            $sql = "select distinct * from (select distinct (LTRIM(Bank)) as DisplayText,(LTRIM(Bank)) as [Value] from bcp_dfadetailcekbg union all select distinct (LTRIM(Bank)) as DisplayText,(LTRIM(Bank)) as [Value] from detailcollectorcekbg) a order by DisplayText";
            $result=$db->queryDB($sql);
            if($result["jumdata"]>0){while ($row = mssql_fetch_assoc($result["result"])) {$resultcabang[]=$row;}}
            else{$resultcabang[]=array();}
        }
        else{$resultcabang[]=array("Bank"=>"Tidak Ada");}
        $jTableResult = array();
        $jTableResult= $resultcabang;
        print json_encode($jTableResult);
    }

    if($_GET["act"] == "list"){
        if($koneksi["status"]){
            $sql="select CONVERT(char(10), b.CreateDate,126) as Tgl,b.Kodenota,SUM(b.Tunai) Tunai,SUM(b.BG) BG,SUM(COALESCE(t.Jml,0)) Transfer from bcp_dfa b join masterdelivery m on m.kodenota=b.kodenota left join bcp_dfadetailtransfer t on t.kodenota=b.kodenota and t.faktur=b.faktur where m.sopir='".$Sopir."' and  NC IS NULL and m.sudahkembali=1 and LEFT(b.kodenota,5)='".$_SESSION["cabangdfa"]."'  group by CONVERT(char(10), b.CreateDate,126),b.Kodenota";
            $result=$db->queryDB($sql);
            if($result["jumdata"]>0){while ($row = mssql_fetch_assoc($result["result"])) {$resultcabang[]=$row;}}
            else{$resultcabang[]=array();}
        }
        else{$resultcabang[]=array();}
        $jTableResult = array();$jTableResult['Result'] = "OK";$jTableResult['Records'] = $resultcabang;
        print json_encode($jTableResult);
    }
    //Creating a new record (createAction)
    else if($_GET["act"] == "create")
    {
        //Insert record into database
        //$result = mysql_query("INSERT INTO people(Name, Age, RecordDate) VALUES('" . $_POST["Name"] . "', " . $_POST["Age"] . ",now());");

        if($koneksi["status"]){
            $sql="select Kodenota,Faktur,Tunai,BG from BCP_DFA";
            $result=$db->queryDB($sql);
            if($result["jumdata"]>0){while ($row = mssql_fetch_assoc($result["result"])) {$resultcabang[]=$row;}}
            else{$resultcabang[]=array("Kodenota"=>"Data Kosong","Faktur"=>"0","Tunai"=>"0","BG"=>"0");}
        }
        else{$resultcabang[]=array("Kodenota"=>"Data Kosong","Faktur"=>"0","Tunai"=>"0","BG"=>"0");}

        //Return result to jTable
        $jTableResult = array();
        $jTableResult['Result'] = "OK";
        $jTableResult['Records'] = $resultcabang;
        print json_encode($jTableResult);
    }
    //Updating a record (updateAction)
    else if($_GET["act"] == "update")
    {
        //Update record in database
        //$result = mysql_query("UPDATE people SET Name = '" . $_POST["Name"] . "', Age = " . $_POST["Age"] . " WHERE PersonId = " . $_POST["PersonId"] . ";");

        //Return result to jTable
        $jTableResult = array();$jTableResult['Result'] = "OK";
        print json_encode($jTableResult);
    }
    //Deleting a record (deleteAction)
    else if($_GET["act"] == "delete")
    {
        //Delete from database
        //$result = mysql_query("DELETE FROM people WHERE PersonId = " . $_POST["PersonId"] . ";");

        //Return result to jTable
        $jTableResult = array();
        $jTableResult['Result'] = "OK";
        print json_encode($jTableResult);
    }

}catch(Exception $ex)
{
    //Return error message
    $jTableResult = array();
    $jTableResult['Result'] = "ERROR";
    $jTableResult['Message'] = $ex->getMessage();
    print json_encode($jTableResult);
}


