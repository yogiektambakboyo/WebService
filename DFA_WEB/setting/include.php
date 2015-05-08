<?php
class DB{
    private $server;
    public function connectDB($cabang){
        if($cabang=="00"){$link="192.168.31.4";$user="sa";$pass="8is4";$DB="BCP";}
        if($cabang=="01"){$link="192.168.31.4";$user="sa";$pass="8is4";$DB="BCP";}
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


    public function connectDBPDO($cabang){
        if($cabang=="00"){$link="192.168.31.4";$user="sa";$pass="8is4";$DB="BCP2";}
        if($cabang=="01"){$link="192.168.31.4";$user="sa";$pass="8is4";$DB="BCP2";}

        try{
            // Connect to MSSQL linux
            $this->server = new PDO ("dblib:host=".$link.";dbname=".$DB, $user, $pass);//, array(PDO::ATTR_PERSISTENT => true)
            // Connect to MSSQL windows
            //$result = new PDO ("mssql:host=".$server.";dbname=".$database, $user, $password);//, array(PDO::ATTR_PERSISTENT => true)
            $this->server->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $data["status"]=true;
        }
        catch(Exception $e){
            echo "Caught exception: ",  $e->getMessage(), "\n";
            $data["status"]=false;
            $data["data"]="Koneksi Server Gagal";
        }
        return $data;
    }

    public function queryDBPDO($sql){
        try{
            $prepare = $this->server->prepare($sql);
            $prepare->execute();
            $data["result"] = $prepare->fetchAll();
            $data["jumdata"] = count($data["result"]);
        }
        catch(Exception $e){
            echo "Caught exception: ",  $e->getMessage(), "\n";
            $data["jumdata"] = 0;
            $data["result"]= 0;
        }
        return $data;
    }

    public function executeDBPDO($sql){
        try{
            $prepare = $this->server;
            $prepare->beginTransaction();
            $prepare->exec($sql);
            $data["result"] = 1;
            $data["jumdata"] = $prepare->rowCount();
            $prepare->commit();
        }
        catch(Exception $e){
            echo "Caught exception: ",  $e->getMessage(), "\n";
            $data["jumdata"] = 0;
            $data["result"]= 0;
            $prepare->rollBack();
        }
        return $data;
    }


}
