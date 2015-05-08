<?php
include "../setting/include.php";$cabang="00";

$db=new DB();$resultccabang=array();$koneksi=$db->connectDB($cabang);$Sopir=$_GET["sopir"];$today = date("Ymd");

if($koneksi["status"]){
    $sql="select  m.Kodenota,d.Faktur,mj.NoPicklist,m.Tgl,mj.TotalBayar,dj.Brg,REPLACE(ISNULL(b.Hint,'Kosong'), '''', ' ') as Hint,REPLACE(b.Keterangan, '''', ' ') as Keterangan,m.Sopir,mj.ShipTo,REPLACE(p.Perusahaan, '''', ' ') as Perusahaan,p.Alamat,CAST((dj.Jml*dj.Rasio) as INT) as Jml,dbo.KonversiSatuanToText(b.Kode,dj.Jml*dj.Rasio) as JmlCRT,CAST(dj.Rasio as INT) as Rasio,dj.HrgSatuan/dj.Rasio as HrgSatuan,dj.Disc/dj.Rasio as DiscRp,mj.TglPickList,CAST(a.Rasio as INT) as RasioMax from
	  masterdelivery m
      join detaildelivery d on d.kodenota=m.kodenota
      join masterjual mj on mj.kodenota=d.Faktur
      join detailjual dj on dj.kodenota=mj.kodenota
      join pelanggan p on p.kode=mj.ShipTo
      join barang b on b.kode=dj.brg
      join (select Brg,Max(Rasio) as Rasio from satuan where SatuanAktif=1 group by brg) a on a.Brg=b.Kode
      where
      sopir='".$Sopir."' and m.SudahKembali=0  and mj.TotalBayar>0  order by m.kodenota";
    $result=$db->queryDB($sql);
    if($result["jumdata"]>0){while ($row = mssql_fetch_assoc($result["result"])) {$resultcabang[]=$row;}}
    else{$resultcabang[]=array("Tgl"=>"1999-11-19 00:00:00","Kodenota"=>"Data Kosong","Sopir"=>"0","Faktur"=>"0","ShipTo"=>"0","Perusahaan"=>"0","Alamat"=>"0","Brg"=>"0","Hint"=>"0","Jml"=>"0","Rasio"=>"0","HrgSatuan"=>"0");}
}
else{$resultcabang[]=array("Tgl"=>"1999-11-19 00:00:00","Kodenota"=>"Koneksi DB Terputus","Sopir"=>"0","Faktur"=>"0","ShipTo"=>"0","Perusahaan"=>"0","Alamat"=>"0","Brg"=>"0","Hint"=>"0","Jml"=>"0","Rasio"=>"0","HrgSatuan"=>"0");}
echo json_encode($resultcabang);