<?php include_once "../script/connectbon.php";

    try{
        $dbh->beginTransaction();

        $sql="update Inbound_Master set shipto=:shipto where POnumber=:ponumber";
        $prepared=$dbh->prepare($sql);
        $prepared->execute(array(
            "shipto"=>$_POST['shipto'],
            "ponumber"=>$_POST["POnumber"]
        ));


        $dbh->commit();
        echo "Transaksi berhasil di close";
    }catch(Exception $e){
        $dbh->rollback();
        echo json_encode(array("msg"=>$e->getMessage()));
    }


?>