<?php

class replenish_manual extends CI_Controller {

    function __construct() {
        parent::__construct();
        if (!$this->session->userdata('OperatorCode')) {
            redirect(base_url() . 'index.php/login');
        }
        $this->load->model('replenish_manual_model');
    }

    function index() {
        if ($this->session->userdata('outstanding')) {
            $data = $this->replenish_manual_model->getTransactionCodeTask($this->session->userdata('OperatorCode'));
            if ($data['status']) {
                $TransactionCode = $data['TransactionCode'];
                if ($this->replenish_manual_model->getJmlListTask($TransactionCode) > 0) {
                    $data['task'] = $this->replenish_manual_model->getListTask($TransactionCode);
                    $data['jumlahtask'] = $this->replenish_manual_model->getJmlListTask($TransactionCode);
                    $data['TransactionCode'] = $TransactionCode;
                    $this->load->view('replenish_manual/list_task_view', $data);
                } else {
                    $data['TransactionCode'] = $TransactionCode;
                    $this->load->view('replenish_manual/input_brg', $data);
                }
            } else {
                redirect(base_url() . 'index.php/login');
            }
        } else {
            $this->buat_task();
        }
    }

    function buat_task() {
        $TransactionCode = $this->replenish_manual_model->getTransactionCode();
        $this->replenish_manual_model->setMasterTaskRpl($TransactionCode, $this->session->userdata('OperatorCode'));
        $this->replenish_manual_model->setDetailTransactionReplenishOpr($TransactionCode, $this->session->userdata('OperatorCode'), $this->session->userdata('OperatorRole'));
        $this->session->set_userdata('outstanding', true);
        $data['TransactionCode'] = $TransactionCode;
        $this->load->view('replenish_manual/input_brg', $data);
    }

    function add_replenish() {
        $this->form_validation->set_rules('bincode', 'Kode Bin', 'required');
        $this->form_validation->set_rules('rackcode', 'Kode Rack', 'required');
        $this->form_validation->set_rules('bintemp', 'Kode Bin Temp', 'required|callback_cekBin');
        $this->form_validation->set_rules('kodeSKU', 'Barang', 'required');
        $this->form_validation->set_rules('whcode', 'Kode Gudang', 'required|callback_cekGudang');
        $this->form_validation->set_rules('jumlahSKU', 'Jumlah', 'required|integer|callback_cekQty');
        $this->form_validation->set_error_delimiters('<div class="alert alert-error"><h5>', '</h5></div>');
        if ($this->form_validation->run() == TRUE) { //masukkan ke dalam cart
            $bincode = $this->input->post('bincode');
            $rackcode = $this->input->post('rackcode');
            $bintemp = $this->input->post('bintemp');
            $kodeSKU = $this->input->post('kodeSKU');
            $namaSKU = $this->input->post('namaSKU');
            $QtyAwal = $this->input->post('QtyAwal');
            $QtyAwalKonversi = $this->input->post('QtyAwalKonversi');
            $jumlahSKU = $this->input->post('jumlahSKU');
            $rasio = $this->input->post('rasio');
            $expdate = $this->input->post('expdate');
            $whcode = $this->input->post('whcode');
            $replenish = array(
                'id' => $bincode,
                'qty' => $jumlahSKU,
                'price' => 1,
                'name' => $namaSKU,
                'options' => array('rackcode' => $rackcode, 'bintemp' => $bintemp,
                    'kodeSKU' => $kodeSKU, 'QtyAwal' => $QtyAwal, 'QtyAwalKonversi' => $QtyAwalKonversi,
                    'jmlSKU' => ($jumlahSKU * $rasio), 'expdate' => date('d-m-Y', strtotime($expdate)), 'whcode' => $whcode,
                    'rasio' => $rasio, 'jmlSKUkonversi' => $this->replenish_manual_model->konversisatuan($kodeSKU, $jumlahSKU * $rasio))
            );
            $this->cart->insert($replenish);
            $data = array();
            $data['status'] = TRUE; //mengirimkan status TRUE ke view
            header('Content-Type: application/json', true);
            echo json_encode($data);
        } else { //ada kesalahan, sehingga mengembalikan pesan error ke view
            $data = array();
            $data['status'] = FALSE;
            $data['error'] = validation_errors();
            header('Content-Type: application/json', true);
            echo json_encode($data);
        }
    }

    function cekBin($str) {
        if($str==$this->input->post('bincode')){
            return true;
        }
        else{
            if ($this->replenish_manual_model->isUsedBin($str)) {
                if (count($this->cart->contents()) == 0) {
                    return true;
                } else {
                    $status = true;
                    foreach ($this->cart->contents() as $item) {
                        if ($item['options']['bintemp'] == $str) {
                            $status = FALSE;
                            break;
                        }
                    }
                    if ($status) {
                        return true;
                    } else {
                        $this->form_validation->set_message('cekBin', '%s tidak valid atau %s sedang digunakan.');
                        return FALSE;
                    }
                }
            }
            $this->form_validation->set_message('cekBin', '%s tidak valid atau %s sedang digunakan.');
            return FALSE;
        }
    }

    function cekQty($Qty) {
        $QtyAwal = $this->input->post('QtyAwal');
        $rasio=$this->input->post('rasio');
        if ($Qty*$rasio > 0 && $Qty*$rasio <= $QtyAwal) {
            return TRUE;
        }
        $this->form_validation->set_message('cekQty', 'Jumlah Salah');
        return FALSE;
    }

    function cekGudang($WHCode) {
        if (count($this->cart->contents()) == 0) {
            return TRUE;
        } else {
            foreach ($this->cart->contents() as $item) {
                $gudang = $item['options']['whcode'];
                break;
            }
            if ($gudang == $WHCode) {
                return TRUE;
            } else {
                return FALSE;
            }
        }
    }

    function get_ajax_bin() {
        $RackSlotCode = $_POST['rackcode'];

        $result = $this->replenish_manual_model->getbin($RackSlotCode);
        $arr = array();
        foreach ($result as $row) {
            $arr[] = array('BinCode' => $row['BinCode']);
        }
        echo $_GET['jsoncallback'] . '(' . json_encode($arr) . ');';
    }

    function get_ajax_sku() {
        $BinCode = $_POST['bincode'];

        $result = $this->replenish_manual_model->getsku($BinCode);
        $arr = array('BinCode' => $result['BinCode'], 'Keterangan' => $result['Keterangan'],
            'Qty' => $result['Qty'], 'QtyKonversi' => $result['QtyKonversi'],
            'SKUCode' => $result['SKUCode'], 'ExpDate' => $result['ExpDate'], 'WHCode' => $result['WHCode']);
        echo $_GET['jsoncallback'] . '(' . json_encode($arr) . ');';
    }

    function get_ajax_satuan() {
        $SKUCode = $_POST['SKUCode'];
        $result = $this->replenish_manual_model->getsatuan($SKUCode);

        $arr = array();
        foreach ($result as $row) {
            $arr[] = array("Rasio" => $row['Rasio'], "Satuan" => $row['Satuan']);
        }
        echo $_GET['jsoncallback'] . '(' . json_encode($arr) . ');';
    }

    function list_replenish() {
        $this->load->view('replenish_manual/list_replenish');
    }

    function delete_replenish() {
        $rowid = $this->input->post('rowid');
        $replenish = array(
            'rowid' => $rowid,
            'qty' => 0
        );
        $this->cart->update($replenish);
    }

    function edit_replenish() {
        $this->form_validation->set_rules('bincode', 'Kode Bin', 'required');
        $this->form_validation->set_rules('rackcode', 'Kode Rack', 'required');
        $this->form_validation->set_rules('bintemp', 'Kode Bin Temp', 'required|callback_cekBin');
        $this->form_validation->set_rules('kodeSKU', 'Barang', 'required');
        $this->form_validation->set_rules('whcode', 'Kode Gudang', 'required|callback_cekGudang');
        $this->form_validation->set_rules('jumlahSKU', 'Jumlah', 'required|integer|callback_cekQty');
        $this->form_validation->set_error_delimiters('<div class="alert alert-error"><h5>', '</h5></div>');
        if ($this->form_validation->run() == TRUE) {
            $rowid = $this->input->post('rowid');
            $bincode = $this->input->post('bincode');
            $rackcode = $this->input->post('rackcode');
            $bintemp = $this->input->post('bintemp');
            $kodeSKU = $this->input->post('kodeSKU');
            $namaSKU = $this->input->post('namaSKU');
            $QtyAwal = $this->input->post('QtyAwal');
            $expdate = $this->input->post('expdate');
            $whcode = $this->input->post('whcode');
            $QtyAwalKonversi = $this->input->post('QtyAwalKonversi');
            $jumlahSKU = $this->input->post('jumlahSKU');
            $rasio = $this->input->post('rasio');
            $retur = array(
                'rowid' => $rowid,
                'qty' => 0
            );
            $this->cart->update($retur);


            $replenish = array(
                'id' => $bincode,
                'qty' => $jumlahSKU,
                'price' => 1,
                'name' => $namaSKU,
                'options' => array('rackcode' => $rackcode, 'bintemp' => $bintemp,
                    'jmlSKU' => $jumlahSKU * $rasio, 'expdate' => $expdate, 'whcode' => $whcode,
                    'kodeSKU' => $kodeSKU, 'QtyAwal' => $QtyAwal, 'QtyAwalKonversi' => $QtyAwalKonversi, 'rasio' => $rasio, 'jmlSKUkonversi' => $this->replenish_manual_model->konversisatuan($kodeSKU, $jumlahSKU * $rasio))
            );
            $this->cart->insert($replenish);
            $data = array();
            $data['status'] = TRUE;
            header('Content-Type: application/json', true);
            echo json_encode($data);
        } else {
            $data = array();
            $data['status'] = FALSE;
            $data['error'] = validation_errors();
            header('Content-Type: application/json', true);
            echo json_encode($data);
        }
    }

    function finish_replenish($TransactionCode) {
        $TransactionCode = base64_decode(str_replace('-', '=', str_replace('_', '/', $TransactionCode)));
        
        if ($this->replenish_manual_model->setFinish($TransactionCode)) {
            $this->session->set_flashdata('pesan', 'Proses Berhasil Ditutup');
            redirect(base_url() . "/index.php/replenish_manual");
        }
        $this->session->set_flashdata('error', 'Proses Gagal Ditutup');
        redirect(base_url() . "/index.php/replenish_manual");
    }

    function save_replenish($TransactionCode) {
        $TransactionCode = base64_decode(str_replace('-', '=', str_replace('_', '/', $TransactionCode)));
        if (count($this->cart->contents()) > 0) {
            $status = true;
            $WHCode='';
            foreach ($this->cart->contents() as $item) {
                //insert database
                $WHCode=$item['options']['whcode'];
                break;
            }
            $this->replenish_manual_model->updateHWCode($TransactionCode,$WHCode);
            foreach ($this->cart->contents() as $item) {
                if (!$this->replenish_manual_model->setdetailtask($TransactionCode, $item['id'], $item['options']['kodeSKU'], $item['options']['jmlSKU'], date('Y/m/d', strtotime($item['options']['expdate'])), $item['options']['rackcode'], $this->session->userdata('OperatorCode'), $item['options']['bintemp'])) {
                    $status = FALSE;
                    
                    break;
                }
            }
            if ($status == TRUE) {
                $this->cart->destroy();
                $this->session->set_flashdata('pesan', 'Data berhasil ditambahkan.');
                $data['task'] = $this->replenish_manual_model->getListTask($TransactionCode);
                $data['jumlahtask'] = $this->replenish_manual_model->getJmlListTask($TransactionCode);
                $data['TransactionCode'] = $TransactionCode;
                $this->load->view('replenish_manual/list_task_view', $data);
            } else {
                $data['TransactionCode'] = $TransactionCode;
                $data['error'] = 'Gagal menyimpan Task';
                $this->load->view('replenish_manual/input_brg', $data);
            }
        }
    }

    function list_task($TransactionCode) {
        $TransactionCode = base64_decode(str_replace('-', '=', str_replace('_', '/', $TransactionCode)));
        $data['task'] = $this->replenish_manual_model->getListTask($TransactionCode);
        $data['jumlahtask'] = $this->replenish_manual_model->getJmlListTask($TransactionCode);
        $data['TransactionCode'] = $TransactionCode;
        $this->load->view('replenish_manual/list_task_view', $data);
    }

    function taruh_bin() {
        if ($this->input->post('btnProses')) {
            $this->form_validation->set_rules('kodebin', 'Kode Bin', 'required|callback_cekBin2');
            $this->form_validation->set_rules('koderack', 'Kode Rack', 'required|callback_cekRack');
            $this->form_validation->set_rules('kodebindest', 'Kode Bin Tujuan', 'required|callback_cekBinDest');
            $this->form_validation->set_rules('jumlahSKU', 'Jumlah', 'required|integer|callback_cekQty');
            $this->form_validation->set_error_delimiters('<div class="alert alert-error"><h5>', '</h5></div>');
            if (!$this->form_validation->run()) {
                $data['TransactionCode'] = $this->input->post('TransactionCode');
                $data['QueueNumber'] = $this->input->post('QueueNumber');
                $data['Keterangan'] = $this->input->post('Keterangan');
                $data['BinCode'] = $this->input->post('BinCode');
                $data['Qty'] = $this->input->post('QtyAwal');
                $data['SKUCode'] = $this->input->post('SKUCode');
                $data['NoUrut'] = $this->input->post('NoUrut');
                $data['SrcRackSlot'] = $this->input->post('SrcRackSlot');
                $data['ExpDate'] = $this->input->post('ExpDate');
                $data['RackName'] = $this->input->post('RackName');
                $data['QtyKonversi'] = $this->input->post('QtyKonversi');
                $this->load->view('replenish_manual/taruh_bin_view', $data);
            } else {
                $TransactionCode = $this->input->post('TransactionCode');
                $QueueNumber = $this->input->post('QueueNumber');
                $NoUrut = $this->input->post('NoUrut');
                $DestRackSlot = $this->input->post('koderack');
                $DestBin = $this->input->post('kodebindest');
                $DestQty = $this->input->post('jumlahSKU') * $this->input->post('rasio');
                if ($this->replenish_manual_model->setDetailTaskRplHistory($TransactionCode, $NoUrut, $QueueNumber, $DestRackSlot, $DestBin, $DestQty, $this->session->userdata('OperatorCode'))) {
                    
                    $data['task'] = $this->replenish_manual_model->getListTask($TransactionCode);
                    $data['jumlahtask'] = $this->replenish_manual_model->getJmlListTask($TransactionCode);
                    $data['TransactionCode'] = $TransactionCode;
                    $this->load->view('replenish_manual/list_task_view', $data);
                } else {
                    $data['TransactionCode'] = $this->input->post('TransactionCode');
                    $data['QueueNumber'] = $this->input->post('QueueNumber');
                    $data['Keterangan'] = $this->input->post('Keterangan');
                    $data['BinCode'] = $this->input->post('BinCode');
                    $data['Qty'] = $this->input->post('QtyAwal');
                    $data['SKUCode'] = $this->input->post('SKUCode');
                    $data['NoUrut'] = $this->input->post('NoUrut');
                    $data['SrcRackSlot'] = $this->input->post('SrcRackSlot');
                    $data['ExpDate'] = $this->input->post('ExpDate');
                    $data['RackName'] = $this->input->post('RackName');
                    $data['QtyKonversi'] = $this->input->post('QtyKonversi');
                    $this->load->view('replenish_manual/taruh_bin_view', $data);
                }
            }
        } else {
            $data['TransactionCode'] = $this->input->post('TransactionCode');
            $data['QueueNumber'] = $this->input->post('QueueNumber');
            $data['Keterangan'] = $this->input->post('Keterangan');
            $data['BinCode'] = $this->input->post('BinCode');
            $data['Qty'] = $this->input->post('Qty');
            $data['SKUCode'] = $this->input->post('SKUCode');
            $data['NoUrut'] = $this->input->post('NoUrut');
            $data['SrcRackSlot'] = $this->input->post('SrcRackSlot');
            $data['ExpDate'] = $this->input->post('ExpDate');
            $data['RackName'] = $this->input->post('RackName');
            $data['QtyKonversi'] = $this->input->post('QtyKonversi');
            $this->load->view('replenish_manual/taruh_bin_view', $data);
        }
    }

    function cekBin2($str) {
        if ($str == $this->input->post('BinCode')) {
            return TRUE;
        }
        $this->form_validation->set_message('cekBin2', 'Bin Tidak Sesuai');
        return FALSE;
    }

    function cekBinDest($str) {
        if ($str == $this->input->post('BinCode')) {
            return TRUE;
        } else {
            if ($this->replenish_manual_model->cekBarang($str, $this->input->post('SKUCode'))) {
                return TRUE;
            }
        }
        $this->form_validation->set_message('cekBinDest', 'Barang Tidak Sama');
        return FALSE;
    }

    function cekRack($str) {
        if ($str == $this->input->post('SrcRackSlot')) {
            return true;
        } else {
            if ($this->replenish_manual_model->cekRackSlotNull($str)) {
                return TRUE;
            }
        }
        $this->form_validation->set_message('cekRack', 'Rack Tidak Kosong');
        return FALSE;
    }

    function get_ajax_rack() {
        //ajax untuk menampilkan informasi SKU barang yang harus dipilih
        $RackSlotCode = $_POST['RackSlotCode'];
        $result = $this->replenish_manual_model->getRackName($RackSlotCode);

        $arr = array("RackName" => $result['RackName']);
        echo $_GET['jsoncallback'] . '(' . json_encode($arr) . ');';
    }

}

?>
