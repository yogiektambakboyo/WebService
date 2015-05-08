<?php
// Test Time Usage
// Script start
$rustart = getrusage();
$time_start = microtime(true);

include '../include/include.php';
$cabang=$_GET["cabang"];
$username=$_GET["username"];
$db=new DB();
$koneksi=$db->connectDB($cabang);
$arr=array();
if($koneksi["status"]){
    //select pelanggan
    $sql="select kode,perusahaan,alamat from pelanggan where replace(sales,'/','')='".$username."'";
    $result1=$db->queryDB($sql);
    //select barang
    $sql="select b.Kode,b.Hint as Keterangan,bm.Keterangan as Merek,bv.Keterangan as Variant,s.CRT,ISNULL(dbo.hrgjualbarang(b.kode,getdate(),1,'',''),0) Harga
            from barang b,brgmerek bm,brgvariant bv,(select brg,max(rasio) CRT from satuan where satuanaktif=1 group by brg) s
            where left(b.kode,2)=substring('".$username."',3,2) and b.aktif=1 and bm.kode=b.merek and bv.kode=b.variant
            and s.brg=b.kode and len(b.Hint)>5";
    $result2=$db->queryDB($sql);


    if($result2["jumdata"]==0){
        $arr=array("status"=>0,"data"=>"Data Barang Kosong");
    }else{
        $name="../sqlite/MASTER_".$username;
        $sqlite= new SQLite3($name);
        $sqlite->exec('DROP TABLE IF EXISTS Pelanggan');
        $sqlite->exec('DROP TABLE IF EXISTS Barang');
        $sqlite->exec('CREATE TABLE IF NOT EXISTS Pelanggan(kode TEXT PRIMARY KEY,perusahaan TEXT,alamat TEXT)');
        $sqlite->exec('CREATE TABLE IF NOT EXISTS Barang(kode TEXT PRIMARY KEY,keterangan TEXT,merek TEXT,variant TEXT,crt INT,harga FLOAT)');
        $status1=true;
        $status2=true;
        while ($row = mssql_fetch_assoc($result1["result"])) {
            $sqlite->exec("INSERT INTO Pelanggan VALUES ('".str_replace("'", "",$row['kode'])."','".str_replace("'", "",$row['perusahaan'])."','".str_replace("'", "",$row['alamat'])."')");
            if($sqlite->lastErrorMsg()!="not an error"){
                $status1=false;
                break;
            }

        }
        if($status1){
            while ($row = mssql_fetch_assoc($result2["result"])) {
                $sqlite->exec("INSERT INTO Barang VALUES ('".str_replace("'", "",$row['Kode'])."','".str_replace("'", "",$row['Keterangan'])."','".str_replace("'", "",$row['Merek'])."','".str_replace("'", "",$row['Variant'])."',".str_replace("'", "",$row['CRT']).",".str_replace("'", "",$row['Harga']).")");
                //echo str_replace("'", "",$row['Kode'])."','".str_replace("'", "",$row['Keterangan'])."','".str_replace("'", "",$row['Merek'])."','".str_replace("'", "",$row['Variant'])."',".str_replace("'", "",$row['CRT']).",".str_replace("'", "",$row['Harga'])."<br>";
                if($sqlite->lastErrorMsg()!="not an error"){
                    $status2=false;
                    break;
                }
            }
            if($status2){
                $arr=array("status"=>1,"data"=>"Data Bisa Download");
            }else{
                $arr=array("status"=>0,"data"=>"Barang ".$sqlite->lastErrorMsg());
            }
        }
        else{
            $arr=array("status"=>0,"data"=>"Pelanggan ".$sqlite->lastErrorMsg());
        }


    }

}else{
    $arr=array("status"=>0,"data"=>$koneksi["data"]);
}
echo $_GET['jsoncallback'] . '(' . json_encode($arr) . ');';


// Test Time
function rutime($ru, $rus, $index) {
    return ($ru["ru_$index.tv_sec"]*1000 + intval($ru["ru_$index.tv_usec"]/1000))
        -  ($rus["ru_$index.tv_sec"]*1000 + intval($rus["ru_$index.tv_usec"]/1000));
}

$ru = getrusage();

echo "<br>";
echo "This process used " . rutime($ru, $rustart, "utime") .
    " ms for its computations\n";
echo "It spent " . rutime($ru, $rustart, "stime") .
    " ms in system calls\n";

// Time Wall Clock
$time_end = microtime(true);

//dividing with 60 will give the execution time in minutes other wise seconds
$execution_time = ($time_end - $time_start)/60;

//execution time of the script
echo "<br>";
echo '<b>Total Execution Time:</b> '.$execution_time.' Mins';
?>