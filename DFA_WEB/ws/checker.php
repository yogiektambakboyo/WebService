<?php
include "../setting/include.php";$cabang="00";

$db=new DB();$resultccabang=array();$koneksi=$db->connectDB($cabang);$Sopir=$_GET["sopir"];

if(($koneksi["status"])&&($Sopir=="3088")){
    $sql="select CAST(dj.Jml as int) as Jml,t.Tgl,t.Faktur,t.Brg,t.Jml as JmlPCS,t.CreateBy,t.ReasonCode,c.Nama,p.Kode,p.Perusahaan,REPLACE(b.Keterangan,'''', '') as Keterangan,CAST(a.Rasio as INT) as RasioMax from
BCP_DFATolakan t
join collector c on c.Kode=t.CreateBy
join masterjual mj on mj.kodenota=t.faktur
join pelanggan p on p.kode=mj.shipto
join barang b on b.kode=t.brg
join (select Brg,Max(Rasio) as Rasio from satuan where SatuanAktif=1 group by brg) a on a.Brg=b.kode
join detailjual dj on dj.kodenota=mj.kodenota and dj.brg=t.brg
where KodeTO is NULL and t.Operator is NULL

union all

select CAST(dj.Jml as int) as Jml,mj.Tgl,dj.Kodenota,dj.Brg,'0' as JmlPCS,'' as CreateBy,'' as ReasonCode,'' as Nama,p.Kode,p.Perusahaan,REPLACE(b.Keterangan,'''', '') as Keterangan,CAST(a.Rasio as INT) as RasioMax from
detailjual dj
join masterjual mj on mj.kodenota=dj.kodenota
join pelanggan p on p.kode=mj.shipto
join barang b on b.kode=dj.brg
join (select distinct Faktur from BCP_DFATolakan  where KodeTO is NULL and Operator is NULL) t on t.faktur=dj.kodenota
join (select Brg,Max(Rasio) as Rasio from satuan where SatuanAktif=1 group by brg) a on a.Brg=b.kode
where dj.brg not in (select Brg from BCP_DFATolakan)";
    $result=$db->queryDB($sql);
    if($result["jumdata"]>0){while ($row = mssql_fetch_assoc($result["result"])) {$resultcabang[]=$row;}}
    else{$resultcabang[]=array("Tgl"=>"1999-11-19 00:00:00","Faktur"=>"0","Brg"=>"0","JmlPCS"=>"0","Jml"=>"0","CreateBy"=>"","ReasonCode"=>"","Nama"=>"","Perusahaan"=>"","Kode"=>"","Keterangan"=>"");}
}
else{$resultcabang[]=array("Tgl"=>"1999-11-19 00:00:00","Faktur"=>"Koneksi Server Putus","Brg"=>"0","JmlPCS"=>"0","Jml"=>"0","CreateBy"=>"","ReasonCode"=>"","Nama"=>"","Perusahaan"=>"","Kode"=>"","Keterangan"=>"");}
echo json_encode(array('deliverydata'=>$resultcabang));