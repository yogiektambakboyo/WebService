<?php
include '../include/include.php';
$cabang=$_GET["cabang"];
$username=$_GET["username"];
$codename=$_GET["codename"];
$db=new DB();
$resultcabang=array();
$koneksi=$db->connectDB($cabang);

if($koneksi["status"]){
    $sql="SELECT COUNT(1) AS jumlah,nama FROM SalesPerson WHERE REPLACE(Kode,'/','')='".$username."' GROUP BY nama";
    
    $result=$db->queryDB($sql);
    $arr=array();
    $jumlah=0;
    $nama=null;
    
    if($result["jumdata"]>0){
        
        while ($row = mssql_fetch_assoc($result["result"])) {
            $jumlah=$row["jumlah"];
            $nama=$row["nama"];
            $resultcabang[]=$row;
        }
        if($jumlah>0){
            $arr=array("status"=>1,"data"=>$nama);
        }
        else {
            $arr=array("status"=>0,"data"=>$nama);
        }
    }else{
        $arr=array("status"=>0,"data"=>"Username/Password Salah");
    }
}
else{
    $arr=array("status"=>0,"data"=>$koneksi["data"]);
}
if($codename==1988){
    echo json_encode(array('logindata'=>$resultcabang));
}else{
    echo $_GET['jsoncallback'] . '(' . json_encode($arr) . ');';
}

