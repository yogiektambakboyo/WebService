<?php

class login_phonegap extends CI_Controller {

    //put your code here
    function __construct() {
        parent::__construct();
        $this->load->model("login_model");
        $this->load->model('main_model');
    }

    function ceklogin($UserName, $Password) {
        $UserName=  str_replace('.', '/', $UserName);
        $Password=  str_replace('.', '/', $Password);
        if ($this->login_model->getLogin($UserName, $Password)) {   
            $login = $this->login_model->getDataLogin($UserName, $Password);
            $arr = array('error' => false, 'outstanding' => false,
                    'OperatorCode' => $login['OperatorCode'], 'OperatorName' => $login['name'], 'OperatorSiteId' => $login['siteId']);
            echo $_GET['jsoncallback'] . '(' . json_encode($arr) . ');';
        } else {
            $arr = array('error' => true, 'msgerror' => 'Username atau Password Salah');
            echo $_GET['jsoncallback'] . '(' . json_encode($arr) . ');';
        }
    }
    
    function refreshhome($OperatorCode)
    {
        $OperatorCode=  str_replace('.', '/', $OperatorCode);
        if ($this->login_model->cekOutstanding($OperatorCode)) {//cek apakah login operator masih mempunyai outstanding
                $Role = $this->login_model->getRole($OperatorCode);
                $arr = array('error' => false, 'Outstanding' => false,
                     'Role' => $Role);
                echo $_GET['jsoncallback'] . '(' . json_encode($arr) . ');';
            } else {
                $MenuOutstanding = $this->login_model->getOutstanding($OperatorCode);
                $arr = array('error' => false, 'Outstanding' => true,
                     'MenuOutstanding' => $MenuOutstanding);
                echo $_GET['jsoncallback'] . '(' . json_encode($arr) . ');';
            }
    }

    function listmaintask($WHRoleCode)
    {
        $WHRoleCode=  str_replace('.', '/', $WHRoleCode);
        $linkaddress=  $this->main_model->getlink($WHRoleCode);
        echo $_GET['jsoncallback'] . '(' . json_encode($linkaddress) . ');';
    }


}

?>
