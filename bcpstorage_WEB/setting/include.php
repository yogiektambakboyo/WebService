<?php
class DB{
    private $server;
    public function connectDB(){
         $link="192.168.31.4";
         $user="sa";
         $pass="8is4";
         $DB="BCPSTORAGE";
        $this->server = mssql_connect($link, $user, $pass);
        if (!$this->server) {
            $data["status"]=false;
            $data["data"]="Koneksi Server Gagal";
        }else{
            $selectDB = mssql_select_db($DB, $this->server);
            if(!$selectDB){
                $data["status"]=false;
                $data["data"]="Koneksi DB Gagal";
            }
            else{
                $data["status"]=true;
            }
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
    function __destruct() {
        mssql_close($this->server);
    }
}
?>
