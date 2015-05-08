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
class Bpb extends CI_Controller {

    //put your code here
    function __construct() {
        parent::__construct();
        $this->load->model('bpb_model');
        if (!$this->session->userdata('OperatorCode') || $this->session->userdata('outstanding') || $this->session->userdata("OperatorRole") != '10/WHR/001') {
            redirect(base_url() . "index.php/login");
        }
    }

    function tes() {
        $x = $this->bpb_model->tes();
        foreach ($x as $row) {
            echo $row;
        }

        $y = get_object_vars($x);
        echo "<pre>";
        print_r($x);
        print_r($y);
        echo "</pre>";
    }

    function step1() { //untuk input kode nota
        //hapus session proses sebelumnya
        $this->session->unset_userdata('transactionCode');
        $this->session->unset_userdata('bpb');
        $this->session->unset_userdata('namaSupplier');
        if ($this->input->post('btnProses')) {
            $this->form_validation->set_rules('transactionCode', 'No BPB', 'required');
            $this->form_validation->set_error_delimiters('<div class="alert alert-error"><h5>', '</h5></div>');
            if ($this->form_validation->run() == FALSE) {
                $data['bpb'] = $this->bpb_model->getTodayBPBList();
                $this->load->view('bpb/step1_view', $data);
            } else {
                $transactionCode = $this->input->post('transactionCode');
                $detailBPB = $this->bpb_model->getDetailKodeNota($transactionCode);
                $this->session->set_userdata('transactionCode', $detailBPB['transactionCode']);
                $this->session->set_userdata('bpb', $detailBPB['kodeNota']);
                $this->session->set_userdata('namaSupplier', $detailBPB['perusahaan']);
                $this->session->set_userdata('keterangan', $detailBPB['keterangan']);
                $this->session->set_flashdata('step2', TRUE);
                redirect(base_url() . "index.php/bpb/step2");
            }
        } else {
            $data['bpb'] = $this->bpb_model->getTodayBPBList($this->session->userdata('OperatorCode'));
            $this->load->view('bpb/step1_view', $data);
        }
    }

    function step2() { //input barcode pallet, sku, jumlah, dll
        if ($this->input->post('btnTambah')) {
            $this->form_validation->set_rules('kodeSKU', 'Kode SKU', 'required|callback_cekSku');
            $this->form_validation->set_rules('namaSKU', 'Nama SKU', 'required');
            $this->form_validation->set_rules('jumlahSKU', 'Jumlah SKU', 'required|integer|callback_cekJumlahBrg');
            $this->form_validation->set_rules('edSKU', 'ED SKU', 'required');
            //$this->form_validation->set_rules('palet', 'Palet', 'required|integer|callback_cekBin');
            $this->form_validation->set_rules('palet', 'Palet', 'required|integer|callback_cekBin');
            $this->form_validation->set_rules('rackslot', 'RackSlot', 'required|integer|callback_cekRackSlot');
            $this->form_validation->set_error_delimiters('<div class="alert alert-error"><h5>', '</h5></div>');
            if ($this->form_validation->run() == FALSE) {
                $TransactionCode = $this->session->userdata('transactionCode');
                $kodeBarang = $this->input->post('kodeSKU');
                $rasio = $this->bpb_model->getRasioBarang($TransactionCode, $kodeBarang);
                if (count($rasio) > 0) {
                    $data['rasio'] = $rasio;
                }
                $data['barang'] = $this->bpb_model->getDetailBarang($kodeBarang);
                $this->load->view('bpb/step2_view', $data);
            } else {
                $TransactionCode = $this->session->userdata('transactionCode');
                $BinCode = $this->input->post('palet');
                $SKUCode = $this->input->post('kodeSKU');
                $ExpDate = $this->input->post('edSKU');
                $ratio = $this->input->post('ratio');
                $Qty = $this->input->post('jumlahSKU') * $ratio;
                $CurrRackSlot = $this->input->post('rackslot');
                $DestRackSlot = $CurrRackSlot; //////////////////////MASIH BERUBAH
                //insert database

                if ($this->bpb_model->setDetailTransaction($TransactionCode, $BinCode, $SKUCode, $ExpDate, $Qty, $CurrRackSlot, $DestRackSlot,$this->session->userdata('ReceiveSource'),$this->session->userdata('OperatorCode'))) {
                    
                    $data['barang']=  $this->bpb_model->getlastbrg($TransactionCode,$SKUCode);
                    if(count($data['barang'])>0){
                        $data['pesan']='Detail transaksi berhasil ditambahkan.';
                        $data['brgnow']=true;
                        $this->load->view('bpb/step2_view', $data);
                    }
                    else{
                        $this->session->set_flashdata('step2', TRUE);
                        $this->session->set_flashdata('pesan', 'Detail transaksi berhasil ditambahkan.'); 
                        redirect(base_url() . "index.php/bpb/step2");
                    }
                } else {
                    $this->session->set_flashdata('step2', TRUE);
                    $this->session->set_flashdata('error', 'Detail transaksi tidak berhasil ditambahkan.');
                    redirect(base_url() . "index.php/bpb/step2");
                }
            }
        }  elseif ($this->session->flashdata('step2')) { //harus dari step 1 atau dari button tambah
            $TransactionCode = $this->session->userdata('transactionCode');
            $OperatorCode = $this->session->userdata('OperatorCode');
            $role = $this->session->userdata('OperatorRole');
            if (!$this->bpb_model->isDetailTransactionOpr($TransactionCode, $OperatorCode, $role)) {
                $this->bpb_model->setDetailTransactionOpr($TransactionCode, $OperatorCode, $role);
            }
            else{
                $this->bpb_model->updateDetailTransactionOpr($TransactionCode, $OperatorCode, $role);
            }
            $this->load->view('bpb/step2_view');
        } elseif ($this->session->flashdata('kodeBarang')) { //dari button cari
            $kodeBarang = $this->session->flashdata('kodeBarang');
            $transactionCode = $this->session->userdata('transactionCode');
            $rasio = $this->bpb_model->getRasioBarang($transactionCode, $kodeBarang);
            if (count($rasio) > 0) {
                $data['rasio'] = $rasio;
            }
            $data['barang'] = $this->bpb_model->getDetailBarang($kodeBarang);
            $this->load->view('bpb/step2_view', $data);
        } else { //jika mencoba langsung tembak url
            redirect(base_url());
        }
    }

    function cari_barang($keyword='') {
        if ($this->input->post('btnCari')) {
            $cari = $this->input->post('cari');
            $kodeNota = $this->session->userdata('transactionCode');
            $data['barang'] = $this->bpb_model->cari_barang($kodeNota, $cari);
            $this->load->view('bpb/cari_barang_view', $data);
        } elseif ($this->input->post('btnPilih')) {
            $this->form_validation->set_rules('barang', 'Kode Barang', 'required|callback_cekBarang');
            $this->form_validation->set_error_delimiters('<div class="alert alert-error"><h5>', '</h5></div>');
            if ($this->form_validation->run() == FALSE) {
                $this->load->view('bpb/cari_barang_view');
            } else {
                $kodeBarang = $this->input->post('barang');
                $this->session->set_flashdata('kodeBarang', $kodeBarang);
                redirect(base_url() . "index.php/bpb/step2");
            }
        } else {
            $data['keyword']=$keyword;
            $this->load->view('bpb/cari_barang_view',$data);
        }
    }

    function cekJumlahBrg($Qty){
        $SKUCode = $this->input->post('kodeSKU');
        $ratio = $this->input->post('ratio');
        $TransactionCode = $this->session->userdata('transactionCode');
        $Qty = $Qty * $ratio;
        $QtyDERP=$this->bpb_model->getQtyDERP($TransactionCode,$SKUCode);
        $QtyRcv=$this->bpb_model->getQtyRcv($TransactionCode,$SKUCode,$this->session->userdata('ReceiveSource'));
        if($Qty<=0){
           $this->form_validation->set_message('cekJumlahBrg', 'Barang Tidak Boleh <=0');
            return FALSE; 
        }
        else if($QtyRcv+$Qty>$QtyDERP){
            $lebih=  $this->bpb_model->getQtyKonversi($SKUCode,($QtyRcv+$Qty)-$QtyDERP);
            $this->form_validation->set_message('cekJumlahBrg', 'Barang Kelebihan sebanyak '.$lebih);
            return FALSE;
        }
        return TRUE;
    }
    function cekBarang($str) {
        $kodeNota = $this->session->userdata('bpb');
        if ($this->bpb_model->isKodeOrBarcodeBarang($kodeNota, $str)) {
            return TRUE;
        } else {
            $this->form_validation->set_message('cekBarang', '%s tidak valid atau tidak terdaftar pada Nota ini.');
            return FALSE;
        }
    }

    function cekSku($str) {
        if ($this->bpb_model->isBarang($str,$this->session->userdata('transactionCode'))) {
            return TRUE;
        } else {
            $this->form_validation->set_message('cekSku', '%s tidak valid.');
            return FALSE;
        }
    }

    function cekBin($str) {
        if ($this->bpb_model->isUsedBin($str)) {
            return TRUE;
        } else {
            $this->form_validation->set_message('cekBin', '%s tidak valid atau Palet sedang digunakan.');
            return FALSE;
        }
    }

    function cekRackSlot($str) {
        if ($this->bpb_model->isRackSlot($str)) {
            return TRUE;
        } else {
            $this->form_validation->set_message('cekRackSlot', '%s tidak valid.');
            return FALSE;
        }
    }
    function listbrg()
    {
        if(!$this->session->userdata('transactionCode'))
        {
            $TransactionCode=$this->session->userdata('TransactionCode');
        }
        else {
            $TransactionCode=$this->session->userdata('transactionCode');
        }
        $data['list']=  $this->bpb_model->listbrg($TransactionCode,$this->session->userdata('OperatorCode'));
        $this->load->view('bpb/List_SKU_view',$data);
    }
    function backstep2()
    {
        $this->load->view('bpb/step2_view');
    }
    
    function get_ajax_EDlama() {
        //ajax untuk menampilkan informasi SKU barang yang harus dipilih
        $SKUCode = $_POST['SKUCode'];
        $EDinput = $_POST['EDinput'];
        
        if(!$this->session->userdata('transactionCode'))
        {
            $TransactionCode=$this->session->userdata('TransactionCode');
        }
        else {
            $TransactionCode=$this->session->userdata('transactionCode');
        }
        $edSKULama = $this->bpb_model->getEDLama($TransactionCode, $SKUCode);
        if ($edSKULama == NULL) {
                $edSKULama=$this->bpb_model->getEDdefault($SKUCode,$EDinput);
            }
            else{
                if($EDinput!=''){
                    $edSKUBaru=$this->bpb_model->getEDdefault($SKUCode,$EDinput);
                    if($edSKULama!=$edSKUBaru){
                        $edSKULama=$this->bpb_model->getEDdefault($SKUCode,$EDinput);
                    }
                }
            }
        $arr= array("EDlama" => $edSKULama);
      
        echo $_GET['jsoncallback'] . '(' . json_encode($arr) . ');';
    }
}

?>
