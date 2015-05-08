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
    $str1 = substr($username,0,2);
    $str2 = substr($username,2,2);
    $str3 = substr($username,4,4);
    $finalstr = $str1."/".$str2."/".$str3;
    $finalstr2 = $str1."/".$str2."%";

    //select pelanggan
    $sql="select kode,perusahaan,alamat from pelanggan where replace(sales,'/','')='".$username."'";
    $result1=$db->queryDB($sql);
    //select barang
    $sql="select b.Kode,b.Keterangan as Nama,b.Hint as Keterangan,bm.Keterangan as Merek,bv.Keterangan as Variant,s.CRT,ISNULL(dbo.hrgjualbarang(b.kode,getdate(),1,'',''),0) Harga
            from barang b,brgmerek bm,brgvariant bv,(select brg,max(rasio) CRT from satuan where satuanaktif=1 group by brg) s
            where left(b.kode,2)=substring('".$username."',3,2) and b.aktif=1 and bm.kode=b.merek and bv.kode=b.variant
            and s.brg=b.kode and len(b.Hint)>5";
    $result2=$db->queryDB($sql);
    //select fjp
    $sql="select distinct b.Shipto,b.Hari from (select f.shipto,max(tgl) Tgl from pelanggan p,bcp_custfjp f where p.sales='".$finalstr."' and f.shipto=p.kode and tgl<=getdate() group by f.shipto) a,bcp_custfjp_detail b where a.shipto=b.shipto and a.tgl=b.tgl order by b.Shipto";
    $result3=$db->queryDB($sql);
    // select historytransaksi
    $sql="select distinct p.Kode,d.Brg from masterjual m,detailjual d,pelanggan p where p.sales='".$finalstr."' and m.kodenota like '".$finalstr2."' and m.shipto=p.kode and m.tgl between getdate()-90 and getdate() and d.kodenota=m.kodenota and d.jml>0";
    $result4=$db->queryDB($sql);

    //Select minOder
    $koneksi=$db->connectDB("00");

    $sql="select dbo.getMinOrder('".$finalstr."') as minimOrder";

    $minOrder = "0";

    if($result2["jumdata"]==0){
        $arr=array("status"=>0,"data"=>"Data Barang Kosong");
    }else{
        $name="../sqlite/MASTER_".$username;
        $sqlite= new SQLite3($name);
        $sqlite->exec('DROP TABLE IF EXISTS Pelanggan');
        $sqlite->exec('DROP TABLE IF EXISTS Barang');
        $sqlite->exec('DROP TABLE IF EXISTS FJP');
        $sqlite->exec('DROP TABLE IF EXISTS HistoryTransaksi');
        $sqlite->exec('CREATE TABLE IF NOT EXISTS Pelanggan(kode TEXT PRIMARY KEY,perusahaan TEXT,alamat TEXT)');
        $sqlite->exec('CREATE TABLE IF NOT EXISTS Barang(kode TEXT PRIMARY KEY,nama TEXT,keterangan TEXT,merek TEXT,variant TEXT,crt INT,harga FLOAT)');
        $sqlite->exec('CREATE TABLE IF NOT EXISTS FJP(kode TEXT,hari TEXT)');
        $sqlite->exec('CREATE TABLE IF NOT EXISTS HistoryTransaksi(kode TEXT,brg TEXT)');
        $status1=true;
        $status2=true;
        $status3=true;
        $status4=true;
        while ($row = mssql_fetch_assoc($result1["result"])) {

            $sqlite->exec("INSERT INTO Pelanggan VALUES ('".str_replace("'", "",$row['kode'])."','".str_replace("'", "",$row['perusahaan'])."','".str_replace("'", "",$row['alamat'])."')");
            if($sqlite->lastErrorMsg()!="not an error"){
                $status1=false;
                break;
            }

        }
        if($status1){
            while ($row = mssql_fetch_assoc($result2["result"])) {
                $sqlite->exec("INSERT INTO Barang VALUES ('".str_replace("'", "",$row['Kode'])."','".str_replace("'", "",$row['Nama'])."','".str_replace("'", "",$row['Keterangan'])."','".str_replace("'", "",$row['Merek'])."','".str_replace("'", "",$row['Variant'])."',".str_replace("'", "",$row['CRT']).",".str_replace("'", "",$row['Harga']).")");
                //echo "INSERT INTO Barang VALUES ('".str_replace("'", "",$row['Kode'])."','".str_replace("'", "",$row['Nama'])."','".str_replace("'", "",$row['Keterangan'])."','".str_replace("'", "",$row['Merek'])."','".str_replace("'", "",$row['Variant'])."',".str_replace("'", "",$row['CRT']).",".str_replace("'", "",$row['Harga']).")"."<br>";
                if($sqlite->lastErrorMsg()!="not an error"){
                    $status2=false;
                    break;
                }
            }
        }
        else{
            $arr=array("status"=>0,"data"=>"Pelanggan ".$sqlite->lastErrorMsg());
        }

        if($status2){
            while ($row = mssql_fetch_assoc($result3["result"])) {

                $sqlite->exec("INSERT INTO FJP VALUES ('".str_replace("'", "",$row['Shipto'])."','".str_replace("'", "",$row['Hari'])."')");
                if($sqlite->lastErrorMsg()!="not an error"){
                    $status3=false;
                    break;
                }
            }
        }else{
            $arr=array("status"=>0,"data"=>"Barang ".$sqlite->lastErrorMsg());
        }

        if($status3){
            while ($row = mssql_fetch_assoc($result4["result"])) {

                $sqlite->exec("INSERT INTO HistoryTransaksi VALUES ('".str_replace("'", "",$row['Kode'])."','".str_replace("'", "",$row['Brg'])."')");
                if($sqlite->lastErrorMsg()!="not an error"){
                    $status4=false;
                    break;
                }
            }
        }else{
            $arr=array("status"=>0,"data"=>"FJP ".$sqlite->lastErrorMsg());
        }

        if($status4){
            $result5=$db->queryDB($sql);
            while($rows = mssql_fetch_assoc($result5["result"])){
                $minOrder = (string)$rows['minimOrder'];
            }
            $arr=array("status"=>1,"data"=>"Data Bisa Download","minorder"=>$minOrder);
        }else{
            $arr=array("status"=>0,"data"=>"FJP ".$sqlite->lastErrorMsg());
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
