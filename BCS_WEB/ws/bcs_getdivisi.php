<?php
    include "../setting/include.php";$cabang=$_GET["cabang"];

    $db=new DB();$resultccabang=array();$koneksi=$db->connectDB($cabang);

    if($koneksi["status"]){
        $sql="select Divisi as Kode,NamaDivisi as Keterangan from kategori where tgltransaksi>'2015-01-01'";
        $result=$db->queryDB($sql);
        if($result["jumdata"]>0){while ($row = mssql_fetch_assoc($result["result"])) {$resultcabang[]=$row;}}
        else{$resultcabang[]=array("Kode"=>"0","Keterangan"=>"Data Kosong");}
    }
    else{$resultcabang[]=array("Kode"=>"0","Keterangan"=>"Data Kosong");}
    echo json_encode($resultcabang);