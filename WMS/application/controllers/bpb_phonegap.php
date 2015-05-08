<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of receiving
 *
 * @author USER
 */
class Bpb_phonegap extends CI_Controller {

    //put your code here
    function __construct() {
        parent::__construct();
        $this->load->model('bpb_model');
    }

    function getlistbpb() {
        $bpblist = $this->bpb_model->getTodayBPBList();
        echo $_GET['jsoncallback'] . '(' . json_encode($bpblist) . ');';
    }

    function setsku($barcode, $kodeNota, $TransactionCode) {
        $$barcode = str_replace('.', '/', $$barcode);
        $kodeNota = str_replace('.', '/', $kodeNota);
        $TransactionCode = str_replace('.', '/', $TransactionCode);
        if ($this->cekBarang($barcode, $kodeNota) == false) {
            $str = '';
            if ($this->cekBarang($barcode, $kodeNota) == false) {
                $str.='Barcode Barang Salah';
            }
            $arr = array('error' => true, 'msgerror' => $str);
            echo $_GET['jsoncallback'] . '(' . json_encode($arr) . ');';
        } else {
            $barang = $this->bpb_model->getDetailBarang($barcode); //dapatkan kode SKU dari barcode
            $edSKULama = $this->bpb_model->getEDLama($TransactionCode, $barang['kode']);
            if ($edSKULama == NULL) {
                $edSKULama = '';
            }
            $rasio = $this->bpb_model->getRasioBarang($TransactionCode, $barang['kode']);
            $arr = array('error' => false, 'edSKU' => $edSKULama, 'rasioN' => $rasio['ratioName'],
                'rasioV' => $rasio['ratio'], 'codeSKU' => $barang['kode'], 'nameSKU' => $barang['keterangan']);
            echo $_GET['jsoncallback'] . '(' . json_encode($arr) . ');';
        }
    }

    function cekBarang($str, $kodeNota) {
        if ($this->bpb_model->isKodeOrBarcodeBarang($kodeNota, $str)) {
            return TRUE;
        } else {
            $this->form_validation->set_message('cekBarang', '%s tidak valid atau tidak terdaftar pada Nota ini.');
            return FALSE;
        }
    }

    function setDetailTaskOpr($TransactionCode, $OperatorCode, $role) {
        $TransactionCode = str_replace('.', '/', $TransactionCode);
        $OperatorCode = str_replace('.', '/', $OperatorCode);
        $role = str_replace('.', '/', $role);
        if (!$this->bpb_model->isDetailTransactionOpr($TransactionCode, $OperatorCode, $role)) {
            $this->bpb_model->setDetailTransactionOpr($TransactionCode, $OperatorCode, $role);
        }
        echo $_GET['jsoncallback'] . '(' . json_encode(true) . ');';
    }

    function caribarangrcv($keyword, $transactioncode) {
        $transactioncode = str_replace('.', '/', $transactioncode);
        $keyword = str_replace('.', '/', $keyword);
        if ($keyword == 'keywordkosong') {
            $keyword = '';
        }
        $barang = $this->bpb_model->cari_barang($transactioncode, $keyword);
        echo $_GET['jsoncallback'] . '(' . json_encode($barang) . ');';
    }

    function setDetailTaskRcv($TransactionCode, $BinCode, $SKUCode, $ExpDate, $ratio, $Qty, $CurrRackSlot) {
        $TransactionCode = str_replace('.', '/', $TransactionCode);
        $BinCode = str_replace('.', '/', $BinCode);
        $SKUCode = str_replace('.', '/', $SKUCode);
        $ExpDate = str_replace('.', '/', $ExpDate);
        $CurrRackSlot = str_replace('.', '/', $CurrRackSlot);

        //melakukan cek 
        $cekBinCode = $this->cekBin($BinCode);
        $cekSKUCode = $this->cekSku($SKUCode);
        $cekRackSlot = $this->cekRackSlot($CurrRackSlot);
        $cekQty = $this->cekQty($Qty);

        if (!$cekBinCode['error']  || !$cekRackSlot['error'] || !$cekSKUCode['error'] || !$cekQty['error']) {
            $strerror='';
            if(!$cekBinCode['error'])
            {
                $strerror.=$cekBinCode['msgerror'].' ';
            }
            if(!$cekRackSlot['error'])
            {
                $strerror.=$cekRackSlot['msgerror'] .' ';
            }
            if(!$cekSKUCode['error'])
            {
                $strerror.=$cekSKUCode['msgerror'] .' ';
            }
            if(!$cekQty['error'])
            {
                $strerror.=$cekQty['msgerror'].' ';
            }
            $arr = array('error' => FALSE, 'msgerror' => $strerror);
            echo $_GET['jsoncallback'] . '(' . json_encode($arr) . ');';
        } else {
            if ($this->bpb_model->setDetailTransaction($TransactionCode, $BinCode, $SKUCode, $ExpDate, $Qty * $ratio, $CurrRackSlot, $CurrRackSlot)) {
                $arr = array('error' => TRUE, 'msgerror' => '');
                echo $_GET['jsoncallback'] . '(' . json_encode($arr) . ');';
            } else {
                $arr = array('error' => FALSE, 'msgerror' => 'Input Detail Task Rcv Gagal');
                echo $_GET['jsoncallback'] . '(' . json_encode($arr) . ');';
            }
        }
    }

    function cekQty($str) {
        
            if (is_numeric($str)) {
                $result = array('error' => TRUE, 'msgerror' => '');
                return $result;
            } else {
                $result = array('error' => FALSE, 'msgerror' => 'Jumlah Harus Bilangan');
                return $result;
            }
        
    }

    function cekSku($str) {

            if ($this->bpb_model->isBarang($str)) {
                $result = array('error' => TRUE, 'msgerror' => '');
                return $result;
            } else {
                $result = array('error' => FALSE, 'msgerror' => 'Kode SKU Tidak Valid');
                return $result;
            }
        
    }

    function cekRackSlot($str) {
        
            if ($this->bpb_model->isRackSlot($str)) {
                $result = array('error' => TRUE, 'msgerror' => '');
                return $result;
            } else {
                $result = array('error' => FALSE, 'msgerror' => 'Kode Rack Tidak Valid');
                return $result;
            }
        
    }

    function cekBin($str) {

        if ($this->bpb_model->isUsedBin($str)) {
            $result = array('error' => TRUE, 'msgerror' => '');
            return $result;
        } else {
            $result = array('error' => FALSE, 'msgerror' => 'Kode Bin Tidak Valid/Sedang Dipakai');
            return $result;
        }
    }

}

?>
