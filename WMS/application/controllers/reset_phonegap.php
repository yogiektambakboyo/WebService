<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Reset2
 *
 * @author USER
 */
class Reset_phonegap extends CI_Controller {

    //put your code here

    function __construct() {
        parent::__construct();
        $this->load->model('reset_model');
    }

    function bin($kodebin) {
        $cekbin=  $this->cek_bin($kodebin);
        $cekbinpakai=  $this->cek_pakai($kodebin);
        if(!$cekbin['error'] || !$cekbinpakai['error'])
        {
            $strerror='';
            $strerror=$cekbin['msgerror'].' '.$cekbinpakai['msgerror'];
            $arr = array('error' => FALSE, 'msgerror' => $strerror);
            echo $_GET['jsoncallback'] . '(' . json_encode($arr) . ');';
        }
        else
        {
            $this->reset_model->updateIsUsed($kodebin);
            $arr = array('error' => TRUE, 'msgerror' =>'');
            echo $_GET['jsoncallback'] . '(' . json_encode($arr) . ');';
        }
        
    }

    function cek_bin($str) {
        if ($this->reset_model->isKodeBin($str)) { //cek kode bin atau bukan
            $result = array('error' => TRUE, 'msgerror' => '');
            return $result;
        } else {
            $result = array('error' => FALSE, 'msgerror' => 'Kode Bin Tidak valid');
            return $result;
        }
    }

    function cek_pakai($str) {
        if ($this->reset_model->isBolehKodeBin($str)) { //cek kode bin sedang dipakai atau tidak
            $result = array('error' => TRUE, 'msgerror' => '');
            return $result;
        } else {
            $result = array('error' => FALSE, 'msgerror' => 'Bin Tidak Dapat Direset');
                return $result;
        }
    }

}

?>
