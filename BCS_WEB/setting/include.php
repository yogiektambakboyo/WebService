<?php
class DB{
    private $server;
    public function connectDB($cabang){
        if($cabang=="00"){$link="192.168.31.4";$user="sa";$pass="8is4";$DB="BCP";}
        if($cabang=="01"){$link="192.168.31.4";$user="sa";$pass="8is4";$DB="BCP";}
		if($cabang=="02"){$link="192.168.31.4";$user="sa";$pass="8is4";$DB="BCP_MLG";}
        if($cabang=="03"){$link="192.168.33.8";$user="sa";$pass="bananaleaf";$DB="BCP";}
        if($cabang=="04"){$link="192.168.31.4";$user="sa";$pass="8is4";$DB="BCP_KDR";}
        if($cabang=="05"){$link="192.168.35.8";$user="sa";$pass="denpasarsejuk";$DB="BCP";}
		if($cabang=="06"){$link="192.168.37.8";$user="sa";$pass="borwita";$DB="BCPMND";}
        if($cabang=="07"){$link="192.168.36.8";$user="sa";$pass="m4k4sarbcp";$DB="BCPMKS";}
        if($cabang=="08"){$link="192.168.38.8";$user="sa";$pass="pisanggoreng";$DB="BCP";}
        if($cabang=="09"){$link="192.168.44.8";$user="sa";$pass="matsunichi";$DB="BCP";}
        if($cabang=="10"){$link="192.168.39.8";$user="sa";$pass="borwita";$DB="BCP";}
        if($cabang=="11"){$link="192.168.31.4";$user="sa";$pass="8is4";$DB="BCP_MDR";}
        if($cabang=="12"){$link="192.168.41.8";$user="sa";$pass="borwita";$DB="BCP";}
        if($cabang=="13"){$link="192.168.45.8";$user="sa";$pass="goldenleaf";$DB="BCPKDI";}
        if($cabang=="14"){$link="192.168.43.2";$user="sa";$pass="goldenleaf";$DB="BCP";}
        if($cabang=="15"){$link="192.168.31.4";$user="sa";$pass="8is4";$DB="BCP_LTB";}
        if($cabang=="16"){$link="192.168.31.4";$user="sa";$pass="8is4";$DB="BCP";}
		if($cabang=="17"){$link="192.168.46.8";$user="sa";$pass="b0rw1t4";$DB="BCP";}
        if($cabang=="18"){$link="192.168.31.4";$user="sa";$pass="8is4";$DB="BCP_MDN";}
        if($cabang=="19"){$link="192.168.48.8";$user="sa";$pass="kurakuraninja";$DB="BCP";}
        if($cabang=="20"){$link="192.168.42.8";$user="sa";$pass="b0rw1t4";$DB="BCP";}
        if($cabang=="21"){$link="192.168.50.8";$user="sa";$pass="warungkopi";$DB="BCP";}
        if($cabang=="22"){$link="192.168.51.8";$user="sa";$pass="b0rw1t4";$DB="BCP";}
        if($cabang=="23"){$link="192.168.52.8";$user="sa";$pass="pohonkelapa";$DB="BCP";}
        if($cabang=="24"){$link="192.168.53.8";$user="sa";$pass="merahkuninghijau";$DB="BCP";}
        if($cabang=="25"){$link="192.168.31.4";$user="sa";$pass="8is4";$DB="BCP";}
        if($cabang=="30"){$link="192.168.31.4";$user="sa";$pass="8is4";$DB="OrderEntry";}
        $this->server = mssql_connect($link, $user, $pass);
        if (!$this->server) {
            $data["status"]=false;
            $data["data"]="Koneksi Server Gagal";
        }else{
            $selectDB = mssql_select_db($DB, $this->server);
            if(!$selectDB){$data["status"]=false;$data["data"]="Koneksi DB Gagal";}
            else{$data["status"]=true;}
        }
        return $data;
    }
    public function queryDB($sql){
        $result = mssql_query($sql);
        $data["jumdata"] = mssql_num_rows($result);
        $data["result"]=$result;
        return $data;
    }
    public function executeDB($sql){
        $result = mssql_query($sql);
        return $result;
    }
    function __destruct() {mssql_close($this->server);}
}
