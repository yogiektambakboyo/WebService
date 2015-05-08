<?php
include "setting/include.php";$cabang="01";$kodebin=$_GET["kodebin"];$koderack=$_GET["koderack"];$jumlah=0;
$db=new DB();$resultccabang=array();$koneksi=$db->connectDB($cabang);

if($koneksi["status"]){
    $sql = "select count(*) as jumlah from wms.RackSlot where RackSlotCode='".$koderack."' and RackType in ('G','H','S')";
    $result=$db->queryDB($sql);
    if($result["jumdata"]>0){
        while ($row = mssql_fetch_assoc($result["result"])) {$jumlah=$row["jumlah"];}
        if($jumlah>0){
            $sql = "SELECT COUNT(*) AS jumlah
                    from wms.Bin b
                    inner join wms.BinSKU s
                    on b.BinCode=s.BinCode
                    where b.RackSlotCode=".$koderack." AND b.IsOnAisle=0 AND s.Qty>0";
            $result=$db->queryDB($sql);
            if($result["jumdata"]>0){
                $jumlah=0;
                while($row = mssql_fetch_assoc($result["result"])){
                    $jumlah=$row["jumlah"];
                }
                if($jumlah>0){
                    $sql = "SELECT MultipleBin FROM wms.RackSlot WHERE RackSlotCode='".$koderack."'";
                    $result=$db->queryDB($sql);
                    if($result["jumdata"]>0){
                        $multiplebin=0;
                        while ($row = mssql_fetch_assoc($result["result"])) {
                            $multiplebin=$row["MultipleBin"];
                        }
                        if ($multiplebin != 0) {
                            $resultcabang[]=array("Status"=>"1");
                        } else {
                            //cek apakah bin yang menempati rak merupakan bin yang akan dimasukkan
                            $sql = "SELECT BinCode from wms.Bin where RackSlotCode='".$koderack."' AND IsOnAisle=0";
                            $result=$db->queryDB($sql);

                            if($result["jumdata"]>0){
                                $bin=0;
                                while ($row = mssql_fetch_assoc($result["result"])) {
                                    $bin=$row["BinCode"];
                                }

                                if ($bin == $kodebin) {
                                    $resultcabang[]=array("Status"=>"1");
                                } else {
                                    $resultcabang[]=array("Status"=>"0");
                                }
                            }else{
                                $resultcabang[]=array("Status"=>"0");
                            }
                        }

                    }else{
                        $resultcabang[]=array("Status"=>"0");
                    }
                }else{
                    $resultcabang[]=array("Status"=>"1");
                }

            }else{
                $resultcabang[]=array("Status"=>"0");
            }
        }else{
            $resultcabang[]=array("Status"=>"1");
        }

    }else{
        $resultcabang[]=array("Status"=>"0");
    }
}
else{
    $resultcabang[]=array("Status"=>"Koneksi DB Terputus");
}
echo json_encode(array('operatortaskreceivebpb2cekrak'=>$resultcabang));