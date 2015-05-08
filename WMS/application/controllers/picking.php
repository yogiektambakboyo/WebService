<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

class Picking extends CI_Controller {

    function __construct() {
        parent::__construct();

        //$this->set_session();
        //periksa apakah sudah login atau belum
        if (!$this->session->userdata('OperatorCode')) {
            redirect(base_url() . "index.php/login");
        }
        $this->load->model('picking_model');
        $this->load->model('login_model');
    }

    function index() {
        $this->tambah_picklist();
    }

    function tambah_picklist() {
        $outstanding = $this->login_model->getOutstanding($this->session->userdata("OperatorCode"));
        if (!$this->session->userdata('outstanding') && $outstanding['statusPCK'] == true && $this->session->userdata("OperatorRole") == '10/WHR/003') {

            //melakukan pemilihan masterTaskPck Pick List yang akan operator proses
            if ($this->input->post('btnTambahPickList')) {
                $this->form_validation->set_rules('picklist', 'No PickList', 'required');
                $this->form_validation->set_error_delimiters('<div class="alert alert-error"><h5>', '</h5></div>');
                if ($this->form_validation->run() == FALSE) {
                    $data['picklist'] = $this->picking_model->getPickList();
                    $this->load->view('picking/tambah_picklist_view', $data);
                } else {
                    $temp = $this->input->post('picklist');
                    $temp2 = explode("~", $temp);
                    $picklist = $temp2[0];
                    $ERPCode = $temp2[1];
                    $WHCode = $temp2[2];
                    $EDPanjang = $temp2[3];

                    $outstanding = $this->login_model->getOutstanding($this->session->userdata("OperatorCode"));
                    if ($outstanding['statusPCK'] == true) {
                        $this->picking_model->setDetailTransactionOpr($picklist, $this->session->userdata('OperatorCode'), $this->session->userdata('OperatorRole'));
                        $this->session->set_userdata('picklistchoose', $picklist);
                        $this->session->set_userdata('ERPCode', $ERPCode);
                        $this->session->set_userdata('WHCodepick', $WHCode);
                        $this->session->set_userdata('EDPanjang', $EDPanjang);
                        $data['outstanding'] = $this->picking_model->getListDetailTaskPck($picklist);

                        $this->load->view('picking/daftar_all_outstanding_view', $data);
                    } else {
                        $data['error']='Selesaikan Dahulu Outstanding Picking Anda';
                        $data['picklist'] = $this->picking_model->getPickList($this->session->userdata('OperatorCode'));
                        $this->load->view('picking/tambah_picklist_view', $data);
                    }
                }
            } else {
                $data['picklist'] = $this->picking_model->getPickList($this->session->userdata('OperatorCode'));
                $this->load->view('picking/tambah_picklist_view', $data);
            }
        } else {
            redirect(base_url() . "index.php/login");
        }
    }

    function proses_pilih_bin() {
        //pengecekan bin yg dibawa untuk picking
        if (!$this->session->userdata("outstanding") /* && $this->session->userdata("OperatorRole") == '10/WHR/003' */) {
            if ($this->input->post('btnProses')) {
                $this->form_validation->set_rules('kodebin', 'Kode Bin', 'required|callback_cek_kodebinexist|callback_cek_kodebinfull');
                $this->form_validation->set_error_delimiters('<div class="alert alert-error"><h5>', '</h5></div>');
                if ($this->form_validation->run() == FALSE) {
                    $this->load->view('picking/pilih_bin_view');
                } else {
                    $this->session->set_userdata('BinNow', $this->input->post('kodebin'));
                    $this->refresh_all_outstanding();
                }
            } else {
                $this->load->view('picking/pilih_bin_view');
            }
        } else {
            redirect(base_url() . "index.php/login");
        }
    }

    function ambil_barang() {
        /* if (!$this->session->userdata("outstanding") && $this->session->userdata("OperatorRole") == '10/WHR/003') { */
        //pengiriman data menuju form ambil barang
        if ($this->input->post('TransactionCode')) {

            if ($this->session->userdata('BinNow')) {//palet penampung barang picking sekarang
                //cek apakah BinNow sekarang sedang dipakai untuk transaksi lain
                $BinNow = $this->session->userdata('BinNow');
                $OperatorCode = $this->session->userdata('OperatorCode');
                $data['TransactionCode'] = $this->input->post('TransactionCode');
                if ($this->picking_model->cekBinPenampungTransaksi($data['TransactionCode'], $BinNow, $OperatorCode)) {//cek apakah bin sedang dipakai untuk task picklist lain
                    $data['Qty'] = $this->input->post('Qty');
                    $data['Konversi'] = $this->input->post('Konversi');
                    $data['SKUCode'] = $this->input->post('SKUCode');
                    $data['Keterangan'] = $this->input->post('Keterangan');
                    $data['Needed'] = $this->input->post('Needed');
                    $data['AddTask'] = $this->input->post('AddTask');
                    $data['NoUrut'] = $this->input->post('NoUrut');
                    $data['satuan'] = $this->picking_model->getsatuan($data['SKUCode']);
                    $this->load->view('picking/proses_ambil_barang_view', $data);
                } else {
                    $data['error'] = 'Bin Telah Dipakai Untuk PickList Lain';
                    $data['outstanding'] = $this->picking_model->getListDetailTaskPck($this->session->userdata('picklistchoose'));
                    $this->load->view('picking/daftar_all_outstanding_view', $data);
                }
            } else {
                $data['error'] = 'Pilih Bin Penampung Dahulu';
                $data['outstanding'] = $this->picking_model->getListDetailTaskPck($this->session->userdata('picklistchoose'));
                $this->load->view('picking/daftar_all_outstanding_view', $data);
            }
        }
        /* } else {
          redirect(base_url() . "index.php/login");
          } */
    }

    function proses_ambil_barang() {
        /* if (!$this->session->userdata("outstanding") && $this->session->userdata("OperatorRole") == '10/WHR/003') { */
        //pengecekan dan update user untuk barang yang diambil
        if ($this->input->post('btnProses')) {
            $this->form_validation->set_rules('kodebin', 'Kode Bin', 'required|callback_cek_kodebinexist|callback_cek_kodebinSKU|callback_cek_kodebinrack');
            $this->form_validation->set_rules('koderack', 'Kode Bin', 'required|callback_cek_koderackexist');
            $this->form_validation->set_rules('jumlah', 'Jumlah', 'required|numeric|callback_cek_qtymax|callback_cek_qtymaxNeeded');
            $this->form_validation->set_error_delimiters('<div class="alert alert-error"><h5>', '</h5></div>');
            if ($this->form_validation->run() == FALSE) {
                $data['TransactionCode'] = $this->input->post('TransactionCode');
                $data['Qty'] = $this->input->post('Qty');
                $data['Konversi'] = $this->input->post('Konversi');
                $data['SKUCode'] = $this->input->post('SKUCode');
                $data['Keterangan'] = $this->input->post('Keterangan');
                $data['Needed'] = $this->input->post('Needed');
                $data['AddTask'] = $this->input->post('AddTask');
                $data['NoUrut'] = $this->input->post('NoUrut');
                $data['satuan'] = $this->picking_model->getsatuan($data['SKUCode']);
                $this->load->view('picking/proses_ambil_barang_view', $data);
            } else {
                $TransactionCode = $this->input->post('TransactionCode');
                $SKUCode = $this->input->post('SKUCode');
                $kodebin = $this->input->post('kodebin');
                $koderack = $this->input->post('koderack');
                $jumlah = $this->input->post('jumlah');
                $rasio = $this->input->post('satuan');
                $AddTask = $this->input->post('AddTask');
                $NoUrut = $this->input->post('NoUrut');
                $kodebinbawa = $this->session->userdata('BinNow');
                $Qty = $this->input->post('Qty');
                $Needed = $this->input->post('Needed');
                //if ($this->picking_model->cekUser_1st($TransactionCode, $NoUrut) == true && $this->picking_model->cekUser_2nd($TransactionCode, $NoUrut) == true) {
                if ($this->picking_model->setambilbarang($TransactionCode, $SKUCode, $kodebin, $kodebinbawa, $koderack, $jumlah * $rasio, $this->session->userdata('OperatorCode'), $Needed, $Qty, $AddTask, $NoUrut)) {
                    //$this->session->set_userdata('outstanding',TRUE);
                    $this->refresh_all_outstanding();
                } else {
                    $data['TransactionCode'] = $this->input->post('TransactionCode');
                    $data['Qty'] = $this->input->post('Qty');
                    $data['Konversi'] = $this->input->post('Konversi');
                    $data['SKUCode'] = $this->input->post('SKUCode');
                    $data['Keterangan'] = $this->input->post('Keterangan');
                    $data['Needed'] = $this->input->post('Needed');
                    $data['AddTask'] = $this->input->post('AddTask');
                    $data['NoUrut'] = $this->input->post('NoUrut');
                    $data['satuan'] = $this->picking_model->getsatuan($data['SKUCode']);
                    $data['error'] = 'Input Gagal';
                    $this->load->view('picking/proses_ambil_barang_view', $data);
                }
            }
        }
        /* } else {
          redirect(base_url() . "index.php/login");
          } */
    }

    function list_my_outstanding() {
        $data['outstanding'] = $this->picking_model->getListMyDetailTaskPck($this->session->userdata('OperatorCode'));
        $this->load->view('picking/daftar_my_outstanding_view', $data);
    }

    function taruh_bin() {
        //if($this->session->userdata('BinNow')!='')
        //{
        //$this->load->view('picking/taruh_bin_view');
        /* }
          else
          {
          $data['error']='tentukan bin yang dipakai dahulu';
          $data['outstanding']=$this->picking_model->getListMyDetailTaskPck($this->session->userdata('OperatorCode'));
          $this->load->view('picking/daftar_my_outstanding_view', $data);
          } */
        //proses taruh bin
        if ($this->input->post('btnProses')) {
            $this->form_validation->set_rules('kodebin', 'Kode Bin', 'required|callback_cek_kodebinexist|callback_cek_binuser_1st|callback_cek_exist_kodebin_SKU');
            $this->form_validation->set_rules('koderack', 'Kode Rack', 'required|callback_cek_koderackexist');
            $this->form_validation->set_error_delimiters('<div class="alert alert-error"><h5>', '</h5></div>');
            if ($this->form_validation->run() == FALSE) {
                $this->load->view('picking/taruh_bin_view');
            } else {
                $kodebin = $this->input->post('kodebin');
                $koderack = $this->input->post('koderack');
                if ($this->picking_model->settaruhbin($this->session->userdata('picklistchoose'), $kodebin, $koderack, $this->session->userdata('OperatorCode')) == false) {
                    $data['error'] = 'Gagal Menaruh';
                    $this->load->view('picking/taruh_bin_view', $data);
                } else {
                    //$this->session->unset_userdata('outstanding');
                    $this->session->set_flashdata('pesan',"Bin ".$this->session->userdata('BinNow')." Dengan Transaksi ".$this->session->userdata('picklistchoose')." Berhasil Ditaruh");
                    $this->session->unset_userdata('BinNow');
                    
                    redirect(base_url()."index.php/picking/refresh_all_outstanding");
                    //$this->refresh_all_outstanding();
                }
            }
        } else {
            $this->load->view('picking/taruh_bin_view');
        }
    }

    function cek_exist_kodebin_SKU($kodebin) {
        $OperatorCode = $this->session->userdata('OperatorCode');
        if ($this->picking_model->cekexistkodebinSKU($kodebin, $OperatorCode) == false) {
            $this->form_validation->set_message('cek_exist_kodebin_SKU', 'Bin Masih Kosong!');
            return false;
        }
        return true;
    }

    function cek_binuser_1st($kodebin) {//cek benarkah pembawa bin merupakan penaruh
        //cek apakah sku barang yg diambil benar
        $OperatorCode = $this->session->userdata('OperatorCode');
        if ($this->picking_model->cekbinuser_1st($kodebin, $OperatorCode) == false) {
            $this->form_validation->set_message('cek_binuser_1st', 'Pembawa Bin Salah!');
            return false;
        }
        return true;
    }

    function cek_qtymax($jumlah) {//cek max qty
        //cek apakah sku barang yg diambil benar
        $SKUCode = $this->input->post('SKUCode');
        $kodebin = $this->input->post('kodebin');
        $rasio = $this->input->post('satuan');
        $jumlah*=$rasio;
        if ($this->picking_model->cekQtyBinSKU($kodebin, $SKUCode, $jumlah) == false) {
            $this->form_validation->set_message('cek_qtymax', 'Jumlah Berlebihan!');
            return false;
        }
        return true;
    }

    function cek_qtymaxNeeded($jumlah) {//cek max qty
        //cek apakah sku barang yg diambil benar
        $Qty = $this->input->post('Qty');
        $rasio = $this->input->post('satuan');
        $jumlah*=$rasio;

        if ($jumlah > $Qty) {
            $this->form_validation->set_message('cek_qtymaxNeeded', 'Jumlah Berlebihan!');
            return false;
        }
        return true;
    }

    function cek_kodebinrack($kodebin) {//cek apakah bin ada di rack itu
        //cek apakah sku barang yg diambil benar
        $koderack = $this->input->post('koderack');
        if ($this->picking_model->cekBinRack($kodebin, $koderack) == false) {
            $this->form_validation->set_message('cek_kodebinrack', 'Bin di Rack yang Salah!');
            return false;
        }
        return true;
    }

    function cek_kodebinSKU($kodebin) {//cek apakah sku di bin benar
        //cek apakah sku barang yg diambil benar
        $SKUCode = $this->input->post('SKUCode');
        if ($this->picking_model->cekBinSKU($kodebin, $SKUCode) == false) {
            $this->form_validation->set_message('cek_kodebinSKU', 'SKU Barang Salah!');
            return false;
        }
        return true;
    }

    function cek_kodebinexist($kodebin) {
        //cek apakah bin yang di scan benar-benar ada 
        if ($this->picking_model->cekvalidasiexistkodebin($kodebin) == false) {
            $this->form_validation->set_message('cek_kodebinexist', 'Kode Bin Salah!');
            return false;
        }
        return true;
    }

    function cek_koderackexist($koderack) {
        //cek apakah bin yang di scan benar-benar ada 
        if ($this->picking_model->cekvalidasiexistkoderack($koderack) == false) {
            $this->form_validation->set_message('cek_koderackexist', 'Kode Rack Salah!');
            return false;
        }
        return true;
    }

    function cek_kodebinfull($kodebin) {
        //cek apakah bin yang di scan benar-benar ada 
        if ($this->picking_model->cekBinDestfull($kodebin) == false) {
            $this->form_validation->set_message('cek_kodebinfull', 'Bin Sedang Terisi!');
            return false;
        }
        return true;
    }

    function refresh_all_outstanding() {
        //refresh list outstanding
        $outstanding = $this->login_model->getOutstanding($this->session->userdata("OperatorCode"));
        if ($outstanding['statusPCK'] == false) {
            $temp = $this->picking_model->getTempBinOutstanding($this->session->userdata("OperatorCode"));
            $this->session->set_userdata('BinNow', $temp['DestBin']);
            $this->session->set_userdata('picklistchoose', $temp['TransactionCode']);
        }
        $data['outstanding'] = $this->picking_model->getListDetailTaskPck($this->session->userdata('picklistchoose'));
        if ($outstanding['statusPCK'] == false) {
            $ERPCode=  $this->picking_model->getERPCode($this->session->userdata('picklistchoose'));
            $this->session->set_userdata('ERPCode', $ERPCode['ERPCode']);
        }
        $this->load->view('picking/daftar_all_outstanding_view', $data);
    }

    function get_ajax_suggestion() {
        //ajax untuk menampilkan informasi SKU barang yang harus dipilih
        $SKUCode = $_POST['SKUCode'];
        $Qty = $_POST['Qty'];
        $result = $this->picking_model-> getSuggestion($SKUCode,$Qty,$this->session->userdata('WHCodepick'),$this->session->userdata('EDPanjang'));
        $arr=array();
        foreach ($result as $row)
        {
            $arr[] = array("RackSlotName" => $row['RackSlotName'],"RackSlotLvl" => $row['RackSlotLvl'],"ExpDate" => date("M Y",strtotime($row['ExpDate'])),"QtyText" => $row['QtyText']);
        }
        echo $_GET['jsoncallback'] . '(' . json_encode($arr) . ');';
    }
}

?>
