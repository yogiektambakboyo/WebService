<?php
include '../setting/include.php';
$cabang=$_GET["cabang"];$db=new DB();$koneksi=$db->connectDB($cabang);$arr=array();

if($koneksi["status"]){
    //select pelanggan
    $sql="select kode,perusahaan,alamat,ISNULL(penghubung,'') as penghubung,alamat,kota,ISNULL(telp,'') as telp,segment,ISNULL(kodepos,'') as kodepos,kecamatan,kelurahan,longitude,latitude,ISNULL(nohp,0) as nohp from pelanggan where left(kode,2)='".$cabang."' and aktif=1 and moq=1";
    $result1=$db->queryDB($sql);

    if($result1["jumdata"]==0){$arr=array("status"=>0,"data"=>"Data Barang Kosong");}else{
        $name="../sqlite/MASTER_".$cabang;
        $sqlite= new SQLite3($name);
        $sqlite->exec('DROP TABLE IF EXISTS Pelanggan');
        $sqlite->exec('CREATE TABLE IF NOT EXISTS Pelanggan(kode TEXT PRIMARY KEY,perusahaan TEXT,alamat TEXT,penghubung TEXT,kota TEXT,telp TEXT,segment TEXT,kodepos TEXT,kecamatan,kelurahan TEXT,longitude TEXT,latitude TEXT,nohp INT)');
        $status1=true;

        while ($row = mssql_fetch_assoc($result1["result"])) {
            $sqlite->exec("INSERT INTO Pelanggan VALUES ('".str_replace("'", "",$row['kode'])."','".str_replace("'", "",$row['perusahaan'])."','".str_replace("'", "",$row['alamat'])."','".str_replace("'","",$row['penghubung'])."','".str_replace("'","",$row['kota'])."','".str_replace("'","",$row['telp'])."','".str_replace("'","",$row['segment'])."','".str_replace("'","",$row['kodepos'])."','".str_replace("'","",$row['kecamatan'])."','".str_replace("'","",$row['kelurahan'])."','".str_replace("'","",$row['longitude'])."','".str_replace("'","",$row['latitude'])."',".$row['nohp'].")");
            if($sqlite->lastErrorMsg()!="not an error"){$status1=false;break;}
        }
        if($status1){$arr[]=array("status"=>1,"data"=>"Data Bisa Download");}
        else{$arr[]=array("status"=>0,"data"=>"Pelanggan ".$sqlite->lastErrorMsg());}
    }

}else{
    $arr[]=array("status"=>0,"data"=>$koneksi["data"]);
}
 echo json_encode($arr);