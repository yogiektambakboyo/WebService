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
                $this->session->set_flashdata('step2', TRUE);
                redirect(base_url() . "index.php/bpb/step2");
            }
        } else {
            $data['bpb'] = $this->bpb_model->getTodayBPBList();
            $this->load->view('bpb/step1_view', $data);
        }
    }

    function step2() { //input barcode pallet, sku, jumlah, dll
        if ($this->input->post('btnTambah')) {
            $this->form_validation->set_rules('palet', 'Palet', 'required|integer|callback_cekBin');
            //$this->form_validation->set_rules('palet', 'Palet', 'required|integer');
            $this->form_validation->set_rules('rackslot', 'RackSlot', 'required|integer|callback_cekRackSlot');
            $this->form_validation->set_error_delimiters('<div class="alert alert-error"><h5>', '</h5></div>');
            if ($this->form_validation->run() == FALSE) {
                $this->load->view('bpb/step2_view');
            } else {
                $BinCode = $this->input->post('palet');
                $RackSlot = $this->input->post('rackslot');
                $this->session->set_userdata('binCode', $BinCode);
                $this->session->set_userdata('rackSlot', $RackSlot);
                $this->session->set_flashdata('step3', TRUE);
                redirect(base_url() . "index.php/bpb/step3");
            }
        } elseif ($this->session->flashdata('step2')) { //harus dari step 1
            $TransactionCode = $this->session->userdata('transactionCode');
            $OperatorCode = $this->session->userdata('OperatorCode');
            $role = $this->session->userdata('OperatorRole');
            if (!$this->bpb_model->isDetailTransactionOpr($TransactionCode, $OperatorCode, $role)) {
                $this->bpb_model->setDetailTransactionOpr($TransactionCode, $OperatorCode, $role);
            }
            $this->load->view('bpb/step2_view');
        } else { //jika mencoba langsung tembak url
            redirect(base_url());
        }
    }

    function step3() { //input barcode pallet, sku, jumlah, dll
        if ($this->input->post('btnTambah')) {
            $this->form_validation->set_rules('kodeSKU', 'Kode SKU', 'required|callback_cekSku');
            $this->form_validation->set_rules('namaSKU', 'Nama SKU', 'required');
            $this->form_validation->set_rules('jumlahSKU', 'Jumlah SKU', 'required|integer');
            $this->form_validation->set_rules('edSKU', 'ED SKU', 'required');
            $this->form_validation->set_error_delimiters('<div class="alert alert-error"><h5>', '</h5></div>');
            if ($this->form_validation->run() == FALSE) {
                $this->load->view('bpb/step3_view');
            } else {
                $TransactionCode = $this->session->userdata('transactionCode');
                $BinCode = $this->session->userdata('binCode');
                $SKUCode = $this->input->post('kodeSKU');
                $ExpDate = $this->input->post('edSKU');
                $ratio = $this->input->post('ratio');
                $Qty = $this->input->post('jumlahSKU') * $ratio;
                $CurrRackSlot = $this->session->userdata('rackSlot');
                $DestRackSlot = $CurrRackSlot;
                //insert database
                if ($this->bpb_model->setDetailTransaction($TransactionCode, $BinCode, $SKUCode, $ExpDate, $Qty, $CurrRackSlot, $DestRackSlot)) {
                    $this->session->set_flashdata('step3', TRUE);
                    $this->session->set_flashdata('pesan', 'Detail transaksi berhasil ditambahkan.');
                    redirect(base_url() . "index.php/bpb/step3");
                } else {
                    $this->session->set_flashdata('step3', TRUE);
                    $this->session->set_flashdata('error', 'Detail transaksi tidak berhasil ditambahkan.');
                    redirect(base_url() . "index.php/bpb/step3");
                }
            }
        } elseif ($this->input->post('btnSet')) {
            $this->form_validation->set_rules('sku', 'Kode Barang / Barcode', 'required|callback_cekBarang');
            $this->form_validation->set_error_delimiters('<div class="alert alert-error"><h5>', '</h5></div>');
            if ($this->form_validation->run() == FALSE) {
                $this->load->view('bpb/step3_view');
            } else {
                $kodeBarang = $this->input->post('sku');
                $transactionCode = $this->session->userdata('transactionCode');
                $edSKULama = $this->bpb_model->getEDLama($transactionCode, $kodeBarang);
                if ($edSKULama != NULL) {
                    $data['edSKULama'] = $edSKULama;
                }
                $rasio = $this->bpb_model->getRasioBarang($transactionCode, $kodeBarang);
                if (count($rasio) > 0) {
                    $data['rasio'] = $rasio;
                }
                $data['barang'] = $this->bpb_model->getDetailBarang($kodeBarang);
                $this->load->view('bpb/step3_view', $data);
            }
        } elseif ($this->session->flashdata('step3')) { //harus dari step 2 atau dari button tambah
            $this->load->view('bpb/step3_view');
        } elseif ($this->session->flashdata('kodeBarang')) { //dari button cari
            $kodeBarang = $this->session->flashdata('kodeBarang');
            $transactionCode = $this->session->userdata('transactionCode');
            $edSKULama = $this->bpb_model->getEDLama($transactionCode, $kodeBarang);
            if ($edSKULama != NULL) {
                $data['edSKULama'] = $edSKULama;
            }
            $rasio = $this->bpb_model->getRasioBarang($transactionCode, $kodeBarang);
            if (count($rasio) > 0) {
                $data['rasio'] = $rasio;
            }
            $data['barang'] = $this->bpb_model->getDetailBarang($kodeBarang);
            $this->load->view('bpb/step3_view', $data);
        } else { //jika mencoba langsung tembak url
            redirect(base_url());
        }
    }

    function cari_barang() {
        if ($this->input->post('btnCari')) {
            $cari = $this->input->post('cari');
            $kodeNota = $this->session->userdata('bpb');
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
                redirect(base_url() . "index.php/bpb/step3");
            }
        } else {
            $this->load->view('bpb/cari_barang_view');
        }
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
        if ($this->bpb_model->isBarang($str)) {
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
            $this->form_validation->set_message('cekBin', '%s tidak valid atau sedang dipakai.');
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

}

?>
