<?php
include "../setting/include.php";$cabang="01";

$db=new DB();$resultccabang=array();$koneksi=$db->connectDB($cabang);$Sopir=$_GET["sopir"];$today = date("Ymd");

if($koneksi["status"]){
$sql="select m.Kodenota,d.Faktur,m.Tgl,mj.TotalBayar,dj.Brg,ISNULL(b.Hint,'Kosong') as Hint,m.Sopir,mj.ShipTo,p.Perusahaan,p.Alamat,CAST((dj.Jml*dj.Rasio) as INT) as Jml,CAST(dj.Rasio as INT) as Rasio,dj.HrgSatuan/dj.Rasio as HrgSatuan,dj.Disc/dj.Rasio as DiscRp from
	  masterdelivery m
      join detaildelivery d on d.kodenota=m.kodenota
      join masterjual mj on mj.kodenota=d.Faktur
      join detailjual dj on dj.kodenota=mj.kodenota
      join pelanggan p on p.kode=mj.ShipTo
      join barang b on b.kode=dj.brg
      where
      sopir='".$Sopir."' and m.SudahKembali=0";
    $result=$db->queryDB($sql);
    if($result["jumdata"]>0){while ($row = mssql_fetch_assoc($result["result"])) {$resultcabang[]=$row;}}
    else{$resultcabang[]=array("Tgl"=>"1999-11-19 00:00:00","Kodenota"=>"Data Kosong","Sopir"=>"0","Faktur"=>"0","ShipTo"=>"0","Perusahaan"=>"0","Alamat"=>"0","Brg"=>"0","Hint"=>"0","Jml"=>"0","Rasio"=>"0","HrgSatuan"=>"0");}
}
else{$resultcabang[]=array("Tgl"=>"1999-11-19 00:00:00","Kodenota"=>"Koneksi DB Terputus","Sopir"=>"0","Faktur"=>"0","ShipTo"=>"0","Perusahaan"=>"0","Alamat"=>"0","Brg"=>"0","Hint"=>"0","Jml"=>"0","Rasio"=>"0","HrgSatuan"=>"0");}
echo json_encode(array('deliverydata'=>$resultcabang));